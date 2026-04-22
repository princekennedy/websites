package com.example.myapplication.data

import com.example.myapplication.model.AuthResult
import com.example.myapplication.model.RegisterRequest
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException
import javax.net.ssl.SSLException
import kotlinx.coroutines.CancellationException
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONArray
import org.json.JSONObject

class AuthRepository(
    private val client: OkHttpClient = OkHttpClient(),
) {
    suspend fun registerAndLoadPermissions(
        baseUrl: String,
        request: RegisterRequest,
    ): AuthResult = withContext(Dispatchers.IO) {
        try {
            val normalizedBaseUrl = normalizeBaseUrl(baseUrl)

            if (normalizedBaseUrl.isBlank()) {
                return@withContext AuthResult.Error("Set the backend base URL first.")
            }

            val payload = JSONObject()
                .put("name", request.name)
                .put("email", request.email)
                .put("password", request.password)
                .put("password_confirmation", request.passwordConfirmation)

            val registerRequest = Request.Builder()
                .url("$normalizedBaseUrl/api/auth/register")
                .post(payload.toString().toRequestBody(JSON_MEDIA_TYPE))
                .addHeader("Accept", "application/json")
                .build()

            client.newCall(registerRequest).execute().use { response ->
                val bodyString = response.body?.string().orEmpty()

                if (!response.isSuccessful) {
                    return@withContext AuthResult.Error(extractError(bodyString, response.code))
                }

                val root = bodyString.toJsonObjectOrNull()
                    ?: return@withContext AuthResult.Error("Registration succeeded but the server response could not be read.")
                val token = extractToken(root)
                    ?: return@withContext AuthResult.Error("Registration succeeded but the backend did not return an auth token.")
                val permissionsResult = fetchPermissionsInternal(normalizedBaseUrl, token)

                return@withContext when (permissionsResult) {
                    is PermissionLoadResult.Success -> AuthResult.Success(
                        token = token,
                        permissions = permissionsResult.permissions,
                        warningMessage = null,
                    )

                    is PermissionLoadResult.Error -> AuthResult.Success(
                        token = token,
                        permissions = emptyList(),
                        warningMessage = permissionsResult.message,
                    )
                }
            }
        } catch (exception: Exception) {
            if (exception is CancellationException) {
                throw exception
            }

            return@withContext AuthResult.Error(extractConnectivityError(exception))
        }
    }

    suspend fun fetchPermissions(baseUrl: String, token: String): Result<List<String>> = withContext(Dispatchers.IO) {
        try {
            val normalizedBaseUrl = normalizeBaseUrl(baseUrl)

            if (normalizedBaseUrl.isBlank()) {
                return@withContext Result.failure(IllegalArgumentException("Set the backend base URL first."))
            }

            when (val result = fetchPermissionsInternal(normalizedBaseUrl, token)) {
                is PermissionLoadResult.Success -> Result.success(result.permissions)
                is PermissionLoadResult.Error -> Result.failure(IllegalStateException(result.message))
            }
        } catch (exception: Exception) {
            if (exception is CancellationException) {
                throw exception
            }

            Result.failure(IllegalStateException(extractConnectivityError(exception), exception))
        }
    }

    private fun fetchPermissionsInternal(baseUrl: String, token: String): PermissionLoadResult {
        val permissionsRequest = Request.Builder()
            .url("$baseUrl/api/me/permissions")
            .get()
            .addHeader("Accept", "application/json")
            .addHeader("Authorization", "Bearer $token")
            .build()

        client.newCall(permissionsRequest).execute().use { response ->
            val bodyString = response.body?.string().orEmpty()

            if (!response.isSuccessful) {
                return PermissionLoadResult.Error(extractError(bodyString, response.code))
            }

            val root = bodyString.toJsonObjectOrNull()
                ?: return PermissionLoadResult.Error("Permission response could not be parsed.")

            return PermissionLoadResult.Success(extractPermissions(root))
        }
    }

    private fun extractToken(root: JSONObject): String? {
        val data = root.optJSONObject("data")

        return listOfNotNull(
            root.optStringOrNull("token"),
            root.optStringOrNull("access_token"),
            root.optStringOrNull("plainTextToken"),
            data?.optStringOrNull("token"),
            data?.optStringOrNull("access_token"),
            data?.optStringOrNull("plainTextToken"),
        ).firstOrNull()
    }

    private fun extractPermissions(root: JSONObject): List<String> {
        root.optJSONArray("permissions")?.toStringList()?.takeIf { it.isNotEmpty() }?.let { return it }
        root.optJSONObject("data")?.optJSONArray("permissions")?.toStringList()?.takeIf { it.isNotEmpty() }?.let { return it }
        root.optJSONArray("data")?.toStringList()?.takeIf { it.isNotEmpty() }?.let { return it }

        return emptyList()
    }

    private fun extractError(body: String, statusCode: Int): String {
        val parsed = body.toJsonObjectOrNull()
        val message = parsed?.optStringOrNull("message")
        if (message != null) {
            return message
        }

        val errors = parsed?.optJSONObject("errors")
        if (errors != null) {
            val keys = errors.keys()
            while (keys.hasNext()) {
                val key = keys.next()
                val value = errors.optJSONArray(key)?.optString(0)
                if (!value.isNullOrBlank()) {
                    return value
                }
            }
        }

        return "Request failed with status $statusCode."
    }

    private fun extractConnectivityError(exception: Exception): String = when (exception) {
        is IllegalArgumentException -> "The backend URL is invalid. Check the setting and try again."
        is UnknownHostException, is ConnectException -> "Could not reach the backend. Check the base URL and confirm the Laravel server is running."
        is SocketTimeoutException -> "The backend took too long to respond. Check the server and try again."
        is SSLException -> "A secure connection to the backend could not be established. Check the backend URL and SSL configuration."
        else -> exception.message ?: "Could not reach the backend."
    }

    private fun normalizeBaseUrl(baseUrl: String): String = baseUrl.trim().trimEnd('/')

    private fun String.toJsonObjectOrNull(): JSONObject? = runCatching { JSONObject(this) }.getOrNull()

    private fun JSONObject.optStringOrNull(key: String): String? = optString(key).takeIf { it.isNotBlank() }

    private fun JSONArray.toStringList(): List<String> = buildList {
        for (index in 0 until length()) {
            val value = opt(index)
            when (value) {
                is String -> add(value)
                is JSONObject -> value.optStringOrNull("name")?.let(::add)
            }
        }
    }

    private sealed interface PermissionLoadResult {
        data class Success(val permissions: List<String>) : PermissionLoadResult

        data class Error(val message: String) : PermissionLoadResult
    }

    private companion object {
        val JSON_MEDIA_TYPE = "application/json; charset=utf-8".toMediaType()
    }
}