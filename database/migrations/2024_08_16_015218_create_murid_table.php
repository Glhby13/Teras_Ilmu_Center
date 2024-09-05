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
        Schema::create('murid', function (Blueprint $table) {
            $table->id('id_murid');
            $table->string('nama', 100);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('no_telepon', 15);
            $table->text('deskripsi')->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('pendidikan', 50)->nullable();
            $table->string('username', 20)->unique();
            $table->string('email', 40)->unique()->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Add foreign key constraint
            $table->foreign('username')->references('username')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');  // Optional: Delete murid if user is deleted
        });

        // Run SQL to modify the column type to MEDIUMBLOB
        DB::statement('ALTER TABLE murid ADD gambar MEDIUMBLOB NULL AFTER email');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
