<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 32)->default('email')->index();
            $table->string('event', 64)->index();
            $table->string('address');
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('filters')->nullable();
            $table->timestamps();

            $table->unique(['channel', 'event', 'address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipients');
    }
};
