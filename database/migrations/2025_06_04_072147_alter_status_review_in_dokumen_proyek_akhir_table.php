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
        Schema::table('dokumen_proyek_akhir', function (Blueprint $table) {
            // Ubah kolom enum untuk menyertakan 'rejected'
            // Perhatikan urutan jika penting bagi Anda
            $table->enum('status_review', ['pending', 'approved', 'revision_needed', 'rejected'])
                  ->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_proyek_akhir', function (Blueprint $table) {
            // Kembalikan ke definisi lama jika perlu rollback
            // Pastikan ini sesuai dengan definisi sebelum perubahan
            $table->enum('status_review', ['pending', 'approved', 'revision_needed'])
                  ->default('pending')->change();
        });
    }
};
