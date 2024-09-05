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
        Schema::create('question', function (Blueprint $table) {
            $table->id('id_question');
            $table->string('username', 20);
            $table->enum('kategori', ['Sholat', 'Nikah', 'Puasa', 'Zakat']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();


            // Add foreign key constraint
            $table->foreign('username')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        // Run SQL to modify the column type to MEDIUMBLOB
        DB::statement('ALTER TABLE question ADD gambar MEDIUMBLOB NULL AFTER deskripsi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question');
    }
};
