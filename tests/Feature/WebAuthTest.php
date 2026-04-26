<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Website;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_loads(): void
    {
        $this->seed(CmsSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Build a beautiful online presence that grows your brand');
    }

    public function test_home_page_uses_seeded_slider_content(): void
    {
        $this->seed(CmsSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Simple. Elegant. Effective.');
        $response->assertSee('Showcase your services with confidence');
    }

    public function test_guest_can_view_auth_pages(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Log in');
        $this->get(route('register'))->assertOk()->assertSee('Create account');
    }

    public function test_guest_can_access_public_srhr_pages_without_login(): void
    {
        $this->seed(CmsSeeder::class);

        $this->get(route('public.categories.index'))->assertOk()->assertSee('Browse published SRHR topics with clearer entry points.');
        $this->get(route('public.contents.index'))->assertOk()->assertSee('Published SRHR content arranged like a modern landing library.');
    }

    public function test_seeded_header_shows_multiple_public_menu_groups(): void
    {
        $this->seed(CmsSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('About');
        $response->assertSee('Home');
        $response->assertSee(route('public.pages.show', 'about'), false);
        $response->assertDontSee('/pages/learn', false);
        $response->assertDontSee('/pages/support', false);
        $response->assertDontSee('/pages/account', false);
    }

    public function test_public_header_uses_admin_managed_menu_items_with_dropdowns(): void
    {
        $this->seed(CmsSeeder::class);

        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();
        $websiteId = $menu->website_id;
        $menu->items()->delete();

        $parent = $menu->items()->create([
            'website_id' => $websiteId,
            'title' => 'Resources',
            'layout_type' => 'default',
            'route' => '/content',
            'sort_order' => 1,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]);

        $menu->items()->create([
            'website_id' => $websiteId,
            'parent_id' => $parent->id,
            'title' => 'Topics',
            'layout_type' => 'default',
            'route' => '/topics',
            'sort_order' => 2,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]);

        $menu->items()->create([
            'website_id' => $websiteId,
            'parent_id' => $parent->id,
            'title' => 'Support Email',
            'layout_type' => 'default',
            'target_reference' => 'mailto:support@example.com',
            'sort_order' => 3,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Resources');
        $response->assertSee('Topics');
        $response->assertSee('Support Email');
        $response->assertDontSee('Content');
    }

    public function test_user_can_register_without_cms_access(): void
    {
        $this->seed(CmsSeeder::class);

        $response = $this->post(route('register'), [
            'name' => 'Platform User',
            'email' => 'user@example.com',
            'phone' => '0712345678',
            'website_name' => 'Platform User Site',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'user@example.com']);
        $this->assertFalse(auth()->user()?->canAccessCms() ?? true);
    }

    public function test_guest_is_redirected_from_cms_dashboard(): void
    {
        $this->get(route('cms.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_user_can_log_in(): void
    {
        $this->seed(CmsSeeder::class);

        $user = User::factory()->create([
            'email' => 'reviewer@example.com',
            'password' => bcrypt('password'),
        ]);

        $adminRole = Role::query()->where('name', 'admin')->where('guard_name', 'web')->firstOrFail();
        $user->assignRole($adminRole);

        $response = $this->post(route('login'), [
            'email' => 'reviewer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('cms.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_content_form_uses_ckeditor_for_rich_text_editing(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('cms.contents.create'));

        $response->assertOk();
        $response->assertSee('data-ckeditor-field="content-body"', false);
        $response->assertSee('cdn.ckeditor.com/ckeditor5', false);
        $response->assertSee('ClassicEditor', false);
    }

    public function test_public_content_page_renders_saved_rich_text(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $website = Website::query()->firstOrFail();
        $category = ContentCategory::query()->firstOrFail();

        $content = Content::query()->create([
            'website_id' => $website->id,
            'title' => 'Rich Text Public Page',
            'slug' => 'rich-text-public-page',
            'summary' => 'A rich text rendering check.',
            'body' => '<h2>Formatted heading</h2><p><strong>Public body</strong> with <a href="https://example.com">a link</a>.</p>',
            'content_type' => 'page',
            'category_id' => $category->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'published_at' => Carbon::now(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $response = $this->get(route('public.contents.show', $content));

        $response->assertOk();
        $response->assertSee('<h2>Formatted heading</h2>', false);
        $response->assertSee('<strong>Public body</strong>', false);
    }

    public function test_menu_item_slug_route_loads_dynamic_page_content(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();
        $website = Website::query()->firstOrFail();

        $menuItem = MenuItem::query()->create(MenuItem::normalizeForPersistence([
            'website_id' => $website->id,
            'menu_id' => $menu->id,
            'title' => 'Dynamic Support Page',
            'layout_type' => 'default',
            'target_reference' => null,
            'sort_order' => 999,
            'visibility' => 'public',
            'open_in_webview' => true,
            'is_active' => true,
        ]));

        $linkedCategory = ContentCategory::query()->create([
            'website_id' => $website->id,
            'name' => 'Dynamic Category',
            'slug' => 'dynamic-category',
            'description' => 'Linked category description.',
            'menu_item_id' => $menuItem->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Content::query()->create([
            'website_id' => $website->id,
            'title' => 'Category Content Entry',
            'slug' => 'category-content-entry',
            'summary' => 'Category content summary.',
            'body' => '<p>Category body.</p>',
            'content_type' => 'page',
            'category_id' => $linkedCategory->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'published_at' => Carbon::now(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standaloneCategory = ContentCategory::query()->create([
            'website_id' => $website->id,
            'name' => 'Standalone Category',
            'slug' => 'standalone-category',
            'description' => 'Standalone category description.',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $standaloneContent = Content::query()->create([
            'website_id' => $website->id,
            'title' => 'Standalone Content Entry',
            'slug' => 'standalone-content-entry',
            'summary' => 'Standalone content summary.',
            'body' => '<p>Standalone body.</p>',
            'content_type' => 'page',
            'category_id' => $standaloneCategory->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'published_at' => Carbon::now(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $menuItem->update([
            'target_reference' => 'content:'.$standaloneContent->id,
        ]);

        $response = $this->get(route('public.menu-pages.show', ['menuItemName' => $menuItem->publicPageSlug()]));

        $response->assertOk();
        $response->assertSee('Dynamic Category');
        $response->assertSee('Category Content Entry');
        $response->assertSee('Standalone Content Entry');
        $response->assertSee('Dynamic pathway');
    }

    public function test_public_header_uses_dynamic_menu_item_link_for_legacy_webview_routes(): void
    {
        $this->seed(CmsSeeder::class);

        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();

        $menuItem = $menu->items()->create([
            'website_id' => $menu->website_id,
            'title' => 'Legacy Dynamic Page',
            'layout_type' => 'default',
            'target_reference' => 'content:1',
            'route' => '/menu-pages/123',
            'sort_order' => 999,
            'visibility' => 'public',
            'open_in_webview' => true,
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('public.contents.show', 'home'), false);
        $response->assertDontSee('/menu-pages/123', false);
    }

    public function test_public_header_routes_all_menu_items_through_dynamic_menu_item_path(): void
    {
        $this->seed(CmsSeeder::class);

        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();

        $contentItem = $menu->items()->create(MenuItem::normalizeForPersistence([
            'website_id' => $menu->website_id,
            'title' => 'Content Routed Dynamically',
            'layout_type' => 'default',
            'sort_order' => 1200,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]));

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('public.menu-pages.show', ['menuItemName' => $contentItem->publicPageSlug()]), false);
        $response->assertDontSee('href="'.route('public.contents.index').'"', false);
    }

    public function test_dynamic_menu_item_route_loads_public_non_webview_menu_item(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();
        $website = Website::query()->firstOrFail();
        $category = ContentCategory::query()->firstOrFail();

        $content = Content::query()->create([
            'website_id' => $website->id,
            'title' => 'Dynamic Content Type Page',
            'slug' => 'dynamic-content-type-page',
            'summary' => 'Dynamic content type summary.',
            'body' => '<p>Dynamic content type body.</p>',
            'content_type' => 'page',
            'category_id' => $category->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'published_at' => Carbon::now(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $menuItem = $menu->items()->create(MenuItem::normalizeForPersistence([
            'website_id' => $website->id,
            'title' => 'Non Webview Dynamic Page',
            'layout_type' => 'default',
            'sort_order' => 1300,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]));

        $category->update(['menu_item_id' => $menuItem->id]);

        $response = $this->get(route('public.menu-pages.show', ['menuItemName' => $menuItem->publicPageSlug()]));

        $response->assertOk();
        $response->assertSee('Non Webview Dynamic Page');
        $response->assertSee('Dynamic Content Type Page');
    }
}