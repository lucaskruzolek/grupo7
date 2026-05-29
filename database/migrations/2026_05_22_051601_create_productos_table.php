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

            // Claves Foráneas Relacionales
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->onDelete('restrict');
            $table->foreignId('coleccion_id')->nullable()
                ->constrained('colecciones')
                ->onDelete('set null');
            $table->foreignId('color_id')
                ->constrained('colores')
                ->onDelete('restrict');
            
            // Información descriptiva (Flat Model)
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_mascota', ['perro', 'gato', 'ambos'])->default('ambos');

            // Stock, Talle e Identificadores de variante
            $table->string('sku_base', 50);           // Agrupa variantes por modelo (Ej: "BUZO-POLAR")
            $table->string('sku_color', 80)->index(); // Agrupa variantes por color (Ej: "BUZO-POLAR-ROJO")
            $table->string('sku', 50)->unique();      // Variante única (Ej: "BUZO-POLAR-ROJO-S")
            $table->string('talle', 10);              // Almacena directo el talle como texto ('S', 'M', 'L', '1', etc.)
            
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio', 10, 2)->default(0.00);
            
            $table->timestamps();
            $table->softDeletes();

            // Evita que el mismo modelo repita la combinación de color y talle en el disco
            $table->unique(['sku_base', 'color_id', 'talle'], 'variacion_unica');
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
