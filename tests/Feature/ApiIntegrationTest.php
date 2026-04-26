<?php

namespace Tests\Feature;

use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_registration_returns_token_and_permissions(): void
    {
        $this->seed(CmsSeeder::class);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'mobile@example.com')
            ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email'], 'token', 'permissions']])
            ->assertJsonFragment(['app.access.person-space']);
    }

    public function test_mobile_can_fetch_permissions_after_registration(): void
    {
        $this->seed(CmsSeeder::class);

        $register = $this->postJson('/api/auth/register', [
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertCreated();

        $token = $register->json('data.token');

        $this->withToken($token)
            ->getJson('/api/me/permissions')
            ->assertOk()
            ->assertJsonPath('data.email', 'mobile@example.com');

        $this->withToken($token)
            ->getJson('/api/me/bootstrap')
            ->assertOk()
            ->assertJsonPath('data.menu.location', 'public-primary');
    }

    public function test_public_bootstrap_returns_seeded_srhr_modules(): void
    {
        $this->seed(CmsSeeder::class);

        $this->getJson('/api/app/bootstrap')
            ->assertOk()
            ->assertJsonPath('data.menu.location', 'public-primary');

        $response = $this->getJson('/api/app/bootstrap');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data.hero_slides')
            ->assertJsonCount(5, 'data.categories')
            ->assertJsonCount(8, 'data.featured_contents')
            ->assertJsonFragment(['key' => 'app_name', 'value' => 'SRHR Connect'])
            ->assertJsonPath('data.hero_slides.0.title', 'Build a beautiful online presence that grows your brand');
    }
}