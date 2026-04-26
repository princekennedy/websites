<?php

namespace App\Models;

use App\Enums\MenuItemLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\ContentCategory;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use BelongsToWebsite;
    use HasFactory;

    public const VISIBILITY_OPTIONS = ['public', 'private', 'restricted'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'menu_id',
        'parent_id',
        'title',
        'layout_type',
        'target_reference',
        'route',
        'icon',
        'sort_order',
        'visibility',
        'open_in_webview',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'open_in_webview' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ContentCategory::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public static function normalizeForPersistence(array $attributes): array
    {
        $rawLayoutType = trim((string) ($attributes['layout_type'] ?? MenuItemLayoutType::Default->value));
        $layoutType = MenuItemLayoutType::tryFrom($rawLayoutType)?->value ?? MenuItemLayoutType::Default->value;
        $route = static::normalizeNullableString($attributes['route'] ?? null);
        $targetReference = static::normalizeNullableString($attributes['target_reference'] ?? null);
        $openInWebview = static::normalizeBoolean($attributes['open_in_webview'] ?? false);

        if ($route === null && ! static::isExternalTarget($targetReference)) {
            $title = $attributes['title'] ?? '';
            $slug = Str::slug($title);
            $menuItemName = $slug !== '' ? $slug : 'item';
            $route = '/menu-item/'.$menuItemName;
        }

        return [
            ...$attributes,
            'layout_type' => $layoutType,
            'route' => $route,
            'target_reference' => $targetReference,
            'open_in_webview' => $openInWebview,
        ];
    }

    public function publicPageSlug(): string
    {
        $slug = Str::slug($this->title);

        return $slug !== '' ? $slug : 'menu-item-'.$this->getKey();
    }

    public function normalizedLayoutType(): string
    {
        return MenuItemLayoutType::tryFrom((string) $this->layout_type)?->value ?? MenuItemLayoutType::Default->value;
    }

    private static function normalizeNullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private static function normalizeBoolean(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => $value === 1,
            default => in_array(Str::lower(trim((string) $value)), ['1', 'true', 'on', 'yes'], true),
        };
    }

    private static function isExternalTarget(?string $target): bool
    {
        return filled($target) && Str::startsWith($target, ['http://', 'https://', 'mailto:', 'tel:']);
    }
}