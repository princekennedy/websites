<?php

namespace App\Enums;

enum SliderLayoutType: string
{
    case Default  = 'default';
    case Split    = 'split';
    case Centered = 'centered';

    public function label(): string
    {
        return match ($this) {
            self::Default  => 'Default — Full-width dark gradient with left-aligned text',
            self::Split    => 'Split — Image right, text left two-column layout',
            self::Centered => 'Centered — Center-aligned text over subtle background',
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
