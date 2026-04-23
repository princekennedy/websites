<?php

namespace App\Support;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicNavigation
{
    private ?int $resolvedWebsiteId = null;

    public function menus(string $location = 'public-primary'): Collection
    {
        if (! Schema::hasTable('menus') || ! Schema::hasTable('menu_items')) {
            return collect();
        }

        $websiteId = $this->websiteId();

        if ($websiteId === null) {
            return collect();
        }

        $allowedVisibilities = $this->allowedVisibilities();

        return Menu::query()
            ->withoutGlobalScope('website')
            ->where('website_id', $websiteId)
            ->where('location', $location)
            ->where('is_active', true)
            ->whereIn('visibility', $allowedVisibilities)
            ->with([
                'items' => fn ($query) => $query
                    ->withoutGlobalScope('website')
                    ->where('website_id', $websiteId)
                    ->where('is_active', true)
                    ->whereIn('visibility', $allowedVisibilities)
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->orderBy('name')
            ->get()
            ->map(function (Menu $menu): array {
                $items = $menu->items
                    ->whereNull('parent_id')
                    ->sortBy('sort_order')
                    ->values()
                    ->map(fn (MenuItem $item): array => $this->mapItem($item, $menu->items))
                    ->filter(fn (array $item): bool => filled($item['href']) || collect($item['children'])->isNotEmpty())
                    ->values();

                return [
                    'title' => $menu->name,
                    'items' => $items,
                ];
            })
            ->filter(fn (array $menu): bool => collect($menu['items'])->isNotEmpty())
            ->values();
    }

    public function items(string $location = 'public-primary'): Collection
    {
        $menus = $this->menus($location);

        if ($menus->isEmpty()) {
            return $this->fallbackItems();
        }

        $items = $menus
            ->flatMap(fn (array $menu): Collection => collect($menu['items']))
            ->values();

        if ($items->isEmpty()) {
            return $this->fallbackItems();
        }

        return $items;
    }

    public function quickLinks(string $location = 'public-primary', int $limit = 5): Collection
    {
        return $this->collectQuickLinks($this->items($location))
            ->unique('href')
            ->take($limit)
            ->values();
    }

    public function menu(string $location = 'public-primary'): ?Menu
    {
        if (! Schema::hasTable('menus') || ! Schema::hasTable('menu_items')) {
            return null;
        }

        $websiteId = $this->websiteId();

        if ($websiteId === null) {
            return null;
        }

        $allowedVisibilities = $this->allowedVisibilities();

        return Menu::query()
            ->withoutGlobalScope('website')
            ->where('website_id', $websiteId)
            ->where('location', $location)
            ->where('is_active', true)
            ->whereIn('visibility', $allowedVisibilities)
            ->with([
                'items' => fn ($query) => $query
                    ->withoutGlobalScope('website')
                    ->where('website_id', $websiteId)
                    ->where('is_active', true)
                    ->whereIn('visibility', $allowedVisibilities)
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->first();
    }

    /**
     * @return array<int, string>
     */
    private function allowedVisibilities(): array
    {
        return auth()->check() ? MenuItem::VISIBILITY_OPTIONS : ['public'];
    }

    private function mapItem(MenuItem $item, Collection $allItems): array
    {
        $children = $allItems
            ->where('parent_id', $item->id)
            ->sortBy('sort_order')
            ->values()
            ->map(fn (MenuItem $child): array => $this->mapItem($child, $allItems))
            ->filter(fn (array $child): bool => filled($child['href']) || collect($child['children'])->isNotEmpty())
            ->values();

        return [
            'title' => $item->title,
            'href' => $this->resolveMenuItemUrl($item),
            'children' => $children,
        ];
    }

    private function collectQuickLinks(Collection $items): Collection
    {
        return $items
            ->flatMap(function (array $item): Collection {
                $children = collect($item['children'] ?? []);

                if ($children->isNotEmpty()) {
                    $childLinks = $this->collectQuickLinks($children);

                    if ($childLinks->isNotEmpty()) {
                        return $childLinks;
                    }
                }

                return filled($item['href'] ?? null)
                    ? collect([['title' => $item['title'], 'href' => $item['href']]])
                    : collect();
            })
            ->values();
    }

    private function fallbackItems(): Collection
    {
        return collect([
            ['title' => 'Topics', 'href' => route('public.categories.index'), 'children' => collect()],
            ['title' => 'Content', 'href' => route('public.contents.index'), 'children' => collect()],
            ['title' => 'FAQs', 'href' => route('public.faqs.index'), 'children' => collect()],
            ['title' => 'Quizzes', 'href' => route('public.quizzes.index'), 'children' => collect()],
            ['title' => 'Services', 'href' => route('public.services.index'), 'children' => collect()],
        ]);
    }

    private function resolveMenuItemUrl(MenuItem $item): ?string
    {
        if ($item->type === 'external_url') {
            return $this->resolveExternalTarget($item->route ?? $item->target_reference);
        }

        if ($item->type === 'internal_route') {
            return $this->resolveInternalRoute($item->route);
        }

        return $item->route ?? route('public.menu-pages.show', ['menuItemName' => $item->publicPageSlug()]);
    }

    private function resolveContentTarget(?string $reference): ?string
    {
        if (! Schema::hasTable('contents')) {
            return route('public.contents.index');
        }

        $id = $this->extractReferenceId($reference, 'content');

        if ($id === null) {
            return route('public.contents.index');
        }

        $slug = Content::query()
            ->withoutGlobalScope('website')
            ->where('website_id', $this->websiteId())
            ->whereKey($id)
            ->value('slug');

        return $slug === null ? route('public.contents.index') : route('public.contents.show', $slug);
    }

    private function resolveCategoryTarget(?string $reference): ?string
    {
        if (! Schema::hasTable('content_categories')) {
            return route('public.categories.index');
        }

        $id = $this->extractReferenceId($reference, 'category');

        if ($id === null) {
            return route('public.categories.index');
        }

        $slug = ContentCategory::query()
            ->withoutGlobalScope('website')
            ->where('website_id', $this->websiteId())
            ->whereKey($id)
            ->value('slug');

        return $slug === null ? route('public.categories.index') : route('public.categories.show', $slug);
    }

    private function resolveQuizTarget(?string $reference): ?string
    {
        if (! Schema::hasTable('quizzes')) {
            return route('public.quizzes.index');
        }

        $id = $this->extractReferenceId($reference, 'quiz');

        if ($id === null) {
            return route('public.quizzes.index');
        }

        $slug = Quiz::query()
            ->withoutGlobalScope('website')
            ->where('website_id', $this->websiteId())
            ->whereKey($id)
            ->value('slug');

        return $slug === null ? route('public.quizzes.index') : route('public.quizzes.show', $slug);
    }

    private function websiteId(): ?int
    {
        if ($this->resolvedWebsiteId !== null) {
            return $this->resolvedWebsiteId;
        }

        $this->resolvedWebsiteId = app(CurrentWebsite::class)->id();

        return $this->resolvedWebsiteId;
    }

    private function resolveInternalRoute(?string $route): ?string
    {
        if (! filled($route)) {
            return null;
        }

        if (Route::has($route)) {
            return route($route);
        }

        return $this->resolveExternalTarget($route);
    }

    private function resolveExternalTarget(?string $target): ?string
    {
        if (! filled($target)) {
            return null;
        }

        if (Str::startsWith($target, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return $target;
        }

        return url(Str::startsWith($target, ['/']) ? $target : '/'.ltrim($target, '/'));
    }

    private function extractReferenceId(?string $reference, string $prefix): ?int
    {
        if (! is_string($reference) || ! str_starts_with($reference, $prefix.':')) {
            return null;
        }

        $value = (int) str($reference)->after(':')->toString();

        return $value > 0 ? $value : null;
    }
}