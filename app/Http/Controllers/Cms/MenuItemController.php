<?php

namespace App\Http\Controllers\Cms;

use App\Enums\DesignLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\MenuItemRequest;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MenuItemController extends Controller
{
    public function create(Menu $menu): View
    {
        return view('cms.menu-items.create', $this->viewData($menu, new MenuItem()));
    }

    public function store(MenuItemRequest $request, Menu $menu): RedirectResponse
    {
        $menu->items()->create(MenuItem::normalizeForPersistence([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'open_in_webview' => $request->boolean('open_in_webview'),
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()
            ->route('cms.menus.edit', $menu)
            ->with('status', 'Menu item created.');
    }

    public function edit(Menu $menu, MenuItem $item): View
    {
        return view('cms.menu-items.edit', $this->viewData($menu, $item));
    }

    public function update(MenuItemRequest $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        $item->update(MenuItem::normalizeForPersistence([
            ...$request->validated(),
            'sort_order' => $request->integer('sort_order'),
            'open_in_webview' => $request->boolean('open_in_webview'),
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()
            ->route('cms.menus.edit', $menu)
            ->with('status', 'Menu item updated.');
    }

    public function destroy(Menu $menu, MenuItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()
            ->route('cms.menus.edit', $menu)
            ->with('status', 'Menu item deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function viewData(Menu $menu, MenuItem $item): array
    {
        return [
            'menu' => $menu,
            'item' => $item,
            'visibilityOptions' => MenuItem::VISIBILITY_OPTIONS,
            'layoutOptions' => DesignLayoutType::options(),
            'parentOptions' => $menu->items()->orderBy('title')->get(),
            'contentOptions' => Content::query()->orderBy('title')->get(['id', 'title']),
            'categoryOptions' => ContentCategory::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}