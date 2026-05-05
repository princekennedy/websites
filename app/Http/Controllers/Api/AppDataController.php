<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Content;
use App\Models\Menu;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppDataController extends Controller
{
    public function config(): JsonResponse
    {
        $settings = AppSetting::query()
            ->where('is_public', true)
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group')
            ->map(fn ($group) => $group->mapWithKeys(fn (AppSetting $setting) => [$setting->key => $this->settingValue($setting)]));

        return $this->respond(
            'Application configuration loaded successfully.',
            $settings,
            ['groups' => $settings->keys()->values()->all()],
        );
    }

    public function bootstrap(Request $request): JsonResponse
    {
        $allowedVisibilities = $this->allowedMenuVisibilities($request);
        $allowedContentVisibilities = $this->allowedContentVisibilities($request);

        $menu = Menu::query()
            ->with([
                'items' => fn ($query) => $query
                    ->where('is_active', true)
                    ->whereIn('visibility', $allowedVisibilities)
                    ->orderBy('sort_order'),
            ])
            ->where('location', 'public-primary')
            ->where('is_active', true)
            ->whereIn('visibility', $allowedVisibilities)
            ->first();

        $categories = $this->categoriesPayload($allowedContentVisibilities);
        $contents = $this->contentsPayload($allowedContentVisibilities, limit: 8, includeBlocks: true);
        $settings = AppSetting::query()
            ->where('is_public', true)
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->map(fn (AppSetting $setting): array => [
                'key' => $setting->key,
                'label' => $setting->label,
                'value' => $this->settingValue($setting),
                'layout_type' => $setting->layout_type ?: 'default',
                'group' => $setting->group,
                'input_type' => $setting->input_type,
            ])->values()->all();

        return $this->respond('Application bootstrap loaded successfully.', [
            'menu' => [
                'name' => $menu?->name,
                'layout_type' => $menu?->layout_type ?: 'default',
                'location' => $menu?->location,
                'items' => $menu === null ? [] : $this->menuTree($menu->items),
            ],
            'hero_slides' => $this->heroSlidesPayload(),
            'categories' => $categories,
            'featured_contents' => $contents,
            'settings' => $settings,
        ]);
    }

    public function mainMenu(Request $request): JsonResponse
    {
        $allowedVisibilities = $this->allowedMenuVisibilities($request);

        $menu = Menu::query()
            ->with(['items' => fn ($query) => $query
                ->where('is_active', true)
                ->whereIn('visibility', $allowedVisibilities)
                ->orderBy('sort_order')])
            ->where('location', 'public-primary')
            ->where('is_active', true)
            ->whereIn('visibility', $allowedVisibilities)
            ->first();

        return $this->respond('Main menu loaded successfully.', $menu === null ? null : [
            'id' => $menu->id,
            'name' => $menu->name,
            'slug' => $menu->slug,
            'layout_type' => $menu->layout_type ?: 'default',
            'location' => $menu->location,
            'items' => $this->menuTree($menu->items),
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = collect($this->categoriesPayload(['public']));

        return $this->respond('Categories loaded successfully.', $categories, ['count' => $categories->count()]);
    }

    public function contents(Request $request): JsonResponse
    {
        $limit = max(1, min($request->integer('limit', 12), 50));
        $contents = collect($this->contentsPayload(
            ['public'],
            limit: $limit,
            includeBlocks: false,
            type: $request->string('type')->toString() ?: null,
            audience: $request->string('audience')->toString() ?: null,
            category: $request->string('category')->toString() ?: null,
            search: $request->string('q')->toString() ?: null,
        ));

        return $this->respond('Content loaded successfully.', $contents, ['count' => $contents->count()]);
    }

    public function showContent(string $slug): JsonResponse
    {
        $content = Content::query()
            ->with([
                'category',
                'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->firstOrFail();

        return $this->respond('Content loaded successfully.', [
            'id' => $content->id,
            'title' => $content->title,
            'slug' => $content->slug,
            'summary' => $content->summary,
            'body' => $content->body,
            'layout_type' => $content->layout_type ?: 'default',
            'content_type' => $content->content_type,
            'audience' => $content->audience,
            'featured_image_url' => $content->featuredImageUrl(),
            'attachments' => $content->attachmentItems(),
            'published_at' => optional($content->published_at)->toIso8601String(),
            'category' => $content->category === null ? null : [
                'id' => $content->category->id,
                'name' => $content->category->name,
                'slug' => $content->category->slug,
            ],
            'blocks' => $content->blocks->map(fn ($block) => [
                'id' => $block->id,
                'block_type' => $block->block_type,
                'layout_type' => $block->layout_type ?: 'default',
                'title' => $block->title,
                'body' => $block->body,
                'sort_order' => $block->sort_order,
            ])->values()->all(),
        ]);
    }

    public function notifications(): JsonResponse
    {
        return $this->respond('Notifications loaded successfully.', [], ['count' => 0]);
    }

    private function respond(string $message, mixed $data, array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    private function categoriesPayload(array $allowedVisibilities): array
    {
        return Content::query()->categories()
            ->where('is_active', true)
            ->withCount([
                'contents' => fn ($query) => $query
                    ->where('status', 'published')
                    ->whereIn('visibility', $allowedVisibilities),
            ])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn (Content $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'layout_type' => $category->layout_type ?: 'default',
                'description' => $category->description,
                'contents_count' => $category->contents_count,
            ])->values()->all();
    }

    private function heroSlidesPayload(): array
    {
        $slides = Slider::query()
            ->with(['media', 'items.media'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->flatMap(fn (Slider $slide) => $slide->slidesPayload()->map(fn (array $item): array => [
                'image' => $item['image'],
                'kicker' => $item['kicker'] ?? null,
                'title' => $item['title'],
                'layout_type' => $slide->layout_type ?: 'default',
                'description' => $item['desc'] ?? null,
                'buttons' => collect($item['buttons'] ?? [])->map(fn (array $button): array => [
                    'text' => $button['text'],
                    'link' => $button['link'],
                    'style' => $button['style'] ?? 'primary',
                ])->values()->all(),
            ]))
            ->values()
            ->all();

        if ($slides !== []) {
            return $slides;
        }

        return [
            [
                'image' => asset('seed/hero-slide-1.png'),
                'kicker' => 'Modern digital experiences',
                'title' => 'Build a beautiful online presence that grows your brand',
                'description' => 'Launch faster with a clean landing page, elegant navigation, and a polished image slider that makes your business stand out.',
                'buttons' => [
                    ['text' => 'Start Project', 'link' => '#', 'style' => 'primary'],
                    ['text' => 'Explore Features', 'link' => '#features', 'style' => 'secondary'],
                ],
            ],
            [
                'image' => asset('seed/hero-slide-2.png'),
                'kicker' => 'Creative and responsive',
                'title' => 'Design that looks premium on every screen',
                'description' => 'Use Tailwind CSS to create responsive layouts, dropdown menus, and eye-catching sections with minimal effort.',
                'buttons' => [
                    ['text' => 'View Demo', 'link' => '#', 'style' => 'primary'],
                    ['text' => 'Talk to Us', 'link' => '#contact', 'style' => 'secondary'],
                ],
            ],
            [
                'image' => asset('seed/hero-slide-3.png'),
                'kicker' => 'Simple. Elegant. Effective.',
                'title' => 'Showcase your services with confidence',
                'description' => 'Present your products, services, and value clearly with a page structure that is clean, modern, and conversion-focused.',
                'buttons' => [
                    ['text' => 'Get Quote', 'link' => '#', 'style' => 'primary'],
                    ['text' => 'Learn More', 'link' => '#features', 'style' => 'secondary'],
                ],
            ],
        ];
    }

    private function contentsPayload(array $allowedVisibilities, int $limit = 12, bool $includeBlocks = false, ?string $type = null, ?string $audience = null, ?string $category = null, ?string $search = null): array
    {
        return Content::query()
            ->with([
                'category',
                'blocks' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            ])
            ->where('status', 'published')
            ->whereIn('visibility', $allowedVisibilities)
            ->when($type !== null, fn ($query) => $query->where('content_type', $type))
            ->when($audience !== null, fn ($query) => $query->where('audience', $audience))
            ->when($category !== null, function ($query) use ($category): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
            })
            ->when($search !== null, function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->limit($limit)
            ->get()
            ->map(function (Content $content) use ($includeBlocks): array {
                $payload = [
                    'id' => $content->id,
                    'title' => $content->title,
                    'slug' => $content->slug,
                    'summary' => $content->summary,
                    'body' => $content->body,
                    'layout_type' => $content->layout_type ?: 'default',
                    'content_type' => $content->content_type,
                    'audience' => $content->audience,
                    'featured_image_url' => $content->featuredImageUrl(),
                    'attachments' => $content->attachmentItems(),
                    'published_at' => optional($content->published_at)->toIso8601String(),
                    'category' => $content->category === null ? null : [
                        'id' => $content->category->id,
                        'name' => $content->category->name,
                        'slug' => $content->category->slug,
                    ],
                ];

                if ($includeBlocks) {
                    $payload['blocks'] = $content->blocks->map(fn ($block): array => [
                        'id' => $block->id,
                        'block_type' => $block->block_type,
                        'layout_type' => $block->layout_type ?: 'default',
                        'title' => $block->title,
                        'body' => $block->body,
                    ])->values()->all();
                }

                return $payload;
            })->values()->all();
    }

    private function settingValue(AppSetting $setting): mixed
    {
        $resolvedValue = $setting->resolvedValue();

        if ($resolvedValue === null) {
            return null;
        }

        return match ($setting->input_type) {
            'boolean' => filter_var($resolvedValue, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($resolvedValue) ? $resolvedValue + 0 : $resolvedValue,
            'json' => is_string($resolvedValue) ? (json_decode($resolvedValue, true) ?? $resolvedValue) : $resolvedValue,
            default => $resolvedValue,
        };
    }

    /**
     * @return array<int, string>
     */
    private function allowedMenuVisibilities(Request $request): array
    {
        return $request->user() === null ? ['public'] : Menu::VISIBILITY_OPTIONS;
    }

    /**
     * @return array<int, string>
     */
    private function allowedContentVisibilities(Request $request): array
    {
        return $request->user() === null ? ['public'] : Content::VISIBILITY_OPTIONS;
    }

    private function menuTree($items, ?int $parentId = null): array
    {
        return $items
            ->where('parent_id', $parentId)
            ->map(function (Menu $item) use ($items): array {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'layout_type' => $item->layout_type ?: 'default',
                    'icon' => $item->icon,
                    'target_reference' => $item->target_reference,
                    'route' => $item->route,
                    'open_in_webview' => $item->open_in_webview,
                    'children' => $this->menuTree($items, $item->id),
                ];
            })
            ->values()
            ->all();
    }
}