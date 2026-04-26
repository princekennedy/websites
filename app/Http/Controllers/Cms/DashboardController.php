<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Slider;
use App\Models\Website;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('cms.dashboard', [
            'stats' => [
                'websites' => Website::count(),
                'categories' => ContentCategory::count(),
                'contents' => Content::count(),
                'sliders' => Slider::count(),
                'menus' => Menu::count(),
                'menuItems' => MenuItem::count(),
                'settings' => AppSetting::count(),
            ],
            'recentContents' => Content::query()
                ->with('category')
                ->latest('updated_at')
                ->limit(6)
                ->get(),
            'moduleHighlights' => [
                [
                    'label' => 'Website workspaces',
                    'count' => Website::count(),
                    'description' => 'Active websites that can be managed and switched inside the CMS.',
                ],
                [
                    'label' => 'Published content',
                    'count' => Content::where('status', 'published')->count(),
                    'description' => 'Published pages and articles available for public rendering.',
                ],
                [
                    'label' => 'Active sliders',
                    'count' => Slider::where('is_active', true)->count(),
                    'description' => 'Homepage and campaign slider entries ready for the current default design.',
                ],
                [
                    'label' => 'App settings',
                    'count' => AppSetting::count(),
                    'description' => 'Runtime app labels, links, and support contact details.',
                ],
            ],
        ]);
    }
}