<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('star_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('star_schema_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('star_schema_id')->constrained('star_schemas')->cascadeOnDelete();
            $table->string('indicator_key', 80)->nullable();
            $table->string('label', 150);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['star_schema_id', 'sort_order'], 'star_schema_indicator_order_unique');
        });

        Schema::create('star_schema_indicator_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('star_schema_indicator_id')->constrained('star_schema_indicators')->cascadeOnDelete();
            $table->string('label', 180);
            $table->decimal('score', 8, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['star_schema_indicator_id', 'sort_order'], 'star_schema_option_order_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('star_schema_indicator_options');
        Schema::dropIfExists('star_schema_indicators');
        Schema::dropIfExists('star_schemas');
    }
};