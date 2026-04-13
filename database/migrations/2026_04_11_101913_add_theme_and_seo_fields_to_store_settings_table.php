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
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('address')->nullable();
            $table->text('map_embed')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_og_image')->nullable();
            $table->string('seo_twitter_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'favicon_path',
                'mobile_number',
                'address',
                'map_embed',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'seo_title',
                'seo_og_image',
                'seo_twitter_image'
            ]);
        });
    }
};
