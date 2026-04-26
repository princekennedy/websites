<?php

namespace App\Enums;

enum MenuLayoutType: string
{
    case Default = 'default';
    case Tabs    = 'tabs';
    case Pills   = 'pills';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default — Horizontal nav with dropdown submenus',
            self::Tabs    => 'Tabs — Underline tab-style navigation bar',
            self::Pills   => 'Pills — Rounded pill-style navigation bar',
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
