<?php

namespace App\Support;

use App\Enums\CategoryLayoutType;
use App\Enums\ContentLayoutType;
use App\Enums\FooterLayoutType;
use App\Enums\HeaderLayoutType;
use App\Enums\MenuItemLayoutType;
use App\Enums\MenuLayoutType;
use App\Enums\SliderLayoutType;

/**
 * Central registry mapping each design section to its layout enum.
 *
 * This is the single source of truth for all available layouts.
 *
 * ┌──────────────────────┬────────────────────────────────────────────────────┐
 * │ Section key          │ Enum class                                         │
 * ├──────────────────────┼────────────────────────────────────────────────────┤
 * │ content              │ App\Enums\ContentLayoutType                        │
 * │ content-categories   │ App\Enums\CategoryLayoutType                       │
 * │ menu-items           │ App\Enums\MenuItemLayoutType                       │
 * │ sliders              │ App\Enums\SliderLayoutType                         │
 * │ menus (chrome)       │ App\Enums\MenuLayoutType                           │
 * │ headers              │ App\Enums\HeaderLayoutType                         │
 * │ footers              │ App\Enums\FooterLayoutType                         │
 * └──────────────────────┴────────────────────────────────────────────────────┘
 *
 * Adding a new design:
 *   1. Add a new Blade file under resources/views/designs/{section}/
 *   2. Add a matching case to the enum listed above for that section
 *   3. The dropdown options and validation rules update automatically
 */
final class DesignLayouts
{
    /**
     * Map of section folder name → fully-qualified enum class-string.
     *
     * @var array<string, class-string>
     */
    private const MAP = [
        'content'            => ContentLayoutType::class,
        'content-categories' => CategoryLayoutType::class,
        'menu-items'         => MenuItemLayoutType::class,
        'sliders'            => SliderLayoutType::class,
        'menus'              => MenuLayoutType::class,
        'headers'            => HeaderLayoutType::class,
        'footers'            => FooterLayoutType::class,
    ];

    /**
     * All registered section → enum-class mappings.
     *
     * @return array<string, class-string>
     */
    public static function sections(): array
    {
        return self::MAP;
    }

    /**
     * Return the enum class for a given section key.
     *
     * @return class-string|null
     */
    public static function enumClass(string $section): ?string
    {
        return self::MAP[$section] ?? null;
    }

    /**
     * Return [ value => label ] pairs for dropdown <select> options.
     *
     * @return array<string, string>
     */
    public static function options(string $section): array
    {
        $class = self::enumClass($section);

        return $class ? $class::options() : [];
    }

    /**
     * Return a flat list of valid values for form-request validation.
     *
     * @return array<int, string>
     */
    public static function values(string $section): array
    {
        $class = self::enumClass($section);

        return $class ? $class::values() : [];
    }

    // ── Convenience accessors ──────────────────────────────────────────────

    /** @return array<string, string> */
    public static function contentOptions(): array
    {
        return ContentLayoutType::options();
    }

    /** @return array<int, string> */
    public static function contentValues(): array
    {
        return ContentLayoutType::values();
    }

    /** @return array<string, string> */
    public static function categoryOptions(): array
    {
        return CategoryLayoutType::options();
    }

    /** @return array<int, string> */
    public static function categoryValues(): array
    {
        return CategoryLayoutType::values();
    }

    /** @return array<string, string> */
    public static function menuOptions(): array
    {
        return MenuLayoutType::options();
    }

    /** @return array<int, string> */
    public static function menuValues(): array
    {
        return MenuLayoutType::values();
    }

    /** @return array<string, string> */
    public static function menuItemOptions(): array
    {
        return MenuItemLayoutType::options();
    }

    /** @return array<int, string> */
    public static function menuItemValues(): array
    {
        return MenuItemLayoutType::values();
    }

    /** @return array<string, string> */
    public static function sliderOptions(): array
    {
        return SliderLayoutType::options();
    }

    /** @return array<int, string> */
    public static function sliderValues(): array
    {
        return SliderLayoutType::values();
    }

    /** @return array<string, string> */
    public static function headerOptions(): array
    {
        return HeaderLayoutType::options();
    }

    /** @return array<int, string> */
    public static function headerValues(): array
    {
        return HeaderLayoutType::values();
    }

    /** @return array<string, string> */
    public static function footerOptions(): array
    {
        return FooterLayoutType::options();
    }

    /** @return array<int, string> */
    public static function footerValues(): array
    {
        return FooterLayoutType::values();
    }
}
