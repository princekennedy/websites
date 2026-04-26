<?php

namespace App\Models;

use App\Enums\DesignLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use App\Models\Content;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentCategory extends Model
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'menu_item_id',
        'name',
        'visibility',
        'slug',
        'layout_type',
        'description',
        'sort_order',
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

    public function normalizedLayoutType(): string
    {
        return DesignLayoutType::tryFrom((string) $this->layout_type)?->value ?? DesignLayoutType::Default->value;
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'category_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}