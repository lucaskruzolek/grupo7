<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Categoria;
use App\Models\Color;
use App\Models\Coleccion;
use Illuminate\Support\Facades\Storage;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definición de productos base
        $productosBase = [
            // --- NUEVOS PRODUCTOS DE ROPA (De products.txt) ---
            
            // Buzos
            [
                'nombre' => 'Buzo "Urban Paws"',
                'sku_base' => 'BUZO-URBAN',
                'descripcion' => 'Algodón con frisa interior, estampado continuo de huellas estilizadas.',
                'tipo_mascota' => 'perro',
                'categoria' => 'buzos',
                'coleccion' => 'Invierno',
                'colores' => ['Rojo', 'Azul', 'Gris'],
                'talles' => ['XS', 'S', 'M', 'L'],
                'precio_base' => 13500.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Polar "Nordic Bear"',
                'sku_base' => 'BUZO-NORDIC',
                'descripcion' => 'Tela polar de alto gramaje, color sólido con parche bordado.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'buzos',
                'coleccion' => 'Invierno',
                'colores' => ['Gris', 'Negro', 'Azul'],
                'talles' => ['S', 'M', 'L'],
                'precio_base' => 14800.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Buzo "Sporty Paws"',
                'sku_base' => 'BUZO-SPORTY',
                'descripcion' => 'Algodón rústico sin frisar, diseño bicolor con franjas laterales.',
                'tipo_mascota' => 'perro',
                'categoria' => 'buzos',
                'coleccion' => 'Picnic',
                'colores' => ['Negro', 'Rojo'],
                'talles' => ['M', 'L', 'XL'],
                'precio_base' => 12900.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Buzo "Galaxy Tie-Dye"',
                'sku_base' => 'BUZO-GALAXY',
                'descripcion' => 'Algodón elastizado con teñido artesanal patrón galaxia.',
                'tipo_mascota' => 'gato',
                'categoria' => 'buzos',
                'coleccion' => 'Primavera',
                'colores' => ['Violeta', 'Rosa', 'Azul'],
                'talles' => ['XS', 'S', 'M'],
                'precio_base' => 11500.00,
                'imagenes' => []
            ],

            // Suéteres
            [
                'nombre' => 'Suéter "Classic Aspen"',
                'sku_base' => 'SUETER-ASPEN',
                'descripcion' => 'Lana acrílica gruesa, patrón de trenzas en relieve, color entero.',
                'tipo_mascota' => 'perro',
                'categoria' => 'sueteres',
                'coleccion' => 'Invierno',
                'colores' => ['Rojo', 'Gris', 'Verde'],
                'talles' => ['S', 'M', 'L', 'XL'],
                'precio_base' => 15500.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Suéter "Scottish Plaid"',
                'sku_base' => 'SUETER-SCOTTISH',
                'descripcion' => 'Hilo de algodón peinado, patrón tartán tradicional.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'sueteres',
                'coleccion' => 'Invierno',
                'colores' => ['Rojo', 'Azul'],
                'talles' => ['XS', 'S', 'M', 'L'],
                'precio_base' => 16200.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Jacquard "Winter Woods"',
                'sku_base' => 'SUETER-WINTER',
                'descripcion' => 'Tejido de punto cerrado con motivos invernales (pinos y copos).',
                'tipo_mascota' => 'perro',
                'categoria' => 'sueteres',
                'coleccion' => 'Invierno',
                'colores' => ['Verde', 'Gris', 'Rojo'],
                'talles' => ['M', 'L', 'XL'],
                'precio_base' => 17500.00,
                'imagenes' => []
            ],

            // Pecheras
            [
                'nombre' => 'Pechera Acolchada "City Walk"',
                'sku_base' => 'PECHERA-CITY',
                'descripcion' => 'Exterior de nylon matelaseado en rombos, interior forrado.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'pecheras',
                'coleccion' => 'Esenciales',
                'colores' => ['Negro', 'Azul', 'Rojo'],
                'talles' => ['S', 'M', 'L'],
                'precio_base' => 19500.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Pechera "Cozy Fleece"',
                'sku_base' => 'PECHERA-COZY',
                'descripcion' => 'Exterior e interior en tela corderito sintético, herrajes reforzados.',
                'tipo_mascota' => 'perro',
                'categoria' => 'pecheras',
                'coleccion' => 'Invierno',
                'colores' => ['Blanco', 'Gris'],
                'talles' => ['M', 'L', 'XL'],
                'precio_base' => 21000.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Pechera "Denim & Polar"',
                'sku_base' => 'PECHERA-REVERSIBLE',
                'descripcion' => 'Pechera reversible, un lado en tela de jean flexible, reverso en micropolar liso.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'pecheras',
                'coleccion' => 'Esenciales',
                'colores' => ['Azul', 'Gris', 'Negro'],
                'talles' => ['S', 'M', 'L', 'XL'],
                'precio_base' => 18500.00,
                'imagenes' => []
            ],

            // Impermeables
            [
                'nombre' => 'Rompevientos "Storm Walker"',
                'sku_base' => 'IMP-STORM',
                'descripcion' => 'Tela siré resistente al agua con bandas reflectivas de alta visibilidad.',
                'tipo_mascota' => 'perro',
                'categoria' => 'impermeables',
                'coleccion' => 'Primavera',
                'colores' => ['Rojo', 'Amarillo', 'Azul'],
                'talles' => ['S', 'M', 'L', 'XL'],
                'precio_base' => 22000.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Piloto "Yellow Ducklings"',
                'sku_base' => 'IMP-YELLOW',
                'descripcion' => 'PVC translúcido impermeable con estampado de figuras geométricas amarillas.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'impermeables',
                'coleccion' => 'Primavera',
                'colores' => ['Amarillo', 'Blanco'],
                'talles' => ['XS', 'S', 'M'],
                'precio_base' => 19900.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Impermeable "Drizzle Shield"',
                'sku_base' => 'IMP-DRIZZLE',
                'descripcion' => 'Exterior engomado mate, forro interno en algodón a rayas.',
                'tipo_mascota' => 'perro',
                'categoria' => 'impermeables',
                'coleccion' => 'Invierno',
                'colores' => ['Negro', 'Amarillo', 'Rojo'],
                'talles' => ['M', 'L', 'XL'],
                'precio_base' => 24500.00,
                'imagenes' => []
            ],

            // Pijamas
            [
                'nombre' => 'Pijama Térmico',
                'sku_base' => 'PIJAMA-WINTER',
                'descripcion' => 'Micropolar elástico que cubre cuatro patas, bloque de colores sólidos.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'pijamas',
                'coleccion' => 'Invierno',
                'colores' => ['Gris', 'Rosa', 'Azul'],
                'talles' => ['XS', 'S', 'M', 'L'],
                'precio_base' => 13800.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Pijama "Minimal Bones"',
                'sku_base' => 'PIJAMA-MINIMAL',
                'descripcion' => 'Franela de algodón cepillado, fondo pastel con estampado minimalista.',
                'tipo_mascota' => 'perro',
                'categoria' => 'pijamas',
                'coleccion' => 'Clásicos',
                'colores' => ['Rosa', 'Blanco', 'Gris'],
                'talles' => ['S', 'M', 'L'],
                'precio_base' => 12500.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Pijama "Moon & Stars"',
                'sku_base' => 'PIJAMA-MOON',
                'descripcion' => 'Algodón ligero hipoalergénico, patrón nocturno continuo.',
                'tipo_mascota' => 'gato',
                'categoria' => 'pijamas',
                'coleccion' => 'Clásicos',
                'colores' => ['Azul', 'Violeta'],
                'talles' => ['XS', 'S', 'M'],
                'precio_base' => 11900.00,
                'imagenes' => []
            ],

            // Camisetas
            [
                'nombre' => 'Camiseta "Nautical Stripes"',
                'sku_base' => 'CAMISETA-NAUTICAL',
                'descripcion' => 'Jersey de algodón ligero, rayas horizontales contrastantes.',
                'tipo_mascota' => 'ambos',
                'categoria' => 'camisetas',
                'coleccion' => 'Picnic',
                'colores' => ['Rojo', 'Azul', 'Blanco'],
                'talles' => ['XS', 'S', 'M', 'L'],
                'precio_base' => 9500.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Musculosa "Neon Mesh"',
                'sku_base' => 'CAMISETA-NEON',
                'descripcion' => 'Tela dry-fit microperforada, diseño liso con ribetes neón.',
                'tipo_mascota' => 'perro',
                'categoria' => 'camisetas',
                'coleccion' => 'Picnic',
                'colores' => ['Negro', 'Amarillo'],
                'talles' => ['S', 'M', 'L', 'XL'],
                'precio_base' => 8900.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Camiseta "Urban Camo"',
                'sku_base' => 'CAMISETA-URBAN',
                'descripcion' => 'Algodón con spandex, patrón de camuflaje en tonos grises.',
                'tipo_mascota' => 'perro',
                'categoria' => 'camisetas',
                'coleccion' => 'Nuevos',
                'colores' => ['Gris', 'Negro'],
                'talles' => ['M', 'L', 'XL'],
                'precio_base' => 10500.00,
                'imagenes' => []
            ],
            [
                'nombre' => 'Camiseta "Spring Floral"',
                'sku_base' => 'CAMISETA-SPRING',
                'descripcion' => 'Viscosa elástica fresca, patrón de flores pequeñas.',
                'tipo_mascota' => 'gato',
                'categoria' => 'camisetas',
                'coleccion' => 'Primavera',
                'colores' => ['Rosa', 'Blanco', 'Amarillo'],
                'talles' => ['XS', 'S', 'M'],
                'precio_base' => 9900.00,
                'imagenes' => []
            ],

            // --- ACCESORIOS MANTENIDOS ---
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

                // Insertar 3 Imágenes predecibles para este SKU_COLOR
                for ($i = 1; $i <= 3; $i++) {
                    $fileName = '/img/productos/' . strtolower($skuColor) . '-' . $i . '.webp';
                    $imageUrl = Storage::disk('s3')->url($fileName);

                    ProductoImagen::firstOrCreate(
                        [
                            'sku_color' => $skuColor,
                            'orden' => $i
                        ],
                        [
                            'url' => $imageUrl
                        ]
                    );
                }

                // Recorrer los talles para este producto y crear las variantes únicas
                foreach ($pBase['talles'] as $talle) {
                    // Generar el SKU completo de la variante (Ej: BUZO-POLAR-ROJO-S)
                    $sku = $skuColor . '-' . $talle;
                    Producto::updateOrCreate(
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
                            'precio' => $pBase['precio_base']
                        ]
                    );
                }
            }
        }
    }
}
