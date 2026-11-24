<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimasi_servis', function (Blueprint $table) {
            $table->id();
            $table->char('session_token', 36)->index();
            $table->decimal('tingkat_kerusakan', 5, 2);
            $table->decimal('usia_laptop', 4, 1);
            $table->decimal('kesulitan_perbaikan', 4, 1);
            $table->unsignedInteger('estimasi_biaya');
            $table->enum('kategori_biaya', ['Murah', 'Sedang', 'Mahal']);
            $table->json('detail_fuzzy')->nullable();
            $table->string('versi_fuzzy', 20)->default('1.0');
            $table->timestamps();

            $table->index(['session_token', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimasi_servis');
    }
};
