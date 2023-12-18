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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 512);
            $table->text('description');
            $table->dateTime('start_date')->index();
            $table->dateTime('end_date')->index();
            $table->string('location', 512);
            $table->text('url', 1024);
            $table->unsignedBigInteger('calendar_id');
            $table->foreign('calendar_id')
                ->references('id')
                ->on('calendars');
            $table->unsignedBigInteger('cas_user_id');
            $table->foreign('cas_user_id')
                ->references('id')
                ->on('cas_users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
