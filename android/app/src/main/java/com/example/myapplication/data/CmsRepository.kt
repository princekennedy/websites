package com.example.myapplication.data

import com.example.myapplication.model.AppBootstrap
import com.example.myapplication.model.CmsCategory
import com.example.myapplication.model.CmsContent
import com.example.myapplication.model.CmsFaq
import com.example.myapplication.model.CmsMenuItem
import com.example.myapplication.model.CmsQuiz
import com.example.myapplication.model.CmsServiceCenter
import com.example.myapplication.model.PublicSetting
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException
import javax.net.ssl.SSLException
import kotlinx.coroutines.CancellationException
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.OkHttpClient
import okhttp3.Request
import org.json.JSONArray
import org.json.JSONObject

class CmsRepository(
    private val client: OkHttpClient = OkHttpClient(),
) {
    suspend fun fetchBootstrap(baseUrl: String, token: String? = null): Result<AppBootstrap> = withContext(Dispatchers.IO) {
        try {
            val normalizedBaseUrl = baseUrl.trim().trimEnd('/')

            if (normalizedBaseUrl.isBlank()) {
                return@withContext Result.failure(IllegalArgumentException("Set the backend base URL first."))
            }

            val endpoint = if (token.isNullOrBlank()) {
                "$normalizedBaseUrl/api/app/bootstrap"
            } else {
                "$normalizedBaseUrl/api/me/bootstrap"
            }

            val requestBuilder = Request.Builder()
                .url(endpoint)
                .get()
                .addHeader("Accept", "application/json")

            if (!token.isNullOrBlank()) {
                requestBuilder.addHeader("Authorization", "Bearer $token")
            }

            client.newCall(requestBuilder.build()).execute().use { response ->
                val bodyString = response.body?.string().orEmpty()

                if (!response.isSuccessful) {
                    return@withContext Result.failure(IllegalStateException(extractError(bodyString, response.code)))
                }

                val root = runCatching { JSONObject(bodyString) }.getOrNull()
                    ?: return@withContext Result.failure(IllegalStateException("The app bootstrap response could not be parsed."))
                val data = root.optJSONObject("data")
                    ?: return@withContext Result.failure(IllegalStateException("The app bootstrap payload is missing."))

                return@withContext Result.success(
                    AppBootstrap(
                        menuTitle = data.optJSONObject("menu")?.optStringOrNull("name"),
                        menuItems = data.optJSONObject("menu")
                            ?.optJSONArray("items")
                            .toMenuItemList(),
                        categories = data.optJSONArray("categories").toCategoryList(),
                        featuredContents = data.optJSONArray("featured_contents").toContentList(),
                        faqs = data.optJSONArray("faqs").toFaqList(),
                        quizzes = data.optJSONArray("quizzes").toQuizList(),
                        services = data.optJSONArray("services").toServiceList(),
                        settings = data.optJSONArray("settings").toSettingList(),
                    ),
                )
            }
        } catch (exception: Exception) {
            if (exception is CancellationException) {
                throw exception
            }

            Result.failure(IllegalStateException(extractConnectivityError(exception), exception))
        }
    }

    private fun JSONArray?.toMenuItemList(): List<CmsMenuItem> = buildList {
        if (this@toMenuItemList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                CmsMenuItem(
                    title = item.optString("title"),
                    type = item.optString("type"),
                    icon = item.optStringOrNull("icon"),
                    targetReference = item.optStringOrNull("target_reference"),
                    route = item.optStringOrNull("route"),
                    openInWebView = item.optBoolean("open_in_webview"),
                    children = item.optJSONArray("children").toMenuItemList(),
                ),
            )
        }
    }

    private fun JSONArray?.toCategoryList(): List<CmsCategory> = buildList {
        if (this@toCategoryList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                CmsCategory(
                    name = item.optString("name"),
                    description = item.optStringOrNull("description"),
                    contentsCount = item.optInt("contents_count"),
                ),
            )
        }
    }

    private fun JSONArray?.toContentList(): List<CmsContent> = buildList {
        if (this@toContentList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                CmsContent(
                    title = item.optString("title"),
                    summary = item.optStringOrNull("summary"),
                    contentType = item.optString("content_type"),
                    audience = item.optString("audience"),
                    category = item.optStringOrNull("category"),
                ),
            )
        }
    }

    private fun JSONArray?.toFaqList(): List<CmsFaq> = buildList {
        if (this@toFaqList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                CmsFaq(
                    question = item.optString("question"),
                    answer = item.optString("answer"),
                    category = item.optStringOrNull("category"),
                ),
            )
        }
    }

    private fun JSONArray?.toQuizList(): List<CmsQuiz> = buildList {
        if (this@toQuizList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                CmsQuiz(
                    title = item.optString("title"),
                    summary = item.optStringOrNull("summary"),
                    questionsCount = item.optInt("questions_count"),
                    audience = item.optString("audience"),
                ),
            )
        }
    }

    private fun JSONArray?.toServiceList(): List<CmsServiceCenter> = buildList {
        if (this@toServiceList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                CmsServiceCenter(
                    name = item.optString("name"),
                    district = item.optStringOrNull("district"),
                    summary = item.optStringOrNull("summary"),
                    serviceHours = item.optStringOrNull("service_hours"),
                    contactPhone = item.optStringOrNull("contact_phone"),
                    contactEmail = item.optStringOrNull("contact_email"),
                    isFeatured = item.optBoolean("is_featured"),
                ),
            )
        }
    }

    private fun JSONArray?.toSettingList(): List<PublicSetting> = buildList {
        if (this@toSettingList == null) {
            return@buildList
        }

        for (index in 0 until length()) {
            val item = optJSONObject(index) ?: continue
            add(
                PublicSetting(
                    key = item.optString("key"),
                    label = item.optString("label"),
                    value = item.optString("value"),
                    group = item.optString("group"),
                ),
            )
        }
    }

    private fun JSONObject.optStringOrNull(key: String): String? = optString(key).takeIf { it.isNotBlank() }

    private fun extractError(body: String, statusCode: Int): String {
        val parsed = runCatching { JSONObject(body) }.getOrNull()
        val message = parsed?.optStringOrNull("message")

        return message ?: "Request failed with status $statusCode."
    }

    private fun extractConnectivityError(exception: Exception): String = when (exception) {
        is IllegalArgumentException -> "The backend URL is invalid. Check the setting and try again."
        is UnknownHostException, is ConnectException -> "Could not reach the backend. Check the base URL and confirm the Laravel server is running."
        is SocketTimeoutException -> "The backend took too long to respond. Check the server and try again."
        is SSLException -> "A secure connection to the backend could not be established. Check the backend URL and SSL configuration."
        else -> exception.message ?: "Could not reach the backend."
    }
}