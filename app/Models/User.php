<?php

namespace App\Models;

use App\Models\Website;
use App\Models\UserWebsite;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'current_website_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasAdminCmsRole(): bool
    {
        if (! Schema::hasTable('roles')) {
            return false;
        }

        return $this->hasAnyRole(['admin', 'super-admin']);
    }

    public function canAccessCms(): bool
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return false;
        }

        if (! $this->hasAdminCmsRole()) {
            return false;
        }

        return $this->hasPermissionTo('cms.access');
    }

    public function hasCmsPermission(string $permission): bool
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return false;
        }

        if (! Permission::query()->where('name', $permission)->where('guard_name', 'web')->exists()) {
            return false;
        }

        return $this->hasPermissionTo($permission);
    }

    public function canManageAnyCmsModule(): bool
    {
        foreach ([
            'cms.manage.categories',
            'cms.manage.contents',
            'cms.manage.faqs',
            'cms.manage.quizzes',
            'cms.manage.services',
            'cms.manage.menus',
            'cms.manage.settings',
        ] as $permission) {
            if ($this->hasCmsPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function currentWebsite(): BelongsTo
    {
        return $this->belongsTo(Website::class, 'current_website_id');
    }

    public function ownedWebsites(): HasMany
    {
        return $this->hasMany(Website::class, 'created_by');
    }

    public function websiteMemberships(): HasMany
    {
        return $this->hasMany(UserWebsite::class);
    }

    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class, 'user_websites')
            ->withPivot(['role', 'is_owner'])
            ->withTimestamps();
    }

    public function switchToWebsite(?Website $website): void
    {
        $targetWebsite = $website;

        if ($targetWebsite !== null && ! $this->websites()->whereKey($targetWebsite->getKey())->exists()) {
            $targetWebsite = null;
        }

        if ($targetWebsite === null) {
            $targetWebsite = $this->websites()->orderBy('name')->first();
        }

        $this->forceFill([
            'current_website_id' => $targetWebsite?->getKey(),
        ])->save();
    }
}
