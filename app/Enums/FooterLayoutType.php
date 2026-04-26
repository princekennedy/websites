<?php

namespace App\Enums;

/**
 * Layouts available under resources/views/designs/footers/.
 * Add a new case here whenever you add a new Blade file to that folder.
 */
enum FooterLayoutType: string
{
    case Default   = 'default';    // designs/footers/default.blade.php
    case Minimal   = 'minimal';    // designs/footers/minimal.blade.php
    case Editorial = 'editorial';  // designs/footers/editorial.blade.php
    case Card      = 'card';       // designs/footers/card.blade.php

    public function label(): string
    {
        return match ($this) {
            self::Default   => 'Default - Dark multi-column footer with links',
            self::Minimal   => 'Minimal - Single-line compact footer',
            self::Editorial => 'Editorial - Masthead-style footer with colophon',
            self::Card      => 'Card - Bordered card footer with columns',
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
