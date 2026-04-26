<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureCmsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if($user === null, Response::HTTP_FORBIDDEN);

        if (! $user->canAccessCms()) {
            return redirect()
                ->route('home')
                ->with('status', 'CMS access is limited to administrator accounts.');
        }

        $routeName = $request->route()?->getName();
        $requiredPermission = $routeName === null ? null : match (true) {
            Str::is([
                'cms.categories.create',
                'cms.categories.store',
                'cms.categories.edit',
                'cms.categories.update',
                'cms.categories.destroy',
            ], $routeName) => 'cms.manage.categories',
            Str::is([
                'cms.contents.create',
                'cms.contents.store',
                'cms.contents.edit',
                'cms.contents.update',
                'cms.contents.destroy',
            ], $routeName) => 'cms.manage.contents',
            Str::is([
                'cms.sliders.create',
                'cms.sliders.store',
                'cms.sliders.edit',
                'cms.sliders.update',
                'cms.sliders.destroy',
            ], $routeName) => 'cms.manage.sliders',
            Str::is([
                'cms.menus.create',
                'cms.menus.store',
                'cms.menus.edit',
                'cms.menus.update',
                'cms.menus.destroy',
                'cms.menus.items.create',
                'cms.menus.items.store',
                'cms.menus.items.edit',
                'cms.menus.items.update',
                'cms.menus.items.destroy',
            ], $routeName) => 'cms.manage.menus',
            $routeName === 'cms.settings.update' => 'cms.manage.settings',
            default => null,
        };

        abort_if($requiredPermission !== null && ! $user->hasCmsPermission($requiredPermission), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}