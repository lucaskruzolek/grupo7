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
        Schema::create('producto_variaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade');
            $table->foreignId('color_id')
                ->constrained('colores')
                ->onDelete('restrict');
            $table->foreignId('talle_id')
                ->nullable()
                ->constrained('talles')
                ->onDelete('restrict');
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio_adicional', 10, 2)->default(0.00);
            $table->string('sku', 50)->unique();
            $table->timestamps();
            $table->softDeletes();

            // Evitar variaciones duplicadas: mismo producto + color + talle
            $table->unique(['producto_id', 'color_id', 'talle_id'], 'variacion_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_variaciones');
    }
};
