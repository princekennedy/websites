<?php

namespace App\Http\Controllers\Cms;

use App\Enums\CategoryLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\ContentCategoryRequest;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContentCategoryController extends Controller
{
    public function index(): View
    {
        return view('cms.categories.index', [
            'categories' => ContentCategory::query()
                ->withCount('contents')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('cms.categories.create', [
            'category' => new ContentCategory(),
            'menuItemOptions' => $this->menuItemOptions(),
            'layoutOptions' => CategoryLayoutType::options(),
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function store(ContentCategoryRequest $request): RedirectResponse
    {
        ContentCategory::create([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.categories.index')
            ->with('status', 'Category created.');
    }

    public function show(ContentCategory $category): View
    {
        $category->load(['contents.blocks']);
        return view('cms.categories.show', [
            'category' => $category,
        ]);
    }

    public function edit(ContentCategory $category): View
    {
        return view('cms.categories.edit', [
            'category' => $category,
            'menuItemOptions' => $this->menuItemOptions(),
            'layoutOptions' => CategoryLayoutType::options(),
            'visibilityOptions' => Content::VISIBILITY_OPTIONS,
        ]);
    }

    public function update(ContentCategoryRequest $request, ContentCategory $category): RedirectResponse
    {
        $category->update([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.categories.index')
            ->with('status', 'Category updated.');
    }

    public function destroy(ContentCategory $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('cms.categories.index')
            ->with('status', 'Category deleted.');
    }

    private function menuItemOptions()
    {
        return MenuItem::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);
    }
}