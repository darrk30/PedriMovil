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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitud', 18, 15); // Coordenada de latitud con alta precisión
            $table->decimal('longitud', 18, 15); // Coordenada de longitud con alta precisión
            $table->string('status'); // Estado del registro
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clave foránea con relación a la tabla users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};

