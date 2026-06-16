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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_venta')->nullable()->index();
            $table->dateTime('fecha_despacho')->nullable();
            
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('usuarios')
                ->onDelete('set null');
                
            $table->enum('estado', ['CARRITO', 'CONFIRMADO', 'DESPACHADO'])->default('CARRITO');
            $table->decimal('total', 10, 2)->default(0.00);
            
            $table->foreignId('forma_pago_id')
                ->nullable()
                ->constrained('formas_pago')
                ->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
