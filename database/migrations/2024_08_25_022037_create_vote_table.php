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
        Schema::create('vote', function (Blueprint $table) {
            $table->id('id_vote');
            $table->string('username', 20);
            $table->unsignedBigInteger('id_question');
            $table->enum('vote_type', ['UpVote', 'DownVote']);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('username')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_question')->references('id_question')->on('question')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote');
    }
};
