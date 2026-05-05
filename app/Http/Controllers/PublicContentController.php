<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PublicContentController extends Controller
{
    public function categories(): View
    {
        if (! Schema::hasTable('contents')) {
            return $this->emptyCategoriesPage();
        }

        try {
            $categories = Content::query()->categories()
                ->where('is_active', true)
                ->withCount([
                    'contents' => fn ($query) => $query
                        ->where('status', 'published')
                        ->where('visibility', 'public'),
                ])
                ->with([
                    'contents' => fn ($query) => $query
                        ->where('status', 'published')
                        ->where('visibility', 'public')
                        ->latest('published_at')
                        ->limit(3),
                ])
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
        } catch (Throwable $exception) {
            report($exception);

            return $this->emptyCategoriesPage();
        }

        return view('page', [
            'pageTemplate' => 'categories-index',
            'categories' => $categories,
        ]);
    }

    public function showCategory(Content $category): View
    {
        abort_unless($category->is_active, 404);

        $contents = $category->contents()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('page', [
            'pageTemplate' => 'categories-show',
            'category' => $category,
            'contents' => $contents,
        ]);
    }

    public function contents(Request $request): View
    {
        $selectedCategory = $request->string('category')->toString();
        $selectedType = $request->string('type')->toString();
        $search = trim($request->string('q')->toString());

        if (! Schema::hasTable('contents')) {
            return $this->emptyContentsPage($selectedCategory, $selectedType, $search);
        }

        try {
            $contents = Content::query()
                ->with('category')
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->when($selectedCategory !== '', function ($query) use ($selectedCategory): void {
                    $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $selectedCategory));
                })
                ->when($selectedType !== '', fn ($query) => $query->where('content_type', $selectedType))
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where(function ($innerQuery) use ($search): void {
                        $innerQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('summary', 'like', "%{$search}%")
                            ->orWhere('body', 'like', "%{$search}%");
                    });
                })
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString();

            $filterCategories = Content::query()->categories()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
        } catch (Throwable $exception) {
            report($exception);

            return $this->emptyContentsPage($selectedCategory, $selectedType, $search);
        }

        return view('page', [
            'pageTemplate' => 'contents-index',
            'contents' => $contents,
            'filterCategories' => $filterCategories,
            'typeOptions' => Content::TYPE_OPTIONS,
            'selectedCategory' => $selectedCategory,
            'selectedType' => $selectedType,
            'search' => $search,
        ]);
    }

    private function emptyCategoriesPage(): View
    {
        return view('page', [
            'pageTemplate' => 'categories-index',
            'categories' => collect(),
        ]);
    }

    private function emptyContentsPage(string $selectedCategory, string $selectedType, string $search): View
    {
        return view('page', [
            'pageTemplate' => 'contents-index',
            'contents' => collect(),
            'filterCategories' => collect(),
            'typeOptions' => Content::TYPE_OPTIONS,
            'selectedCategory' => $selectedCategory,
            'selectedType' => $selectedType,
            'search' => $search,
        ]);
    }

    public function showContent(Content $content): View
    {
        abort_unless($content->status === 'published' && $content->visibility === 'public', 404);

        $content->load([
            'category',
            'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
        ]);

        $relatedContents = Content::query()
            ->with('category')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->whereKeyNot($content->getKey())
            ->when($content->category_id !== null, fn ($query) => $query->where('parent_id', $content->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('page', [
            'pageTemplate' => 'contents-show',
            'content' => $content,
            'relatedContents' => $relatedContents,
        ]);
    }
}