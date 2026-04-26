<?php

namespace App\Enums;

enum DesignLayoutType: string
{
    case Default = 'default';
    case Minimal = 'minimal';
    case Editorial = 'editorial';
    case Card = 'card';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default - Dark gradient hero with section cards',
            self::Minimal => 'Minimal - Clean stacked list layout',
            self::Editorial => 'Editorial - Two-column article-first layout',
            self::Card => 'Card - Grid cards with compact summaries',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return collect(self::cases())
            ->map(fn (self $case): string => $case->value)
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}