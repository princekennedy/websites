<?php

namespace App\Models;

use App\Enums\DesignLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;

    public const VISIBILITY_OPTIONS = ['public', 'private', 'restricted'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'name',
        'slug',
        'description',
        'sort_order',
        'layout_type',
        'location',
        'visibility',
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
            'is_active' => 'boolean',
        ];
    }

    protected function getSlugSourceColumn(): string
    {
        return 'name';
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function normalizedLayoutType(): string
    {
        return DesignLayoutType::tryFrom((string) $this->layout_type)?->value ?? DesignLayoutType::Default->value;
    }
}