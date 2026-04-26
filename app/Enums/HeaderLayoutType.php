<?php

namespace App\Enums;

/**
 * Layouts available under resources/views/designs/headers/.
 * Add a new case here whenever you add a new Blade file to that folder.
 */
enum HeaderLayoutType: string
{
    case Default   = 'default';    // designs/headers/default.blade.php
    case Minimal   = 'minimal';    // designs/headers/minimal.blade.php
    case Editorial = 'editorial';  // designs/headers/editorial.blade.php
    case Card      = 'card';       // designs/headers/card.blade.php

    public function label(): string
    {
        return match ($this) {
            self::Default   => 'Default - Dark gradient header with full navigation',
            self::Minimal   => 'Minimal - Clean light header, compact links',
            self::Editorial => 'Editorial - Article-masthead style header',
            self::Card      => 'Card - Bordered card-style header panel',
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
