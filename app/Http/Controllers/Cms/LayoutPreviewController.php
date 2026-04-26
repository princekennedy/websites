<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Support\DesignLayouts;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\MenuItem;
use App\Models\Slider;
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
        $content = Content::query()
            ->with('category')
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
        $category = ContentCategory::query()
            ->with(['contents' => fn ($q) => $q->where('status', 'published')->limit(6)])
            ->where('is_active', true)
            ->first() ?? new ContentCategory([
                'name'        => 'Sample Category',
                'description' => 'A description of this topic category and the published content it contains.',
            ]);

        $contents = $category->relationLoaded('contents') ? $category->contents : collect();

        return compact('category', 'contents');
    }

    /**
     * @return array<string, mixed>
     */
    private function menuItemData(): array
    {
        $menuItem = MenuItem::query()
            ->where('is_active', true)
            ->first() ?? new MenuItem(['title' => 'Sample Menu Page']);

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
            'eyebrow'     => 'Documentation',
            'description' => 'Browse the available guides and articles linked to this page.',
        ];

        return compact('menuItem', 'pageCategories', 'pageContents', 'pageContext');
    }

    /**
     * @return array<string, mixed>
     */
    private function sliderData(): array
    {
        $sliders = Slider::query()
            ->with('media')
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
