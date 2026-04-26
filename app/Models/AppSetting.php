<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWebsite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppSetting extends Model implements HasMedia
{
    use BelongsToWebsite;
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'website_id',
        'key',
        'label',
        'value',
        'layout_type',
        'group',
        'input_type',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('setting_asset');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
    }

    public static function seedDefaultsForWebsite(Website $website): void
    {
        $settings = [
            ['key' => 'app_name', 'label' => 'App name', 'value' => 'SRHR Connect', 'group' => 'branding', 'input_type' => 'text', 'description' => 'Primary app label shown across the Android experience.', 'is_public' => true],
            ['key' => 'welcome_message', 'label' => 'Welcome message', 'value' => 'Private, youth-friendly SRHR guidance and service access in one place.', 'group' => 'branding', 'input_type' => 'textarea', 'description' => 'Public summary shown in the mobile client.', 'is_public' => true],
            ['key' => 'support_phone', 'label' => 'Support phone', 'value' => '+265 999 700 800', 'group' => 'support', 'input_type' => 'text', 'description' => 'Primary support line for urgent guidance and referrals.', 'is_public' => true],
            ['key' => 'support_email', 'label' => 'Support email', 'value' => 'support@srhr.test', 'group' => 'support', 'input_type' => 'email', 'description' => 'Primary support email surfaced to app users.', 'is_public' => true],
            ['key' => 'theme_mode', 'label' => 'Theme mode', 'value' => 'light', 'group' => 'theme', 'input_type' => 'text', 'description' => 'Default visual theme for public clients.', 'is_public' => true],
            ['key' => 'theme_accent', 'label' => 'Theme accent', 'value' => '#34d399', 'group' => 'theme', 'input_type' => 'text', 'description' => 'Primary accent color used across the experience.', 'is_public' => true],
            ['key' => 'onboarding_requires_registration', 'label' => 'Require registration for person space', 'value' => '1', 'group' => 'features', 'input_type' => 'boolean', 'description' => 'Whether personalized features require account creation.', 'is_public' => true],
            ['key' => 'cms_version_note', 'label' => 'CMS version note', 'value' => 'Comprehensive module set enabled for content, FAQs, quizzes, services, menus, and runtime settings.', 'group' => 'operations', 'input_type' => 'textarea', 'description' => 'Operational note for admins.', 'is_public' => false],
        ];

        foreach ($settings as $settingData) {
            self::query()->updateOrCreate(
                ['website_id' => $website->id, 'key' => $settingData['key']],
                [...$settingData, 'website_id' => $website->id],
            );
        }
    }
}