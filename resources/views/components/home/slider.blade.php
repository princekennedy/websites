@php
    $sliderModels = \Illuminate\Support\Facades\Schema::hasTable('sliders')
        ? \App\Models\Slider::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
        : collect();

    $slides = $sliderModels
        ->map(function (\App\Models\Slider $slider): array {
            $buttons = collect([
                filled($slider->primary_button_text) ? [
                    'text' => $slider->primary_button_text,
                    'link' => $slider->primary_button_link ?: '#',
                    'class' => 'bg-indigo-600 hover:bg-indigo-700',
                ] : null,
                filled($slider->secondary_button_text) ? [
                    'text' => $slider->secondary_button_text,
                    'link' => $slider->secondary_button_link ?: '#',
                    'class' => 'border border-white/60 bg-white/10 hover:bg-white/20',
                ] : null,
            ])
                ->filter()
                ->values()
                ->all();

            return [
                'title' => $slider->title,
                'kicker' => $slider->kicker,
                'desc' => $slider->caption,
                'image' => $slider->imageUrl() ?: asset('seed/hero-slide-1.svg'),
                'buttons' => $buttons,
            ];
        })
        ->values();

    $requestedLayout = $sliderModels->first()?->normalizedLayoutType() ?? \App\Enums\DesignLayoutType::Default->value;
    $sliderDesignView = 'designs.sliders.'.$requestedLayout;
@endphp

@include(view()->exists($sliderDesignView) ? $sliderDesignView : 'designs.sliders.default', ['slides' => $slides])