<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('user_websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('role', 40)->default('member');
            $table->boolean('is_owner')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'website_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('current_website_id')->references('id')->on('websites')->nullOnDelete();
        });

        Schema::create('content_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('name');
            $table->string('visibility', 20)->default('public');
            $table->string('slug');
            $table->string('layout_type')->default('default');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['website_id', 'slug']);
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('layout_type')->default('default');
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('content_type')->default('page');
            $table->foreignId('category_id')->nullable()->constrained('content_categories')->nullOnDelete();

            $table->string('status', 20)->default('draft');
            $table->string('audience', 30)->default('general');
            $table->string('visibility', 20)->default('public');
            $table->string('featured_image_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['content_type', 'status']);
            $table->index(['audience', 'visibility']);
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->string('block_type', 40);
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->string('layout_type')->default('default'); // design layout

            $table->json('json_data')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['content_id', 'sort_order']);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->string('layout_type')->default('default'); // design layout
            $table->string('location')->nullable(); // header, footer etc
            $table->string('visibility', 20)->default('public');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['website_id', 'slug']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('title');
            $table->string('layout_type')->default('default');
            $table->string('target_reference')->nullable();

            $table->string('route')->nullable();
            $table->string('icon', 80)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('visibility', 20)->default('public');
            $table->boolean('open_in_webview')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['menu_id', 'sort_order']);
        });

        Schema::table('content_categories', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->nullable()->after('website_id')->constrained('menu_items')->nullOnDelete();
            $table->index(['menu_item_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_website_id']);
        });

        Schema::dropIfExists('user_websites');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('content_categories');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('websites');
    }
};