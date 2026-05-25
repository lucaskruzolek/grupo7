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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->onDelete('restrict');
            $table->foreignId('marca_id')
                ->constrained('marcas')
                ->onDelete('restrict');
            $table->foreignId('coleccion_id')->nullable()
                ->constrained('colecciones')
                ->onDelete('set null');
            $table->foreignId('color_id')
                ->constrained('colores')
                ->onDelete('restrict');
            $table->foreignId('talle_id')
                ->nullable()
                ->constrained('talles')
                ->onDelete('restrict');
// Información descriptiva (Flat Model)
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_mascota', ['perro', 'gato', 'ambos'])->default('ambos');
// Stock e Identificadores de variante
            $table->string('sku_base', 50);      // Agrupa variantes (Ej: "BUZO-POLAR")
            $table->string('sku', 50)->unique(); // Variante única (Ej: "BUZO-POLAR-S-ROJO")
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

// Regla de negocio: Evita que el mismo modelo repita combinación de color y talle
            $table->unique(['sku_base', 'color_id', 'talle_id'], 'variacion_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
