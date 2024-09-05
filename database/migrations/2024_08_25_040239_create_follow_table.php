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
        Schema::create('follow', function (Blueprint $table) {
            $table->id('id_follow');
            $table->string('follower', 20);
            $table->string('following', 20);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('follower')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('following')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow');
    }
};
