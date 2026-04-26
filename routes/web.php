<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Cms\AppSettingController;
use App\Http\Controllers\Cms\ContentCategoryController;
use App\Http\Controllers\Cms\ContentController;
use App\Http\Controllers\Cms\DashboardController;
use App\Http\Controllers\Cms\MenuController;
use App\Http\Controllers\Cms\MenuItemController;
use App\Http\Controllers\Cms\SliderController;
use App\Http\Middleware\EnsureCmsAccess;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicMenuPageController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/admin',[ HomeController::class, 'admin'])->name('admin');
Route::get('/pages/{menu:slug}', [PublicPageController::class, 'show'])->name('public.pages.show');
Route::get('/menu-item/{menuItemName}', [PublicMenuPageController::class, 'show'])->name('public.menu-pages.show');
Route::get('/topics', [PublicContentController::class, 'categories'])->name('public.categories.index');
Route::get('/topics/{category:slug}', [PublicContentController::class, 'showCategory'])->name('public.categories.show');
Route::get('/content', [PublicContentController::class, 'contents'])->name('public.contents.index');
Route::get('/content/{content:slug}', [PublicContentController::class, 'showContent'])->name('public.contents.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::redirect('/websites', '/cms/websites');
    Route::get('/dashboard', function (Request $request): RedirectResponse {
        return $request->user()?->canAccessCms()
            ? redirect()->route('cms.dashboard')
            : redirect()->route('home');
    })->name('dashboard');
});

Route::redirect('/cms', '/cms/dashboard');

Route::prefix('cms')
    ->name('cms.')
    ->middleware(['auth', EnsureCmsAccess::class])
    ->scopeBindings()
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/websites', [WebsiteController::class, 'index'])->name('websites.index');
        Route::post('/websites', [WebsiteController::class, 'store'])->name('websites.store');
        Route::post('/websites/{website}/switch', [WebsiteController::class, 'switch'])->name('websites.switch');
        Route::resource('categories', ContentCategoryController::class);
        Route::resource('contents', ContentController::class)->except(['show']);
        Route::resource('sliders', SliderController::class)->except(['show']);
        Route::resource('menus', MenuController::class);
        Route::get('settings', [AppSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AppSettingController::class, 'update'])->name('settings.update');
        Route::get('menus/{menu}/items/create', [MenuItemController::class, 'create'])->name('menus.items.create');
        Route::post('menus/{menu}/items', [MenuItemController::class, 'store'])->name('menus.items.store');
        Route::get('menus/{menu}/items/{item}/edit', [MenuItemController::class, 'edit'])->name('menus.items.edit');
        Route::put('menus/{menu}/items/{item}', [MenuItemController::class, 'update'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{item}', [MenuItemController::class, 'destroy'])->name('menus.items.destroy');
    });
