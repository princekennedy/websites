<?php

namespace App\Enums;

enum CategoryLayoutType: string
{
    case Default  = 'default';
    case Featured = 'featured';
    case List     = 'list';

    public function label(): string
    {
        return match ($this) {
            self::Default  => 'Default — Icon cards in a responsive grid',
            self::Featured => 'Featured — Large spotlight card + content grid',
            self::List     => 'List — Horizontal rows with description and count',
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
