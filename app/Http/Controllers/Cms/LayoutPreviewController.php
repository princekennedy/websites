<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Slider;
use App\Support\DesignLayouts;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LayoutPreviewController extends Controller
{
    public function show(Request $request): Response
    {
        $section = array_key_exists($request->query('section', ''), DesignLayouts::sections())
            ? $request->query('section')
            : 'content';

        $layout = preg_replace('/[^a-z0-9\-]/', '', (string) $request->query('layout', 'default'));

        $viewName = "designs.{$section}.{$layout}";
        if (! view()->exists($viewName)) {
            $viewName = "designs.{$section}.default";
        }

        $html = view($viewName, $this->sampleData($section))->render();

        return response(
            view('cms.layout-preview-wrap', compact('html'))->render()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function sampleData(string $section): array
    {
        return match ($section) {
            'content'            => $this->contentData(),
            'content-categories' => $this->categoryData(),
            'menu-items'         => $this->menuItemData(),
            'sliders'            => $this->sliderData(),
            default              => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function contentData(): array
    {
        $contentId = request()->integer('content_id');

        $content = Content::query()
            ->with('category')
            ->when($contentId > 0, fn ($query) => $query->whereKey($contentId))
            ->where('status', 'published')
            ->latest()
            ->first() ?? new Content([
                'title'        => 'Sample Content Title',
                'summary'      => 'A concise summary describing what this content covers and why it matters to the reader.',
                'body'         => '<h2>Introduction</h2><p>This is a preview of the content layout design. Content blocks support rich text with headings, lists, and inline links.</p><p>The layout you select here controls how published content will be rendered on the public website.</p>',
                'content_type' => 'article',
            ]);

        $relatedContents = Content::query()
            ->where('status', 'published')
            ->where('id', '!=', $content->id ?? 0)
            ->limit(3)
            ->get();

        return compact('content', 'relatedContents');
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryData(): array
    {
        $categoryId = request()->integer('category_id');

        $category = ContentCategory::query()
            ->when($categoryId > 0, fn ($query) => $query->whereKey($categoryId))
            ->where('is_active', true)
            ->first() ?? new ContentCategory([
                'name'        => 'Sample Category',
                'description' => 'A description of this topic category and the published content it contains.',
            ]);

        $contents = $category->exists
            ? $category->contents()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString()
            : Content::query()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString();

        return compact('category', 'contents');
    }

    /**
     * @return array<string, mixed>
     */
    private function menuItemData(): array
    {
        $menuItemId = request()->integer('menu_item_id');
        $menuId = request()->integer('menu_id');

        if ($menuId > 0) {
            return $this->menuData($menuId);
        }

        $menuItemQuery = MenuItem::query();

        if ($menuItemId > 0) {
            // In CMS preview, allow previewing the selected record even if inactive.
            $menuItemQuery->whereKey($menuItemId);
        } else {
            $menuItemQuery->where('is_active', true);
        }

        $menuItem = $menuItemQuery->first() ?? new MenuItem(['title' => 'Sample Menu Page']);

        [$pageCategories, $pageContents, $pageContext] = $this->resolveMenuItemPayload($menuItem);

        return compact('menuItem', 'pageCategories', 'pageContents', 'pageContext');
    }

    /**
     * @return array<string, mixed>
     */
    private function menuData(int $menuId): array
    {
        $menu = Menu::query()
            ->with(['items' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('title')])
            ->whereKey($menuId)
            ->first();

        if ($menu === null || $menu->items->isEmpty()) {
            $menuItem = new MenuItem(['title' => 'Menu Preview']);
            $pageCategories = ContentCategory::query()
                ->with(['contents' => fn ($q) => $q->where('status', 'published')->limit(4)])
                ->where('is_active', true)
                ->limit(2)
                ->get();

            $pageContents = Content::query()
                ->where('status', 'published')
                ->limit(3)
                ->get();

            $pageContext = [
                'eyebrow' => 'Menu Preview',
                'description' => 'No active menu items available. Showing sample data.',
            ];

            return compact('menuItem', 'pageCategories', 'pageContents', 'pageContext');
        }

        $primary = $menu->items->first();
        $allCategories = collect();
        $allContents = collect();

        foreach ($menu->items as $item) {
            [$categories, $contents] = $this->resolveReferencesFromTargetReference((string) $item->target_reference, $item->getKey());
            $allCategories = $allCategories->concat($categories);
            $allContents = $allContents->concat($contents);
        }

        $pageCategories = $allCategories->unique('id')->values();
        $pageContents = $allContents->unique('id')->values();

        $menuItem = new MenuItem([
            'title' => $menu->name,
        ]);

        $pageContext = [
            'eyebrow' => 'Menu: '.$menu->name,
            'description' => 'Preview built from all active menu items in this menu.',
        ];

        if ($primary instanceof MenuItem) {
            $menuItem->title = $primary->title;
        }

        return compact('menuItem', 'pageCategories', 'pageContents', 'pageContext');
    }

    /**
     * @return array{0: Collection<int, ContentCategory>, 1: Collection<int, Content>, 2: array<string, string>}
     */
    private function resolveMenuItemPayload(MenuItem $item): array
    {
        [$pageCategories, $pageContents] = $this->resolveReferencesFromTargetReference((string) $item->target_reference, $item->getKey());

        $pageContext = [
            'eyebrow' => 'Menu page',
            'description' => $pageCategories->isNotEmpty() || $pageContents->isNotEmpty()
                ? 'Preview generated from this menu item target reference and linked categories.'
                : 'No linked categories or published content are assigned to this page yet.',
        ];

        return [$pageCategories, $pageContents, $pageContext];
    }

    /**
     * @return array{0: Collection<int, ContentCategory>, 1: Collection<int, Content>}
     */
    private function resolveReferencesFromTargetReference(string $reference, ?int $menuItemId = null): array
    {
        $categoryIds = $this->referenceIds($reference, 'category');
        $contentIds = $this->referenceIds($reference, 'content');

        $categories = ContentCategory::query()
            ->with([
                'contents' => fn ($query) => $query
                    ->with(['category', 'blocks' => fn ($blockQuery) => $blockQuery->where('is_active', true)->orderBy('sort_order'), 'media'])
                    ->latest('published_at')
                    ->latest('updated_at'),
            ])
            ->where(function ($query) use ($menuItemId, $categoryIds): void {
                if ($menuItemId !== null) {
                    $query->where('menu_item_id', $menuItemId);
                }

                if ($categoryIds->isNotEmpty()) {
                    $query->orWhereIn('id', $categoryIds);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryContentIds = $categories
            ->flatMap(fn (ContentCategory $category): Collection => $category->contents->pluck('id'))
            ->unique()
            ->values();

        $contents = $contentIds->isEmpty()
            ? collect()
            : Content::query()
                ->with(['category', 'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'), 'media'])
                ->whereIn('id', $contentIds)
                ->get()
                ->sortBy(fn (Content $content): int => (int) $contentIds->search($content->getKey()))
                ->reject(fn (Content $content): bool => $categoryContentIds->contains($content->getKey()))
                ->values();

        return [$categories->values(), $contents->values()];
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
     * @return array<string, mixed>
     */
    private function sliderData(): array
    {
        $sliderId = request()->integer('slider_id');

        $sliders = Slider::query()
            ->with('media')
            ->when($sliderId > 0, fn ($query) => $query->whereKey($sliderId))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $slides = $sliders->map(fn (Slider $slider): array => [
            'title'   => $slider->title,
            'kicker'  => $slider->kicker,
            'desc'    => $slider->caption,
            'image'   => $slider->imageUrl() ?: 'https://placehold.co/1200x600/1e293b/94a3b8?text=Slide+Preview',
            'buttons' => array_values(array_filter([
                $slider->primary_button_text ? [
                    'text'  => $slider->primary_button_text,
                    'link'  => $slider->primary_button_link ?? '#',
                    'class' => 'bg-indigo-600 hover:bg-indigo-700',
                ] : null,
                $slider->secondary_button_text ? [
                    'text'  => $slider->secondary_button_text,
                    'link'  => $slider->secondary_button_link ?? '#',
                    'class' => 'bg-white/15 hover:bg-white/25 backdrop-blur',
                ] : null,
            ])),
        ])->values()->all();

        return compact('slides');
    }
}
