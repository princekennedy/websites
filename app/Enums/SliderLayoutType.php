<?php

namespace App\Enums;

/**
 * Layouts available under resources/views/designs/sliders/.
 * Add a new case here whenever you add a new Blade file to that folder.
 */
enum SliderLayoutType: string
{
    case Default   = 'default';    // designs/sliders/default.blade.php
    case Minimal   = 'minimal';    // designs/sliders/minimal.blade.php
    case Editorial = 'editorial';  // designs/sliders/editorial.blade.php
    case Card      = 'card';       // designs/sliders/card.blade.php

    public function label(): string
    {
        return match ($this) {
            self::Default   => 'Default - Full-height image carousel with dots',
            self::Minimal   => 'Minimal - Clean slide strip, no overlay',
            self::Editorial => 'Editorial - Large text-first hero slide',
            self::Card      => 'Card - Compact card-style slide panel',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return collect(self::cases())
            ->map(fn (self $case): string => $case->value)
            ->values()
            ->all();
    }
}
