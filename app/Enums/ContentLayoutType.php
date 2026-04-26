<?php

namespace App\Enums;

enum ContentLayoutType: string
{
    case Default = 'default';
    case Magazine = 'magazine';
    case Card = 'card';
    case Minimal = 'minimal';

    public function label(): string
    {
        return match ($this) {
            self::Default  => 'Default — Dark hero with prose body',
            self::Magazine => 'Magazine — Bold image header with column layout',
            self::Card     => 'Card — Contained card with sidebar',
            self::Minimal  => 'Minimal — Clean light layout, no hero',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
