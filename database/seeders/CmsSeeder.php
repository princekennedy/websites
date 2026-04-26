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
                'name' => 'System Managed Builder Website',
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

        // Reset seed-driven website data so reseeding reflects the latest documentation structure.
        MenuItem::query()->where('website_id', $website->id)->delete();
        Menu::query()->where('website_id', $website->id)->delete();
        Content::query()->where('website_id', $website->id)->delete();
        ContentCategory::query()->where('website_id', $website->id)->delete();
        Slider::query()->where('website_id', $website->id)->get()->each(function (Slider $slider): void {
            $slider->clearMediaCollection('slide_image');
            $slider->delete();
        });

        $categories = collect([
            [
                'name' => 'Dynamic Website Builder',
                'description' => 'Documentation on building page experiences using reusable layouts, linked content, and menu-driven composition.',
                'sort_order' => 1,
            ],
            [
                'name' => 'CMS Management',
                'description' => 'Operational guides for managing categories, content, menus, users, visibility, and publishing workflows in the CMS.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Content Architecture',
                'description' => 'How to model content, structure blocks, and choose layout types so templates render dynamic pages reliably.',
                'sort_order' => 3,
            ],
            [
                'name' => 'System Operations',
                'description' => 'Practical runbooks for setup, roles, permissions, backups, troubleshooting, and release readiness.',
                'sort_order' => 4,
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
                'category' => 'Dynamic Website Builder',
                'summary' => 'System-managed starter website for learning dynamic page building and CMS operations end to end.',
                'body' => '<h2>Welcome to the system-managed starter website</h2><p>This default website is automatically provisioned and maintained by the platform to demonstrate how dynamic pages, menus, categories, and content blocks work together.</p><p>Use this site as your reference implementation for:</p><ul><li>Composing pages from linked content and categories</li><li>Switching design layouts safely through CMS configuration</li><li>Publishing and iterating documentation content quickly</li></ul><p><strong>Tip:</strong> Keep this website as your baseline environment before cloning patterns into production websites.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'About',
                'content_type' => 'page',
                'category' => 'CMS Management',
                'summary' => 'How the default website is auto-managed and why it exists as a living documentation environment.',
                'body' => '<h2>Why this default website exists</h2><p>The platform seeds this website automatically as a self-documenting environment for teams adopting the builder and CMS.</p><p>It is intended for:</p><ol><li>Onboarding editors and administrators</li><li>Testing layout and content modeling decisions</li><li>Validating publishing workflows before launch</li></ol><p>All content can be edited through CKEditor-friendly fields, including rich HTML sections.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'Getting Started with the Dynamic Builder',
                'content_type' => 'article',
                'category' => 'Dynamic Website Builder',
                'summary' => 'A quick walkthrough from creating content to rendering a full public page through dynamic templates.',
                'body' => '<h2>Quick start workflow</h2><ol><li>Create or update a category</li><li>Create content and assign a layout type</li><li>Link content to menu items using references</li><li>Publish and verify in the public routes</li></ol><p>Dynamic pages are rendered through a single page template that resolves header, body, and footer designs based on CMS configuration with safe defaults.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'How Page Templates Resolve Layouts',
                'content_type' => 'article',
                'category' => 'Dynamic Website Builder',
                'summary' => 'Understand how configured layout types map to design files and fallback to default templates.',
                'body' => '<h2>Layout resolution strategy</h2><p>When rendering pages, the system checks for configured design views first. If a view does not exist, it falls back to the default design for that section.</p><h3>Resolution order</h3><ul><li>Configured layout from CMS entity (menu/category/content/menu item)</li><li>Corresponding design file under designs folders</li><li>Default design file when specific layout is missing</li></ul><p>This keeps publishing resilient even when custom layouts are still in progress.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'Designing Content Blocks for Reuse',
                'content_type' => 'article',
                'category' => 'Content Architecture',
                'summary' => 'Build modular content blocks so the same information can power multiple page layouts.',
                'body' => '<h2>Reuse-first content architecture</h2><p>Model content in granular blocks to support flexible rendering across default and custom designs.</p><table><thead><tr><th>Block Type</th><th>Use Case</th><th>Tip</th></tr></thead><tbody><tr><td>Rich Text</td><td>Guides and documentation</td><td>Keep sections short with clear headings</td></tr><tr><td>Callout</td><td>Warnings and highlights</td><td>Use one key message per block</td></tr><tr><td>Checklist</td><td>Operational steps</td><td>Write action-first bullets</td></tr></tbody></table><p>Block discipline improves consistency and simplifies maintenance.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'Category Strategy for Documentation Sites',
                'content_type' => 'article',
                'category' => 'Content Architecture',
                'summary' => 'Organize categories so editors can scale content without losing discoverability.',
                'body' => '<h2>Plan categories before volume grows</h2><p>Strong category design improves navigation and search relevance.</p><ul><li>Separate conceptual guides from operational runbooks</li><li>Use stable names that survive product changes</li><li>Avoid category overlap by assigning clear ownership</li></ul><p>Review taxonomy quarterly and archive obsolete structures proactively.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'CMS Publishing Workflow: Draft to Live',
                'content_type' => 'article',
                'category' => 'CMS Management',
                'summary' => 'A predictable editorial flow for drafting, review, publishing, and rollback.',
                'body' => '<h2>Recommended publishing lifecycle</h2><ol><li>Create content in draft with clear summary</li><li>Attach category, visibility, and layout type</li><li>Peer-review content and links</li><li>Publish with timestamp and monitor usage</li></ol><p>Use revision notes in your team process so rollback and audit activities remain clear.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'Menu and Navigation Governance',
                'content_type' => 'article',
                'category' => 'CMS Management',
                'summary' => 'Control menu structures, visibility, and routes while keeping public navigation intuitive.',
                'body' => '<h2>Navigation design principles</h2><ul><li>Keep top-level menus task-focused</li><li>Use menu item references for category/content rollups</li><li>Avoid exposing draft or restricted content in public menus</li><li>Test links after each structural change</li></ul><p>Menu quality directly affects discoverability and user confidence.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'Roles and Permissions Matrix',
                'content_type' => 'article',
                'category' => 'System Operations',
                'summary' => 'Define who can access CMS modules and how to enforce least-privilege controls.',
                'body' => '<h2>Permission-first administration</h2><p>Assign roles based on responsibilities, not convenience.</p><table><thead><tr><th>Role</th><th>Primary Scope</th><th>Typical Permissions</th></tr></thead><tbody><tr><td>Super Admin</td><td>Platform-wide</td><td>All CMS and operational permissions</td></tr><tr><td>Admin</td><td>Website-level management</td><td>Categories, content, menus, settings</td></tr><tr><td>User</td><td>Application features</td><td>Restricted application interactions</td></tr></tbody></table><p>Review role assignments regularly and remove stale access.</p>',
                'audience' => 'general',
            ],
            [
                'title' => 'Backups, Recovery, and Release Checklist',
                'content_type' => 'article',
                'category' => 'System Operations',
                'summary' => 'Baseline operational checklist to keep deployments safe and recoverable.',
                'body' => '<h2>Operational readiness checklist</h2><ul><li>Verify automated backups for database and media</li><li>Confirm restore drill success in staging</li><li>Review migrations and seed changes</li><li>Check route, view, and permission caches</li><li>Document rollback steps before release</li></ul><p>Reliable operations are as important as feature delivery.</p>',
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
                'title' => 'Build dynamic pages from structured content',
                'kicker' => 'Builder Documentation',
                'caption' => 'Learn how categories, content blocks, menu references, and layout types combine into reusable page templates.',
                'primary_button_text' => 'Open Builder Guide',
                'primary_button_link' => '/pages/builder-guide',
                'secondary_button_text' => 'View Content Library',
                'secondary_button_link' => '/content',
                'sort_order' => 1,
                'asset' => base_path('public/seed/hero-slide-1.png'),
            ],
            [
                'title' => 'Manage content lifecycle with confidence',
                'kicker' => 'CMS Operations',
                'caption' => 'Run editorial and governance workflows with clear permissions, publishing states, and audit-friendly structure.',
                'primary_button_text' => 'Open CMS Playbook',
                'primary_button_link' => '/pages/cms-playbook',
                'secondary_button_text' => 'Review Permissions',
                'secondary_button_link' => '/content?q=permissions',
                'sort_order' => 2,
                'asset' => base_path('public/seed/hero-slide-2.png'),
            ],
            [
                'title' => 'Operate the system as a managed baseline',
                'kicker' => 'System Managed Default Site',
                'caption' => 'Use this first website as your living reference for onboarding, testing, and release readiness.',
                'primary_button_text' => 'View Operations Docs',
                'primary_button_link' => '/content?category=system-operations',
                'secondary_button_text' => 'Read About Site',
                'secondary_button_link' => '/pages/about',
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

            // Always replace the slide image so reseeding stays consistent.
            $slider->clearMediaCollection('slide_image');
            if (is_file($sliderData['asset'])) {
                $slider->addMedia($sliderData['asset'])
                    ->preservingOriginal()
                    ->toMediaCollection('slide_image');
            }
        }

        $builderCategoryId = $categories['Dynamic Website Builder']->id;
        $cmsCategoryId = $categories['CMS Management']->id;
        $architectureCategoryId = $categories['Content Architecture']->id;
        $operationsCategoryId = $categories['System Operations']->id;

        $quickStartContentIds = implode(',', [
            $contents['Getting Started with the Dynamic Builder']->id,
            $contents['How Page Templates Resolve Layouts']->id,
            $contents['CMS Publishing Workflow: Draft to Live']->id,
        ]);

        $operationsContentIds = implode(',', [
            $contents['Roles and Permissions Matrix']->id,
            $contents['Backups, Recovery, and Release Checklist']->id,
        ]);

        $publicMenus = [
            [
                'slug' => 'home',
                'name' => 'Home',
                'description' => 'Entry page for the system-managed dynamic website builder and CMS documentation experience.',
                'sort_order' => 1,
                'items' => [
                    [
                        'title' => 'Quick Start Builder Path',
                        'target_reference' => 'content:'.$quickStartContentIds,
                        'layout_type' => 'default',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Builder Topics',
                        'target_reference' => 'category:'.$builderCategoryId.','.$architectureCategoryId,
                        'layout_type' => 'card',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'about',
                'name' => 'About',
                'description' => 'Documentation website purpose, governance model, and how the platform auto-manages this starter implementation.',
                'sort_order' => 2,
                'items' => [
                    [
                        'title' => 'CMS Management Collection',
                        'target_reference' => 'category:'.$cmsCategoryId,
                        'layout_type' => 'editorial',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'System Operations Runbook',
                        'target_reference' => 'content:'.$operationsContentIds,
                        'layout_type' => 'minimal',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'builder-guide',
                'name' => 'Builder Guide',
                'description' => 'Practical guides for layout selection, reusable blocks, and dynamic page composition.',
                'sort_order' => 3,
                'items' => [
                    [
                        'title' => 'Builder Documentation Hub',
                        'target_reference' => 'category:'.$builderCategoryId.','.$architectureCategoryId,
                        'layout_type' => 'card',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'slug' => 'cms-playbook',
                'name' => 'CMS Playbook',
                'description' => 'Operational playbook for editors and administrators managing dynamic websites.',
                'sort_order' => 4,
                'items' => [
                    [
                        'title' => 'CMS and Ops Reference',
                        'target_reference' => 'category:'.$cmsCategoryId.','.$operationsCategoryId,
                        'layout_type' => 'default',
                        'sort_order' => 1,
                    ],
                ],
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