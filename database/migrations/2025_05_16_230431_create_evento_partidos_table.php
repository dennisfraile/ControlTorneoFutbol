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
        Schema::create('evento_partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained()->onDelete('cascade');
            $table->foreignId('jugador_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['gol', 'amarilla', 'roja', 'cambio']);
            $table->string('descripcion')->nullable();
            $table->integer('minuto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_partidos');
    }
};
