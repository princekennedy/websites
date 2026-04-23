<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Quiz;
use App\Models\ServiceCenter;
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
        $faqs = $this->faqsPayload($allowedContentVisibilities);
        $quizzes = Quiz::query()
            ->withCount('questions')
            ->where('status', 'published')
            ->whereIn('visibility', $allowedContentVisibilities)
            ->latest('published_at')
            ->limit(6)
            ->get()
            ->map(fn (Quiz $quiz): array => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'slug' => $quiz->slug,
                'summary' => $quiz->summary,
                'intro_text' => $quiz->intro_text,
                'questions_count' => $quiz->questions_count,
                'audience' => $quiz->audience,
            ])->values()->all();
        $services = $this->serviceCentersPayload($allowedContentVisibilities);
        $settings = AppSetting::query()
            ->where('is_public', true)
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->map(fn (AppSetting $setting): array => [
                'key' => $setting->key,
                'label' => $setting->label,
                'value' => $this->settingValue($setting),
                'group' => $setting->group,
                'input_type' => $setting->input_type,
            ])->values()->all();

        return $this->respond('Application bootstrap loaded successfully.', [
            'menu' => [
                'name' => $menu?->name,
                'location' => $menu?->location,
                'items' => $menu === null ? [] : $this->menuTree($menu->items),
            ],
            'hero_slides' => $this->heroSlidesPayload(),
            'categories' => $categories,
            'featured_contents' => $contents,
            'faqs' => $faqs,
            'quizzes' => $quizzes,
            'services' => $services,
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
                'title' => $block->title,
                'body' => $block->body,
                'sort_order' => $block->sort_order,
            ])->values()->all(),
        ]);
    }

    public function faqs(Request $request): JsonResponse
    {
        $faqs = collect($this->faqsPayload(
            ['public'],
            audience: $request->string('audience')->toString() ?: null,
            category: $request->string('category')->toString() ?: null,
        ));

        return $this->respond('FAQs loaded successfully.', $faqs, ['count' => $faqs->count()]);
    }

    public function quizzes(Request $request): JsonResponse
    {
        $quizzes = Quiz::query()
            ->with([
                'questions' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'questions.options' => fn ($query) => $query->orderBy('sort_order'),
            ])
            ->where('status', 'published')
            ->whereIn('visibility', ['public'])
            ->when($request->filled('audience'), fn ($query) => $query->where('audience', $request->string('audience')->toString()))
            ->orderBy('title')
            ->get()
            ->map(fn (Quiz $quiz) => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'slug' => $quiz->slug,
                'summary' => $quiz->summary,
                'intro_text' => $quiz->intro_text,
                'audience' => $quiz->audience,
                'published_at' => optional($quiz->published_at)->toIso8601String(),
                'questions' => $quiz->questions->map(fn ($question) => [
                    'id' => $question->id,
                    'prompt' => $question->prompt,
                    'help_text' => $question->help_text,
                    'question_type' => $question->question_type,
                    'sort_order' => $question->sort_order,
                    'options' => $question->options->map(fn ($option) => [
                        'id' => $option->id,
                        'option_text' => $option->option_text,
                        'feedback' => $option->feedback,
                        'sort_order' => $option->sort_order,
                    ])->values()->all(),
                ])->values()->all(),
            ])->values();

        return $this->respond('Quizzes loaded successfully.', $quizzes, ['count' => $quizzes->count()]);
    }

    public function serviceCenters(Request $request): JsonResponse
    {
        $services = collect($this->serviceCentersPayload(
            ['public'],
            district: $request->string('district')->toString() ?: null,
            audience: $request->string('audience')->toString() ?: null,
            featuredOnly: $request->boolean('featured'),
        ));

        return $this->respond('Service centers loaded successfully.', $services, ['count' => $services->count()]);
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
        return ContentCategory::query()
            ->where('is_active', true)
            ->withCount([
                'contents' => fn ($query) => $query
                    ->where('status', 'published')
                    ->whereIn('visibility', $allowedVisibilities),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ContentCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'contents_count' => $category->contents_count,
            ])->values()->all();
    }

    private function heroSlidesPayload(): array
    {
        $slides = Slider::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Slider $slide): array => [
                'image' => $slide->imageUrl() ?: asset('seed/hero-slide-1.svg'),
                'kicker' => $slide->kicker,
                'title' => $slide->title,
                'description' => $slide->caption,
                'buttons' => collect([
                    filled($slide->primary_button_text) ? [
                        'text' => $slide->primary_button_text,
                        'link' => $slide->primary_button_link ?: '#',
                        'style' => 'primary',
                    ] : null,
                    filled($slide->secondary_button_text) ? [
                        'text' => $slide->secondary_button_text,
                        'link' => $slide->secondary_button_link ?: '#',
                        'style' => 'secondary',
                    ] : null,
                ])->filter()->values()->all(),
            ])
            ->values()
            ->all();

        if ($slides !== []) {
            return $slides;
        }

        return [
            [
                'image' => asset('seed/hero-slide-1.svg'),
                'kicker' => 'Modern digital experiences',
                'title' => 'Build a beautiful online presence that grows your brand',
                'description' => 'Launch faster with a clean landing page, elegant navigation, and a polished image slider that makes your business stand out.',
                'buttons' => [
                    ['text' => 'Start Project', 'link' => '#', 'style' => 'primary'],
                    ['text' => 'Explore Features', 'link' => '#features', 'style' => 'secondary'],
                ],
            ],
            [
                'image' => asset('seed/hero-slide-2.svg'),
                'kicker' => 'Creative and responsive',
                'title' => 'Design that looks premium on every screen',
                'description' => 'Use Tailwind CSS to create responsive layouts, dropdown menus, and eye-catching sections with minimal effort.',
                'buttons' => [
                    ['text' => 'View Demo', 'link' => '#', 'style' => 'primary'],
                    ['text' => 'Talk to Us', 'link' => '#contact', 'style' => 'secondary'],
                ],
            ],
            [
                'image' => asset('seed/hero-slide-3.svg'),
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
                        'title' => $block->title,
                        'body' => $block->body,
                    ])->values()->all();
                }

                return $payload;
            })->values()->all();
    }

    private function faqsPayload(array $allowedVisibilities, ?string $audience = null, ?string $category = null): array
    {
        return Faq::query()
            ->with('category')
            ->where('is_published', true)
            ->whereIn('visibility', $allowedVisibilities)
            ->when($audience !== null, fn ($query) => $query->where('audience', $audience))
            ->when($category !== null, function ($query) use ($category): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
            })
            ->orderBy('sort_order')
            ->limit(20)
            ->get()
            ->map(fn (Faq $faq): array => [
                'id' => $faq->id,
                'question' => $faq->question,
                'slug' => $faq->slug,
                'answer' => $faq->answer,
                'category' => $faq->category?->name,
                'audience' => $faq->audience,
            ])->values()->all();
    }

    private function serviceCentersPayload(array $allowedVisibilities, ?string $district = null, ?string $audience = null, bool $featuredOnly = false): array
    {
        return ServiceCenter::query()
            ->with('category')
            ->where('is_active', true)
            ->whereIn('visibility', $allowedVisibilities)
            ->when($district !== null, fn ($query) => $query->where('district', $district))
            ->when($audience !== null, fn ($query) => $query->where('audience', $audience))
            ->when($featuredOnly, fn ($query) => $query->where('is_featured', true))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn (ServiceCenter $service): array => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'district' => $service->district,
                'physical_address' => $service->physical_address,
                'summary' => $service->summary,
                'service_hours' => $service->service_hours,
                'contact_phone' => $service->contact_phone,
                'contact_email' => $service->contact_email,
                'services' => $service->services,
                'is_featured' => $service->is_featured,
                'category' => $service->category?->name,
            ])->values()->all();
    }

    private function settingValue(AppSetting $setting): mixed
    {
        if ($setting->value === null) {
            return null;
        }

        return match ($setting->input_type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($setting->value) ? $setting->value + 0 : $setting->value,
            'json' => json_decode($setting->value, true) ?? $setting->value,
            default => $setting->value,
        };
    }

    /**
     * @return array<int, string>
     */
    private function allowedMenuVisibilities(Request $request): array
    {
        return $request->user() === null ? ['public'] : MenuItem::VISIBILITY_OPTIONS;
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
            ->map(function (MenuItem $item) use ($items): array {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type,
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