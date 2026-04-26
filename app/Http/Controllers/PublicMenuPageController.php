<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicMenuPageController extends Controller
{
    public function show(string $menuItemName): View
    {
        $item = MenuItem::query()
            ->where('is_active', true)
            ->whereIn('visibility', $this->allowedVisibilities())
            ->get()
            ->first(fn (MenuItem $candidate): bool => $candidate->publicPageSlug() === $menuItemName);

        abort_if($item === null, 404);

        [$categories, $contents, $context] = $this->resolvePageData($item);

        return view('page', [
            'pageTemplate' => 'menu-item-show',
            'menuItem' => $item,
            'pageCategories' => $categories,
            'pageContents' => $contents,
            'pageContext' => $context,
        ]);
    }

    /**
     * @return array{0: Collection<int, ContentCategory>, 1: Collection<int, Content>, 2: array<string, mixed>}
     */
    private function resolvePageData(MenuItem $item): array
    {
        $reference = trim((string) $item->target_reference);
        $categoryIds = $this->referenceIds($reference, 'category');
        $contentIds = $this->referenceIds($reference, 'content');
        $allowedContentVisibilities = $this->allowedContentVisibilities();

        $categories = ContentCategory::query()
            ->with([
                'contents' => fn ($query) => $query
                    ->with(['category', 'blocks' => fn ($blockQuery) => $blockQuery->where('is_active', true)->orderBy('sort_order'), 'media'])
                    ->where('status', 'published')
                    ->whereIn('visibility', $allowedContentVisibilities)
                    ->latest('published_at'),
            ])
            ->where('menu_item_id', $item->getKey())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($categoryIds->isNotEmpty()) {
            $referencedCategories = ContentCategory::query()
                ->with([
                    'contents' => fn ($query) => $query
                        ->with(['category', 'blocks' => fn ($blockQuery) => $blockQuery->where('is_active', true)->orderBy('sort_order'), 'media'])
                        ->where('status', 'published')
                        ->whereIn('visibility', $allowedContentVisibilities)
                        ->latest('published_at'),
                ])
                ->whereIn('id', $categoryIds)
                ->where('is_active', true)
                ->get();

            $categories = $categories
                ->concat($referencedCategories)
                ->unique('id')
                ->values();
        }

        $categoryContentIds = $categories
            ->flatMap(fn (ContentCategory $category): Collection => $category->contents->pluck('id'))
            ->unique()
            ->values();

        $contents = $contentIds->isEmpty()
            ? collect()
            : Content::query()
                ->with(['category', 'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'), 'media'])
                ->whereIn('id', $contentIds)
                ->where('status', 'published')
                ->whereIn('visibility', $allowedContentVisibilities)
                ->get()
                ->sortBy(fn (Content $content): int => (int) $contentIds->search($content->getKey()))
                ->reject(fn (Content $content): bool => $categoryContentIds->contains($content->getKey()))
                ->values();

        return [$categories, $contents, $this->pageContext($item, $categories, $contents)];
    }

    /**
     * @return Collection<int, int>
     */
    private function referenceIds(string $reference, string $prefix): Collection
    {
        if (! str_starts_with($reference, $prefix.':')) {
            return collect();
        }

        return collect(explode(',', (string) Str::of($reference)->after(':')))
            ->map(fn (string $value): int => (int) trim($value))
            ->filter(fn (int $value): bool => $value > 0)
            ->values();
    }

    /**
     * @param Collection<int, ContentCategory> $categories
     * @param Collection<int, Content> $contents
     * @return array<string, string>
     */
    private function pageContext(MenuItem $item, Collection $categories, Collection $contents): array
    {
        if ($categories->isNotEmpty() && $contents->isNotEmpty()) {
            return [
                'eyebrow' => 'Dynamic pathway',
                'description' => 'Browse linked categories and featured standalone content collected from this menu page.',
            ];
        }

        if ($categories->isNotEmpty()) {
            return [
                'eyebrow' => 'Category collection',
                'description' => 'Browse every linked category and its published content from this menu pathway.',
            ];
        }

        if ($contents->isNotEmpty()) {
            return [
                'eyebrow' => 'Curated pathway',
                'description' => 'A selected set of published content entries linked from this menu item.',
            ];
        }

        if (filled($item->target_reference) && Str::startsWith((string) $item->target_reference, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return [
                'eyebrow' => 'External resource',
                'description' => 'This menu item points to an external resource and does not have local content assigned yet.',
            ];
        }

        return [
            'eyebrow' => 'Menu page',
            'description' => 'No linked categories or published content are assigned to this menu item yet.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function allowedVisibilities(): array
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
}