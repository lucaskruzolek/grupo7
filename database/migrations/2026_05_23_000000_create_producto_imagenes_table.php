<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->string('sku_color', 80); // Vinculado al grupo de variantes de color del producto
            $table->string('url');
            $table->unsignedSmallInteger('orden')->default(0); // orden = 1 será la foto principal
            $table->timestamps();

            // Indexamos para que las búsquedas del catálogo sean ultrarápidas
            $table->index('sku_color');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_imagenes');
    }
};