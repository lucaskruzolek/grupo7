<?php

namespace Tests\Feature;

use App\Models\Coleccion;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ColeccionTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar SQLite en memoria para pruebas rápidas
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        $this->artisan('migrate');

        // Crear rol y usuario administrador
        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Admin']);
        $this->admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id,
        ]);
    }

    public function test_index_lists_collections_and_returns_view()
    {
        Coleccion::create([
            'nombre' => 'Colección Invierno',
            'descripcion' => 'Ropa abrigada',
            'url_imagen' => 'https://unsplash.com/photos/1',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.colecciones.index'));

        $response->assertStatus(200);
        $response->assertViewIs('backend.admin.colecciones');
        $response->assertViewHas('colecciones');
    }

    public function test_store_saves_collection_with_url()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.colecciones.store'), [
                'nombre' => 'Verano',
                'descripcion' => 'Ropa fresca',
                'url_imagen' => 'https://example.com/verano.jpg',
            ]);

        $response->assertRedirect(route('admin.colecciones.index'));
        $response->assertSessionHas('exito');

        $this->assertDatabaseHas('colecciones', [
            'nombre' => 'Verano',
            'descripcion' => 'Ropa fresca',
            'url_imagen' => 'https://example.com/verano.jpg',
        ]);
    }

    public function test_store_saves_collection_with_uploaded_file_to_r2()
    {
        Storage::fake('s3');

        $file = UploadedFile::fake()->image('coleccion_primavera.png', 800, 600);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.colecciones.store'), [
                'nombre' => 'Primavera',
                'descripcion' => 'Colores vivos',
                'imagen_file' => $file,
            ]);

        $response->assertRedirect(route('admin.colecciones.index'));
        $response->assertSessionHas('exito');

        $coleccion = Coleccion::where('nombre', 'Primavera')->first();
        $this->assertNotNull($coleccion);
        $this->assertNotNull($coleccion->url_imagen);

        // Extraer ruta relativa de R2 a partir de url_imagen
        $urlPath = parse_url($coleccion->url_imagen, PHP_URL_PATH);
        $relativeStoragePath = ltrim($urlPath, '/');
        if (str_starts_with($relativeStoragePath, 'storage/')) {
            $relativeStoragePath = substr($relativeStoragePath, 8);
        }
        
        Storage::disk('s3')->assertExists($relativeStoragePath);
    }

    public function test_update_modifies_collection_fields()
    {
        $coleccion = Coleccion::create([
            'nombre' => 'Otoño Original',
            'descripcion' => 'Descripción vieja',
            'url_imagen' => 'https://example.com/otono.jpg',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.colecciones.update', $coleccion->id), [
                'nombre' => 'Otoño Editado',
                'descripcion' => 'Nueva descripción',
                'url_imagen' => 'https://example.com/nuevo_otono.jpg',
            ]);

        $response->assertRedirect(route('admin.colecciones.index'));
        $response->assertSessionHas('exito');

        $this->assertDatabaseHas('colecciones', [
            'id' => $coleccion->id,
            'nombre' => 'Otoño Editado',
            'descripcion' => 'Nueva descripción',
            'url_imagen' => 'https://example.com/nuevo_otono.jpg',
        ]);
    }

    public function test_destroy_deletes_collection_and_removes_r2_image()
    {
        Storage::fake('s3');
        
        // Simular archivo guardado en R2
        Storage::disk('s3')->put('colecciones/temporada_123.jpg', 'content');

        $coleccion = Coleccion::create([
            'nombre' => 'Campaña Borrar',
            'descripcion' => 'Por eliminar',
            'url_imagen' => 'https://r2.example.com/colecciones/temporada_123.jpg',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.colecciones.destroy', $coleccion->id));

        $response->assertRedirect(route('admin.colecciones.index'));
        $response->assertSessionHas('exito');

        $this->assertSoftDeleted('colecciones', [
            'id' => $coleccion->id
        ]);

        Storage::disk('s3')->assertMissing('colecciones/temporada_123.jpg');
    }
}
