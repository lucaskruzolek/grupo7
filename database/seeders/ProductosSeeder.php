<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Categoria;
use App\Models\Color;
use App\Models\Coleccion;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definición de productos base
        $productosBase = [
            [
                'nombre' => 'Buzo Polar Térmico',
                'sku_base' => 'BUZO-POLAR',
                'descripcion' => 'Buzo de tela polar súper abrigado e hipoalergénico. Ideal para proteger a tu mascota en los paseos invernales.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'buzos',
                'coleccion' => 'Invierno',
                'colores' => ['Rojo', 'Azul', 'Gris', 'Rosa'],
                'talles' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'precio_base' => 12500.00,
                'imagenes' => [
                    'Rojo' => [
                        'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=800',
                        'https://images.unsplash.com/photo-1517849845537-4d257902454a?w=800'
                    ],
                    'Azul' => [
                        'https://images.unsplash.com/photo-1541599540903-216a46ca1ad0?w=800',
                        'https://images.unsplash.com/photo-1537151608828-ea2b117b6b86?w=800'
                    ],
                    'Gris' => [
                        'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=800',
                        'https://images.unsplash.com/photo-1518020382113-a7e8fc38eac9?w=800'
                    ],
                    'Rosa' => [
                        'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?w=800',
                        'https://images.unsplash.com/photo-1477884213984-b9710f231465?w=800'
                    ],
                ]
            ],
            [
                'nombre' => 'Suéter de Lana Trenzado',
                'sku_base' => 'SUETER-LANA',
                'descripcion' => 'Suéter clásico tejido en lana acrílica premium, suave al tacto y muy elástico para un calce perfecto.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'sueteres',
                'coleccion' => 'Invierno',
                'colores' => ['Violeta', 'Naranja', 'Gris'],
                'talles' => ['XS', 'S', 'M', 'L', 'XL'],
                'precio_base' => 14200.00,
                'imagenes' => [
                    'Violeta' => [
                        'https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?w=800'
                    ],
                    'Naranja' => [
                        'https://images.unsplash.com/photo-1504595403659-9084ccaae987?w=800'
                    ],
                    'Gris' => [
                        'https://images.unsplash.com/photo-1544568100-847a948585b9?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Pechera de Neoprene Confort',
                'sku_base' => 'PECHERA-NEOP',
                'descripcion' => 'Pechera ergonómica acolchada con neoprene. Hebillas de alta resistencia y tira reflectiva para máxima seguridad.',
                'tipo_mascota' => 'perro',
                'categoria' => 'pecheras',
                'coleccion' => 'Esenciales',
                'colores' => ['Negro', 'Rojo', 'Verde'],
                'talles' => ['S', 'M', 'L', 'XL'],
                'precio_base' => 18900.00,
                'imagenes' => [
                    'Negro' => [
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800'
                    ],
                    'Rojo' => [
                        'https://images.unsplash.com/photo-1534361960057-19889db9621e?w=800'
                    ],
                    'Verde' => [
                        'https://images.unsplash.com/photo-1596492784531-6e6eb5ea9993?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Capa Impermeable de Lluvia',
                'sku_base' => 'IMP-LLUVIA',
                'descripcion' => 'Capa impermeable liviana con capucha ajustable y ranura trasera para enganchar la correa al arnés.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'impermeables',
                'coleccion' => 'Primavera',
                'colores' => ['Amarillo', 'Azul', 'Rojo'],
                'talles' => ['S', 'M', 'L', 'XL', 'XXL'],
                'precio_base' => 20500.00,
                'imagenes' => [
                    'Amarillo' => [
                        'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?w=800'
                    ],
                    'Azul' => [
                        'https://images.unsplash.com/photo-1581888227599-779811939961?w=800'
                    ],
                    'Rojo' => [
                        'https://images.unsplash.com/photo-1508948956644-0017e845d797?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Camiseta Algodón Casual',
                'sku_base' => 'CAMISETA-ALG',
                'descripcion' => 'Camiseta básica y super elástica de algodón transpirable. Protege la piel sensible y previene la caída excesiva de pelo.',
                'tipo_mascota' => 'gato',
                'categoria' => 'camisetas',
                'coleccion' => 'Picnic',
                'colores' => ['Rosa', 'Blanco', 'Amarillo'],
                'talles' => ['XS', 'S', 'M', 'L'],
                'precio_base' => 8900.00,
                'imagenes' => [
                    'Rosa' => [
                        'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=800'
                    ],
                    'Blanco' => [
                        'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?w=800'
                    ],
                    'Amarillo' => [
                        'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Pijama Enterizo Rayitas',
                'sku_base' => 'PIJAMA-RAYAS',
                'descripcion' => 'Pijama enterizo para mascotas. Tela ultra suave que mantiene la temperatura corporal durante las noches frías.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'pijamas',
                'coleccion' => 'Clásicos',
                'colores' => ['Gris', 'Rosa', 'Azul'],
                'talles' => ['XS', 'S', 'M', 'L'],
                'precio_base' => 11800.00,
                'imagenes' => [
                    'Gris' => [
                        'https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=800'
                    ],
                    'Rosa' => [
                        'https://images.unsplash.com/photo-1526336024174-e58f5cdd8e13?w=800'
                    ],
                    'Azul' => [
                        'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Juguete Mordillo de Goma Dental',
                'sku_base' => 'TOY-MORDILLO',
                'descripcion' => 'Mordillo de caucho natural con ranuras para limpiar sarro y masajear encías mientras juegan.',
                'tipo_mascota' => 'perro',
                'categoria' => 'juguetes',
                'coleccion' => 'Esenciales',
                'colores' => ['Verde', 'Azul', 'Naranja'],
                'talles' => ['M', 'L'],
                'precio_base' => 5400.00,
                'imagenes' => [
                    'Verde' => [
                        'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?w=800'
                    ],
                    'Azul' => [
                        'https://images.unsplash.com/photo-1596492784531-6e6eb5ea9993?w=800'
                    ],
                    'Naranja' => [
                        'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Correa Extensible Retráctil',
                'sku_base' => 'CORREA-RETRACTIL',
                'descripcion' => 'Correa retráctil resistente de 5 metros con sistema de frenado rápido y agarre ergonómico antideslizante.',
                'tipo_mascota' => 'perro',
                'categoria' => 'correas',
                'coleccion' => 'Esenciales',
                'colores' => ['Negro', 'Rojo', 'Gris'],
                'talles' => ['S', 'M', 'L'],
                'precio_base' => 17200.00,
                'imagenes' => [
                    'Negro' => [
                        'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=800'
                    ],
                    'Rojo' => [
                        'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=800'
                    ],
                    'Gris' => [
                        'https://images.unsplash.com/photo-1518020382113-a7e8fc38eac9?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Arnés Antitirones Premium',
                'sku_base' => 'ARNES-ANTITIRON',
                'descripcion' => 'Arnés premium diseñado para evitar tirones. Anillas en pecho y espalda, forro acolchado y reflectante.',
                'tipo_mascota' => 'perro',
                'categoria' => 'arneces',
                'coleccion' => 'Nuevos',
                'colores' => ['Negro', 'Azul', 'Rojo'],
                'talles' => ['S', 'M', 'L', 'XL'],
                'precio_base' => 22500.00,
                'imagenes' => [
                    'Negro' => [
                        'https://images.unsplash.com/photo-1544568100-847a948585b9?w=800'
                    ],
                    'Azul' => [
                        'https://images.unsplash.com/photo-1537151608828-ea2b117b6b86?w=800'
                    ],
                    'Rojo' => [
                        'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=800'
                    ]
                ]
            ],
            [
                'nombre' => 'Cama Nido Nube Desmontable',
                'sku_base' => 'CAMA-NUBE',
                'descripcion' => 'Cama acolchada ortopédica antideslizante con cierre desmontable para un fácil lavado.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'camas',
                'coleccion' => 'Nuevos',
                'colores' => ['Gris', 'Rosa', 'Blanco'],
                'talles' => ['S', 'M', 'L'],
                'precio_base' => 31200.00,
                'imagenes' => [
                    'Gris' => [
                        'https://images.unsplash.com/photo-1541599540903-216a46ca1ad0?w=800'
                    ],
                    'Rosa' => [
                        'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=800'
                    ],
                    'Blanco' => [
                        'https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?w=800'
                    ]
                ]
            ],
        ];

        foreach ($productosBase as $pBase) {
            // Buscar subcategoría
            $categoriaObj = Categoria::where('nombre', $pBase['categoria'])->first();
            if (!$categoriaObj) {
                continue; // Evita errores si la categoría no existe
            }

            // Buscar colección
            $coleccionObj = Coleccion::where('nombre', $pBase['coleccion'])->first();
            $coleccionId = $coleccionObj ? $coleccionObj->id : null;

            // Recorrer los colores permitidos para este producto
            foreach ($pBase['colores'] as $colorNombre) {
                $colorObj = Color::where('nombre', $colorNombre)->first();
                if (!$colorObj) {
                    continue; // Evita errores si el color no existe
                }

                // Generar el sku_color del grupo (Ej: BUZO-POLAR-ROJO)
                $skuColor = $pBase['sku_base'] . '-' . strtoupper($colorNombre);

                // Insertar Imágenes para este SKU_COLOR
                if (isset($pBase['imagenes'][$colorNombre])) {
                    foreach ($pBase['imagenes'][$colorNombre] as $index => $imageUrl) {
                        ProductoImagen::firstOrCreate(
                            [
                                'sku_color' => $skuColor,
                                'url' => $imageUrl
                            ],
                            [
                                'orden' => $index + 1 // El primero (índice 0) tendrá orden 1 (portada)
                            ]
                        );
                    }
                }

                // Recorrer los talles para este producto y crear las variantes únicas
                foreach ($pBase['talles'] as $talle) {
                    // Generar el SKU completo de la variante (Ej: BUZO-POLAR-ROJO-S)
                    $sku = $skuColor . '-' . $talle;

                    // Ajuste ligero de precio según el talle para mayor realismo
                    // (Los talles más grandes añaden un 10% adicional acumulado por nivel)
                    $multiplicador = 1.0;
                    switch ($talle) {
                        case 'S':   $multiplicador = 1.05; break;
                        case 'M':   $multiplicador = 1.10; break;
                        case 'L':   $multiplicador = 1.15; break;
                        case 'XL':  $multiplicador = 1.22; break;
                        case 'XXL': $multiplicador = 1.30; break;
                    }
                    $precioFinal = round($pBase['precio_base'] * $multiplicador, 2);

                    // Insertar variante de producto
                    Producto::firstOrCreate(
                        [
                            'sku_base' => $pBase['sku_base'],
                            'color_id' => $colorObj->id,
                            'talle' => $talle
                        ],
                        [
                            'categoria_id' => $categoriaObj->id,
                            'coleccion_id' => $coleccionId,
                            'nombre' => $pBase['nombre'] . ' - ' . $colorNombre . ' ' . $talle,
                            'descripcion' => $pBase['descripcion'],
                            'tipo_mascota' => $pBase['tipo_mascota'],
                            'sku_color' => $skuColor,
                            'sku' => $sku,
                            'stock' => rand(5, 30),
                            'stock_minimo' => rand(1, 4),
                            'precio' => $precioFinal
                        ]
                    );
                }
            }
        }
    }
}
