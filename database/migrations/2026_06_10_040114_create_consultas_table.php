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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->string('pedido')->nullable()->index();
            $table->enum('asunto', ['consulta', 'reclamo', 'devolucion', 'otro']);
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->boolean('respondido')->default(false);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
