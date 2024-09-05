<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answer', function (Blueprint $table) {
            $table->id('id_answer');
            $table->string('username', 20);
            $table->unsignedBigInteger('id_question');
            $table->unsignedBigInteger('id_parent')->nullable();
            $table->string('replyTo', 20)->nullable();
            $table->text('deskripsi');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('username')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_question')->references('id_question')->on('question')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('replyTo')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        // Run SQL to modify the column type to MEDIUMBLOB
        DB::statement('ALTER TABLE answer ADD gambar MEDIUMBLOB NULL AFTER deskripsi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer');
    }
};
