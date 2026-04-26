<?php

namespace App\Models;

use App\Enums\DesignLayoutType;
use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Website extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'is_active',
        'created_by',
    ];

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_websites')
            ->withPivot(['role', 'is_owner'])
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(UserWebsite::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ContentCategory::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(AppSetting::class);
    }

    public function ensureDefaultHomeMenu(): Menu
    {
        return Menu::query()->updateOrCreate(
            [
                'website_id' => $this->id,
                'slug' => 'home',
            ],
            [
                'website_id' => $this->id,
                'name' => 'Home',
                'description' => 'Default landing page for this website workspace.',
                'sort_order' => 1,
                'layout_type' => DesignLayoutType::Default->value,
                'location' => 'public-primary',
                'visibility' => 'public',
                'is_active' => true,
            ],
        );
    }
}
