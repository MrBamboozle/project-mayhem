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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('tag_line')->nullable();
            $table->text('description');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location');
            $table->json('address');
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->foreignUuid('city_id')->nullable()->references('id')->on('cities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
