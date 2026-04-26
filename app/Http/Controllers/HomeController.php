<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Support\PublicNavigation;
use App\Support\PublicSiteConfig;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class HomeController extends Controller
{
    public function welcome(PublicNavigation $navigation, PublicSiteConfig $siteConfig): View
    {
        try {
            $hasCmsTables = Schema::hasTable('contents')
                && Schema::hasTable('content_categories')
                && Schema::hasTable('menus');

            $primaryMenuItems = $navigation->items();
            $menuHighlights = $this->menuHighlights($navigation->quickLinks(limit: 6));
            $featuredContents = $hasCmsTables
                ? Content::query()
                    ->with('category')
                    ->where('status', 'published')
                    ->where('visibility', 'public')
                    ->latest('published_at')
                    ->limit(6)
                    ->get()
                : new Collection();
            $categories = $hasCmsTables
                ? ContentCategory::query()
                    ->where('is_active', true)
                    ->withCount([
                        'contents' => fn ($query) => $query
                            ->where('status', 'published')
                            ->where('visibility', 'public'),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(6)
                    ->get()
                : new Collection();
            $siteConfigData = $siteConfig->data();
        } catch (Throwable $exception) {
            report($exception);

            $primaryMenuItems = collect();
            $menuHighlights = collect();
            $featuredContents = new Collection();
            $categories = new Collection();
            $siteConfigData = [
                'brand' => [
                    'name' => config('app.name', 'SRHR Connect'),
                    'message' => 'Trusted SRHR guidance and support access in one place.',
                ],
                'homepage' => ['slides' => []],
            ];
        }

        return view('welcome', [
            'featuredContents' => $featuredContents,
            'categories' => $categories,
            'primaryMenuItems' => $primaryMenuItems,
            'menuHighlights' => $menuHighlights,
            'coreMenuItems' => $menuHighlights->take(4)->values(),
            'heroSlides' => $this->heroSlides($featuredContents, $menuHighlights, $siteConfigData),
            'siteConfig' => $siteConfigData,
        ]);
    }

    public function admin(PublicNavigation $navigation, PublicSiteConfig $siteConfig): View
    {
        try {
            $hasCmsTables = Schema::hasTable('contents')
                && Schema::hasTable('content_categories')
                && Schema::hasTable('menus');

            $primaryMenuItems = $navigation->items();
            $menuHighlights = $this->menuHighlights($navigation->quickLinks(limit: 6));
            $featuredContents = $hasCmsTables
                ? Content::query()
                    ->with('category')
                    ->where('status', 'published')
                    ->where('visibility', 'public')
                    ->latest('published_at')
                    ->limit(6)
                    ->get()
                : new Collection();
            $categories = $hasCmsTables
                ? ContentCategory::query()
                    ->where('is_active', true)
                    ->withCount([
                        'contents' => fn ($query) => $query
                            ->where('status', 'published')
                            ->where('visibility', 'public'),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(6)
                    ->get()
                : new Collection();
            $siteConfigData = $siteConfig->data();
        } catch (Throwable $exception) {
            report($exception);

            $primaryMenuItems = collect();
            $menuHighlights = collect();
            $featuredContents = new Collection();
            $categories = new Collection();
            $siteConfigData = [
                'brand' => [
                    'name' => config('app.name', 'SRHR Connect'),
                    'message' => 'Trusted SRHR guidance and support access in one place.',
                ],
                'homepage' => ['slides' => []],
            ];
        }

        return view('admin', [
            'featuredContents' => $featuredContents,
            'categories' => $categories,
            'primaryMenuItems' => $primaryMenuItems,
            'menuHighlights' => $menuHighlights,
            'coreMenuItems' => $menuHighlights->take(4)->values(),
            'heroSlides' => $this->heroSlides($featuredContents, $menuHighlights, $siteConfigData),
            'siteConfig' => $siteConfigData,
        ]);
    }


    private function menuHighlights(Collection $items): Collection
    {
        return $items
            ->filter(fn (array $item): bool => filled($item['href'] ?? null))
            ->values()
            ->map(function (array $item, int $index): array {
                $title = $item['title'] ?? 'Public pathway';

                return [
                    'title' => $title,
                    'href' => $item['href'],
                    'index' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'initials' => $this->initials($title),
                    'description' => $this->menuDescription($title),
                ];
            });
    }

    private function menuDescription(string $title): string
    {
        $value = Str::of($title)->lower();

        return match (true) {
            $value->contains(['faq', 'question']) => 'Review common questions and short, trusted answers from the published knowledge base.',
            $value->contains(['quiz', 'check-in']) => 'Open an interactive learning check-in and connect it to follow-up guidance.',
            $value->contains(['service', 'help', 'support', 'referral']) => 'Move from content into support, referral, and service discovery without friction.',
            $value->contains(['body', 'topic', 'category']) => 'Browse a structured subject pathway with published SRHR guidance and learning content.',
            $value->contains(['guide', 'content', 'article']) => 'Read published public guidance managed directly from the CMS workspace.',
            default => 'Open this CMS-configured public pathway directly from the backend-managed menu.',
        };
    }

    private function initials(string $title): string
    {
        $letters = Str::of($title)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $segment): string => Str::upper(Str::substr($segment, 0, 1)))
            ->implode('');

        return $letters !== '' ? $letters : 'SR';
    }

    private function heroSlides(Collection $featuredContents, Collection $menuHighlights, array $siteConfig): Collection
    {
        $slideImages = collect(data_get($siteConfig, 'homepage.slides', []))
            ->map(fn (array $slide): ?string => filled($slide['image_url'] ?? null) ? $slide['image_url'] : null)
            ->values();

        $contentSlides = $featuredContents
            ->take(4)
            ->map(function (Content $content, int $index) use ($slideImages): array {
                return [
                    'eyebrow' => 'Featured guidance',
                    'title' => $content->title,
                    'description' => $content->summary ?: Str::limit(strip_tags((string) $content->body), 150),
                    'href' => route('public.contents.show', $content),
                    'cta' => 'Read this page',
                    'meta' => $content->category?->name ?? 'General',
                    'index' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'image_url' => $slideImages->get($index) ?: $content->featuredImageUrl(),
                ];
            });

        if ($contentSlides->isNotEmpty()) {
            return $contentSlides->values();
        }

        $menuSlides = $menuHighlights
            ->take(4)
            ->map(function (array $item, int $index) use ($slideImages): array {
                return [
                    'eyebrow' => 'Configured pathway',
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'href' => $item['href'],
                    'cta' => 'Open pathway',
                    'meta' => 'Public menu',
                    'index' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'image_url' => $slideImages->get($index),
                ];
            });

        if ($menuSlides->isNotEmpty()) {
            return $menuSlides->values();
        }

        return collect([[
            'eyebrow' => 'Public platform',
            'title' => data_get($siteConfig, 'brand.name', 'SRHR Connect'),
            'description' => data_get($siteConfig, 'brand.message', 'Trusted SRHR guidance and support access in one place.'),
            'href' => route('public.contents.index'),
            'cta' => 'Browse guidance',
            'meta' => 'CMS ready',
            'index' => '01',
            'image_url' => $slideImages->first(),
        ]]);
    }
}