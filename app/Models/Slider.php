<?php

namespace App\Models;

use App\Enums\SliderLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slider extends Model implements HasMedia
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'title',
        'slug',
        'kicker',
        'layout_type',
        'caption',
        'primary_button_text',
        'primary_button_link',
        'secondary_button_text',
        'secondary_button_link',
        'sort_order',
        'content_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slide_image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
    }

    public function imageUrl(): ?string
    {
        return MediaUrl::first($this, 'slide_image');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SliderItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function normalizedLayoutType(): string
    {
        return SliderLayoutType::tryFrom((string) $this->layout_type)?->value ?? SliderLayoutType::Default->value;
    }

    public function slidesPayload(): Collection
    {
        $items = $this->relationLoaded('items')
            ? $this->items->where('is_active', true)->values()
            : $this->items()->where('is_active', true)->with('media')->get();

        if ($items->isNotEmpty()) {
            return $items
                ->map(fn (SliderItem $item): array => $item->toSlidePayload($this))
                ->values();
        }

        return collect([$this->toSlidePayload()]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSlidePayload(): array
    {
        $buttons = collect([
            filled($this->primary_button_text) ? [
                'text' => $this->primary_button_text,
                'link' => $this->primary_button_link ?: '#',
                'class' => 'bg-indigo-600 hover:bg-indigo-700',
                'style' => 'primary',
            ] : null,
            filled($this->secondary_button_text) ? [
                'text' => $this->secondary_button_text,
                'link' => $this->secondary_button_link ?: '#',
                'class' => 'border border-white/60 bg-white/10 hover:bg-white/20',
                'style' => 'secondary',
            ] : null,
        ])
            ->filter()
            ->values()
            ->all();

        return [
            'title' => $this->title,
            'kicker' => $this->kicker,
            'desc' => $this->caption,
            'image' => $this->imageUrl() ?: asset('seed/hero-slide-1.png'),
            'buttons' => $buttons,
        ];
    }
}