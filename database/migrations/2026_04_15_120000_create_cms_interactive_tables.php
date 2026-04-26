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


        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->text('value')->nullable();
            $table->string('layout_type')->default('default');
            $table->string('group', 60)->default('general');
            $table->string('input_type', 30)->default('text');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['group', 'label']);
            $table->unique(['website_id', 'key']);
        });

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('title');
            $table->foreignId('content_id')->nullable()->constrained('contents')->nullOnDelete();
            $table->string('slug');
            $table->string('kicker', 120)->nullable();
            $table->string('layout_type')->default('default');
            $table->text('caption')->nullable();
            $table->string('primary_button_text', 80)->nullable();
            $table->string('primary_button_link')->nullable();
            $table->string('secondary_button_text', 80)->nullable();
            $table->string('secondary_button_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('slider_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slider_id')->constrained('sliders')->cascadeOnDelete();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('caption')->nullable();
            $table->string('layout_type')->default('default');
            $table->string('primary_button_text', 80)->nullable();
            $table->string('primary_button_link')->nullable();
            $table->string('secondary_button_text', 80)->nullable();
            $table->string('secondary_button_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->unique(['website_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('slider_items');
        Schema::dropIfExists('app_settings');
    }
};