<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetTenantSchema
{
    private const SKIP_SUBDOMAINS = ['www', 'api', 'mail', 'admin', 'cms', 'dev', 'staging', 'test', 'app', 'web', 'console'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if (!$this->isPostgres()) {
            return $next($request);
        }

        $schema = $this->resolveSchema($request);

        if ($schema !== null) {
            $this->setSearchPath($schema);
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is('up') || $request->is('health') || $request->is('telescope/*');
    }

    private function isPostgres(): bool
    {
        return DB::connection()->getDriverName() === 'pgsql';
    }

    private function resolveSchema(Request $request): ?string
    {
        $host = Str::lower((string) $request->getHost());

        $schema = $this->resolveFromSubdomain($host);

        if ($schema !== null) {
            return $schema;
        }

        return $this->resolveFromDatabase($host);
    }

    private function resolveFromSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);

        if (count($parts) < 3) {
            return null;
        }

        $subdomain = $parts[0];

        if (in_array($subdomain, self::SKIP_SUBDOMAINS, true)) {
            return null;
        }

        $schemaName = 'tenant_' . Str::slug($subdomain, '_');

        return $this->schemaExists($schemaName) ? $schemaName : null;
    }

    private function resolveFromDatabase(string $host): ?string
    {
        try {
            if (!Schema::hasTable('websites')) {
                return null;
            }

            $website = DB::table('websites')
                ->where('is_active', true)
                ->where('domain', $host)
                ->first(['slug']);

            if ($website === null) {
                $website = DB::table('websites')
                    ->where('is_active', true)
                    ->get(['slug'])
                    ->first(function ($candidate) use ($host): bool {
                        $slug = Str::lower((string) $candidate->slug);
                        if ($slug === '') {
                            return false;
                        }
                        return preg_match('/(^|[.-])'.preg_quote($slug, '/').'($|[.-])/', $host) === 1;
                    });
            }

            if ($website !== null) {
                $schemaName = 'tenant_' . Str::slug($website->slug, '_');
                if ($this->schemaExists($schemaName)) {
                    return $schemaName;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Could not resolve tenant schema from database', [
                'host' => $host,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function schemaExists(string $schema): bool
    {
        try {
            $result = DB::select(
                'SELECT EXISTS(SELECT 1 FROM information_schema.schemata WHERE schema_name = ?) AS exists',
                [$schema]
            );

            return !empty($result) && $result[0]->exists;
        } catch (\Throwable $e) {
            Log::warning('Failed to check schema existence', [
                'schema' => $schema,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function setSearchPath(string $schema): void
    {
        $quotedSchema = '"' . str_replace('"', '""', $schema) . '"';
        $quotedPublic = '"public"';

        DB::statement("SET search_path TO {$quotedSchema}, {$quotedPublic}");
    }
}
