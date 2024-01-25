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
        Schema::create('report_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cas_user_id');
            $table->foreign('cas_user_id')
                ->references('id')
                ->on('cas_users');
            $table->unsignedBigInteger('report_id');
            $table->foreign('report_id')
                ->references('id')
                ->on('reports');
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_data');
    }
};
