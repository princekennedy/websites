<?php

namespace App\Enums;

/**
 * Controls horizontal alignment of navigation links in the public header.
 * Add a new case here and create the corresponding CSS utility mapping.
 */
enum MenuAlignmentType: string
{
    case Left   = 'left';
    case Center = 'center';
    case Right  = 'right';
    case Spread = 'spread';

    public function label(): string
    {
        return match ($this) {
            self::Left   => 'Left - Links grouped to the left side',
            self::Center => 'Centered - Links grouped in the center',
            self::Right  => 'Right-aligned - Links grouped to the right side',
            self::Spread => 'Spread - Links distributed evenly across the full width',
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
