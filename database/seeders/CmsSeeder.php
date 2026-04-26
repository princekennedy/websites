<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Content;
use App\Models\ContentBlock;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Slider;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect([
            'cms.access',
            'cms.manage.categories',
            'cms.manage.contents',
            'cms.manage.sliders',
            'cms.manage.menus',
            'cms.manage.settings',
            'app.access.person-space',
            'app.view.private-content',
            'app.save.favorites',
            'app.ask-questions',
        ])->map(fn (string $permission): Permission => Permission::query()->updateOrCreate(
            ['name' => $permission, 'guard_name' => 'web'],
            ['name' => $permission, 'guard_name' => 'web'],
        ));

        $adminRole = Role::query()->updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
        );
        $adminRole->syncPermissions($permissions);

        $superAdminRole = Role::query()->updateOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'super-admin', 'guard_name' => 'web'],
        );
        $superAdminRole->syncPermissions($permissions);

        $appUserRole = Role::query()->updateOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        );
        $appUserRole->syncPermissions([
            'app.access.person-space',
            'app.view.private-content',
            'app.save.favorites',
            'app.ask-questions',
        ]);

        Role::query()->where('name', 'app-user')->where('guard_name', 'web')->delete();

        $samplePlatformName = config('app.name', 'Sample Platform');

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'super-admin@srhr.test'],
            [
                'name' => $samplePlatformName.' Super Admin',
                'password' => Hash::make('password'),
                'phone' => '0992383848',
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles([$superAdminRole]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@srhr.test'],
            [
                'name' => $samplePlatformName.' Admin',
                'phone' => '0992383842',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles([$adminRole]);

        $demoYouth = User::query()->updateOrCreate(
            ['email' => 'user@srhr.test'],
            [
                'name' => $samplePlatformName.' User',
                'phone' => '0992383841',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $demoYouth->syncRoles([$appUserRole]);

        $website = Website::query()->updateOrCreate(
            ['slug' => 'default-website'],
            [
                'name' => 'Default Website',
                'domain' => null,
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ],
        );

        foreach ([$superAdmin, $admin, $demoYouth] as $user) {
            $user->websites()->syncWithoutDetaching([
                $website->id => [
                    'role' => in_array($user->email, ['super-admin@srhr.test', 'admin@srhr.test'], true) ? 'owner' : 'member',
                    'is_owner' => in_array($user->email, ['super-admin@srhr.test', 'admin@srhr.test'], true),
                ],
            ]);

            $user->forceFill([
                'current_website_id' => $website->id,
            ])->save();
        }

        $categories = collect([
            [
                'name' => 'Website Builder',
                'description' => 'Guidance for creating websites fast with reusable layouts, sections, and content structures.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Website Management',
                'description' => 'Operational content for managing menus, pages, sliders, and settings from the CMS.',
                'sort_order' => 2,
            ],
        ])->mapWithKeys(function (array $category) use ($website) {
            $model = ContentCategory::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($category['name'])->slug()->value()],
                [...$category, 'website_id' => $website->id, 'is_active' => true],
            );

            return [$category['name'] => $model];
        });

        $contents = collect([
            [
                'title' => 'Home',
                'content_type' => 'page',
                'sort_order' => '1',
                'category' => 'Website Builder',
                'summary' => 'Build websites quickly with reusable sections, modern templates, and content-first workflows.',
                'body' => 'This platform helps teams build and launch websites faster. Create pages, structure menus, manage media, and publish updates from one CMS workspace designed for speed and clarity.',
                'audience' => 'general',
            ],
            [
                'title' => 'About',
                'content_type' => 'page',
                'sort_order' => '2',
                'category' => 'Website Management',
                'summary' => 'A flexible website builder and management platform for content teams and administrators.',
                'body' => 'Use this platform to design web experiences, organize navigation, and manage content lifecycle end to end. It is built to support multi-website workspaces with clear operational controls.',
                'audience' => 'general',
            ],
            [
                'title' => 'Build Pages With Dynamic Sections',
                'content_type' => 'article',
                'category' => 'Website Builder',
                'summary' => 'Combine reusable sections and layout variants to compose pages without hard-coding each screen.',
                'body' => 'Editors can assemble dynamic pages by linking menus to published content and selecting layout types per section. This approach reduces duplication and improves maintainability.',
                'audience' => 'general',
            ],
            [
                'title' => 'Manage Menus and Content From CMS',
                'content_type' => 'article',
                'category' => 'Website Management',
                'summary' => 'Keep navigation, pages, and settings aligned across websites with centralized CMS controls.',
                'body' => 'Administrators can create menus, control visibility, maintain structure, and publish content changes with confidence. Multi-site operations stay consistent through shared governance patterns.',
                'audience' => 'general',
            ],
        ])->mapWithKeys(function (array $entry) use ($categories, $admin, $website) {
            $category = $categories[$entry['category']];

            $content = Content::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($entry['title'])->slug()->value()],
                [
                    'website_id' => $website->id,
                    'title' => $entry['title'],
                    'summary' => $entry['summary'],
                    'body' => $entry['body'],
                    'content_type' => $entry['content_type'],
                    'category_id' => $category->id,
                    'status' => 'published',
                    'audience' => $entry['audience'],
                    'visibility' => 'public',
                    'published_at' => Carbon::now(),
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            ContentBlock::query()->updateOrCreate(
                [
                    'website_id' => $website->id,
                    'content_id' => $content->id,
                    'block_type' => 'rich_text',
                    'sort_order' => 1,
                ],
                [
                    'website_id' => $website->id,
                    'title' => 'Intro block',
                    'body' => $entry['body'],
                    'is_active' => true,
                ],
            );

            return [$entry['title'] => $content];
        });

        AppSetting::seedDefaultsForWebsite($website);

        $sliderRecords = [
            [
                'title' => 'Build a beautiful online presence that grows your brand',
                'kicker' => 'Modern digital experiences',
                'caption' => 'Launch faster with a clean landing page, elegant navigation, and a polished image slider that makes your business stand out.',
                'primary_button_text' => 'Start Project',
                'primary_button_link' => '/get-started',
                'secondary_button_text' => 'Explore Features',
                'secondary_button_link' => '/features',
                'sort_order' => 1,
                'asset' => base_path('public/seed/hero-slide-1.png'),
            ],
            [
                'title' => 'Design that looks premium on every screen',
                'kicker' => 'Creative and responsive',
                'caption' => 'Use Tailwind CSS to create responsive layouts, dropdown menus, and eye-catching sections with minimal effort.',
                'primary_button_text' => 'View Demo',
                'primary_button_link' => '/demo',
                'secondary_button_text' => 'Talk to Us',
                'secondary_button_link' => '/contact',
                'sort_order' => 2,
                'asset' => base_path('public/seed/hero-slide-2.png'),
            ],
            [
                'title' => 'Showcase your services with confidence',
                'kicker' => 'Simple. Elegant. Effective.',
                'caption' => 'Present your products, services, and value clearly with a page structure that is clean, modern, and conversion-focused.',
                'primary_button_text' => 'Get Quote',
                'primary_button_link' => '/pricing',
                'secondary_button_text' => 'Learn More',
                'secondary_button_link' => '/about',
                'sort_order' => 3,
                'asset' => base_path('public/seed/hero-slide-3.png'),
            ],
        ];

        foreach ($sliderRecords as $sliderData) {
            $slider = Slider::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => str($sliderData['title'])->slug()->value()],
                [
                    'website_id' => $website->id,
                    'title' => $sliderData['title'],
                    'kicker' => $sliderData['kicker'],
                    'layout_type' => 'default',
                    'caption' => $sliderData['caption'],
                    'primary_button_text' => $sliderData['primary_button_text'],
                    'primary_button_link' => $sliderData['primary_button_link'],
                    'secondary_button_text' => $sliderData['secondary_button_text'],
                    'secondary_button_link' => $sliderData['secondary_button_link'],
                    'sort_order' => $sliderData['sort_order'],
                    'is_active' => true,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            if ($slider->getFirstMedia('slide_image') === null && is_file($sliderData['asset'])) {
                $slider->addMedia($sliderData['asset'])->preservingOriginal()->toMediaCollection('slide_image');
            }
        }

        $publicMenus = [
            [
                'slug' => 'home',
                'name' => 'Home',
                'description' => 'Landing page for website building and management workflows.',
                'sort_order' => 1,
                'items' => [],
            ],
            [
                'slug' => 'about',
                'name' => 'About',
                'description' => 'About this platform and how it helps teams build and manage websites.',
                'sort_order' => 2,
                'items' => [],
            ],
        ];

        foreach ($publicMenus as $menuData) {
            $menu = Menu::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => $menuData['slug']],
                [
                    'website_id' => $website->id,
                    'name' => $menuData['name'],
                    'description' => $menuData['description'],
                    'sort_order' => (int) ($menuData['sort_order'] ?? 0),
                    'layout_type' => 'default',
                    'location' => 'public-primary',
                    'visibility' => 'public',
                    'is_active' => true,
                ],
            );

            foreach ($menuData['items'] as $item) {
                $slug = Str::slug($item['title']);
                $menuItemName = $slug !== '' ? $slug : 'item';
                $route = $item['route'] ?? '/menu-item/'.$menuItemName;

                $menu->items()->updateOrCreate(
                    ['website_id' => $website->id, 'title' => $item['title']],
                    MenuItem::normalizeForPersistence([
                        'website_id' => $website->id,
                        'layout_type' => $item['layout_type'] ?? 'default',
                        'target_reference' => $item['target_reference'] ?? null,
                        'route' => $item['route'] ?? $route,
                        'icon' => $item['icon'] ?? null,
                        'sort_order' => $item['sort_order'],
                        'visibility' => 'public',
                        'open_in_webview' => $item['open_in_webview'] ?? false,
                        'is_active' => true,
                    ]),
                );
            }
        }
    }
}