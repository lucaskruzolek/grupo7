<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coleccion;

class ColeccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colecciones = [
            [
                'nombre' => 'Clásicos',
                'descripcion' => 'Diseños atemporales y cómodos que nunca pasan de moda.',
                'url_imagen' => 'https://images.unsplash.com/photo-1541599540903-216a46ca1ad0?w=600'
            ],
            [
                'nombre' => 'Invierno',
                'descripcion' => 'Prendas súper abrigadas diseñadas para proteger a tu mascota del frío más extremo.',
                'url_imagen' => 'https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?w=600'
            ],
            [
                'nombre' => 'Primavera',
                'descripcion' => 'Colores vibrantes y telas livianas ideales para pasear en días templados.',
                'url_imagen' => 'https://images.unsplash.com/photo-1522008622779-a550eb13e29e?w=600'
            ],
            [
                'nombre' => 'Picnic',
                'descripcion' => 'Accesorios y ropa divertidos, perfectos para disfrutar una tarde al aire libre.',
                'url_imagen' => 'https://images.unsplash.com/photo-1596492784531-6e6eb5ea9993?w=600'
            ],
            [
                'nombre' => 'Esenciales',
                'descripcion' => 'Los productos básicos e indispensables que toda mascota necesita para su día a día.',
                'url_imagen' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=600'
            ],
            [
                'nombre' => 'Nuevos',
                'descripcion' => 'Últimos lanzamientos y tendencias en moda y confort animal.',
                'url_imagen' => 'https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?w=600'
            ],
        ];

        foreach ($colecciones as $coleccion) {
            Coleccion::firstOrCreate(
                ['nombre' => $coleccion['nombre']],
                [
                    'descripcion' => $coleccion['descripcion'],
                    'url_imagen' => $coleccion['url_imagen']
                ]
            );
        }
    }
}
