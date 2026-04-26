<?php

namespace App\Models;

use App\Enums\ContentLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use App\Models\ContentBlock;
use App\Models\ContentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Content extends Model implements HasMedia
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;
    use InteractsWithMedia;

    public const TYPE_OPTIONS = ['page', 'article', 'faq', 'quiz', 'service', 'referral'];

    public const STATUS_OPTIONS = ['draft', 'review', 'published', 'archived'];

    public const AUDIENCE_OPTIONS = ['general', 'adolescents', 'youth', 'providers'];

    public const VISIBILITY_OPTIONS = ['public', 'private', 'restricted'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'title',
        'slug',
        'layout_type',
        'summary',
        'body',
        'content_type',
        'category_id',
        'status',
        'audience',
        'visibility',
        'featured_image_path',
        'published_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class)->orderBy('sort_order');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
    }

    public function featuredImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('featured_image') ?: $this->featured_image_path;
    }

    public function attachmentItems(): array
    {
        return $this->getMedia('attachments')
            ->map(fn (Media $media): array => [
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => $media->getUrl(),
            ])
            ->values()
            ->all();
    }

    public function normalizedLayoutType(): string
    {
        return ContentLayoutType::tryFrom((string) $this->layout_type)?->value ?? ContentLayoutType::Default->value;
    }
}