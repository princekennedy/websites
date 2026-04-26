<?php

namespace App\Http\Controllers\Cms;

use App\Enums\DesignLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\MenuRequest;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('cms.menus.index', [
            'menus' => Menu::query()
                ->withCount('items')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('cms.menus.create', [
            'menu' => new Menu(),
            'visibilityOptions' => Menu::VISIBILITY_OPTIONS,
            'layoutOptions' => DesignLayoutType::options(),
        ]);
    }

    public function store(MenuRequest $request): RedirectResponse
    {
        $menu = Menu::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.menus.show', $menu)
            ->with('status', 'Menu created. Add items to define the app navigation.');
    }

    public function show(Menu $menu): View
    {
        return view('cms.menus.show', [
            'menu' => $menu->load(['items.parent']),
        ]);
    }

    public function edit(Menu $menu): View
    {
        return view('cms.menus.edit', [
            'menu' => $menu,
            'visibilityOptions' => Menu::VISIBILITY_OPTIONS,
            'layoutOptions' => DesignLayoutType::options(),
        ]);
    }

    public function update(MenuRequest $request, Menu $menu): RedirectResponse
    {
        $menu->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('cms.menus.show', $menu)
            ->with('status', 'Menu updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('cms.menus.index')
            ->with('status', 'Menu deleted.');
    }
}