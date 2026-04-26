<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicPageController extends Controller
{
    public function home(): View
    {
        $menu = $this->publicMenuQuery()
            ->where('slug', 'home')
            ->first();

        if ($menu === null) {
            $menu = $this->publicMenuQuery()->orderBy('sort_order')->orderBy('name')->first();
        }

        abort_if($menu === null, 404);

        return $this->renderPage($menu);
    }

    public function show(Menu $menu): View
    {
        abort_unless(
            $menu->is_active
            && $menu->location === 'public-primary'
            && in_array($menu->visibility, $this->allowedMenuVisibilities(), true),
            404
        );

        return $this->renderPage($menu);
    }

    private function renderPage(Menu $menu): View
    {
        [$primaryContent, $pageContents, $pageCategories] = $this->resolvePageData($menu);

        return view('page', [
            'pageTemplate' => 'menu-show',
            'menu' => $menu,
            'primaryContent' => $primaryContent,
            'pageContents' => $pageContents,
            'pageCategories' => $pageCategories,
        ]);
    }

    /**
     * @return array{0: ?Content, 1: Collection<int, Content>, 2: Collection<int, ContentCategory>}
     */
    private function resolvePageData(Menu $menu): array
    {
        $allowedContentVisibilities = $this->allowedContentVisibilities();

        $items = $menu->items()
            ->where('is_active', true)
            ->whereIn('visibility', $this->allowedMenuVisibilities())
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $contentIds = collect();
        $categoryIds = collect();

        foreach ($items as $item) {
            $reference = trim((string) $item->target_reference);
            $contentIds = $contentIds->concat($this->referenceIds($reference, 'content'));
            $categoryIds = $categoryIds->concat($this->referenceIds($reference, 'category'));
        }

        $contentIds = $contentIds->unique()->values();
        $categoryIds = $categoryIds->unique()->values();

        $pageCategories = $categoryIds->isEmpty()
            ? collect()
            : ContentCategory::query()
                ->with([
                    'contents' => fn ($query) => $query
                        ->where('status', 'published')
                        ->whereIn('visibility', $allowedContentVisibilities)
                        ->latest('published_at'),
                ])
                ->whereIn('id', $categoryIds)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

        $pageContents = $contentIds->isEmpty()
            ? collect()
            : Content::query()
                ->with(['category', 'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->whereIn('id', $contentIds)
                ->where('status', 'published')
                ->whereIn('visibility', $allowedContentVisibilities)
                ->get()
                ->sortBy(fn (Content $content): int => (int) $contentIds->search($content->id))
                ->values();

        $primaryContent = Content::query()
            ->with(['category', 'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->where('status', 'published')
            ->whereIn('visibility', $allowedContentVisibilities)
            ->where(function ($query) use ($menu): void {
                $query->where('slug', $menu->slug)
                    ->orWhere('title', $menu->name);
            })
            ->latest('published_at')
            ->first();

        if ($primaryContent === null && $pageContents->isNotEmpty()) {
            $primaryContent = $pageContents->first();
            $pageContents = $pageContents->skip(1)->values();
        }

        return [$primaryContent, $pageContents, $pageCategories];
    }

    /**
     * @return Collection<int, int>
     */
    private function referenceIds(string $reference, string $prefix): Collection
    {
        if (! Str::startsWith($reference, $prefix.':')) {
            return collect();
        }

        return collect(explode(',', (string) Str::of($reference)->after(':')))
            ->map(fn (string $value): int => (int) trim($value))
            ->filter(fn (int $value): bool => $value > 0)
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function allowedMenuVisibilities(): array
    {
        return auth()->check() ? MenuItem::VISIBILITY_OPTIONS : ['public'];
    }

    /**
     * @return array<int, string>
     */
    private function allowedContentVisibilities(): array
    {
        return auth()->check() ? Content::VISIBILITY_OPTIONS : ['public'];
    }

    private function publicMenuQuery()
    {
        return Menu::query()
            ->where('location', 'public-primary')
            ->where('is_active', true)
            ->whereIn('visibility', $this->allowedMenuVisibilities())
            ->orderBy('sort_order')
            ->orderBy('name');
    }
}
