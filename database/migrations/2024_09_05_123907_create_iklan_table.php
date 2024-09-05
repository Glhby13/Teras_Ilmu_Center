<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('iklan', function (Blueprint $table) {
            $table->id('id_iklan');
            $table->text('judul');
            $table->text('deskripsi');
            $table->string('linkIklan');
            $table->softDeletes();
            $table->timestamps();
        });

        // Run SQL to modify the column type to MEDIUMBLOB
        DB::statement('ALTER TABLE iklan ADD gambar MEDIUMBLOB NULL AFTER linkIklan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iklan');
    }
};
