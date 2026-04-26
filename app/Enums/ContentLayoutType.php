<?php

namespace App\Enums;

/**
 * Layouts available under resources/views/designs/content/.
 * Add a new case here whenever you add a new Blade file to that folder.
 */
enum ContentLayoutType: string
{
    case Default   = 'default';    // designs/content/default.blade.php
    case Minimal   = 'minimal';    // designs/content/minimal.blade.php
    case Editorial = 'editorial';  // designs/content/editorial.blade.php
    case Card      = 'card';       // designs/content/card.blade.php

    public function label(): string
    {
        return match ($this) {
            self::Default   => 'Default - Dark gradient hero with prose body',
            self::Minimal   => 'Minimal - Clean light column, no hero',
            self::Editorial => 'Editorial - Two-column article-first layout',
            self::Card      => 'Card - Contained card with sidebar',
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
