<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // User yang membuat laporan
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Kategori modus penipuan
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();

            // Jenis identitas yang dilaporkan
            $table->enum('reported_type', [
                'phone',
                'bank_account',
                'account',
                'url'
            ]);

            // Nilai identitas yang dilaporkan
            $table->string('reported_value');

            // Kronologi kejadian
            $table->text('chronology');

            // File bukti pendukung
            $table->string('evidence_file');

            // Status laporan
            $table->enum('status', [
                'pending',
                'need_revision',
                'pending_admin_review',
                'verified',
                'rejected'
            ])->default('pending');

            // Verifier yang memeriksa laporan
            $table->foreignId('verifier_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Admin yang melakukan peninjauan akhir
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Catatan Verifier jika laporan perlu diperbaiki
            $table->text('revision_note')->nullable();

            $table->timestamps();

            // Membantu proses pencarian data
            $table->index(['reported_type', 'reported_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
