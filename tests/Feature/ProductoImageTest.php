<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\ImageOptimizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductoImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Force SQLite in-memory database for this test to avoid MySQL dependency issues
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        $this->artisan('migrate');
    }

    public function test_image_optimizer_service_resizes_and_converts_to_webp()
    {
        // Skip this test if GD extension is not available or does not support WebP
        if (!function_exists('imagewebp')) {
            $this->markTestSkipped('GD or WebP support is not available.');
        }

        $service = new ImageOptimizerService();

        // Create a temporary mock image (1500x1000px JPG)
        $file = UploadedFile::fake()->image('test_product.jpg', 1500, 1000);

        $tempPath = $service->convertToWebp($file, 1200, 80);

        $this->assertFileExists($tempPath);
        $this->assertEquals('webp', pathinfo($tempPath, PATHINFO_EXTENSION));

        // Verify dimensions are resized to fit within 1200px
        $size = getimagesize($tempPath);
        $this->assertLessThanOrEqual(1200, $size[0]);
        $this->assertLessThanOrEqual(1200, $size[1]);

        // Clean up
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }

    public function test_upload_image_saves_to_r2_and_database()
    {
        Storage::fake('s3');

        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Admin']);
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id,
        ]);

        $file = UploadedFile::fake()->image('product.png', 500, 500);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.productos.images.upload'), [
                'sku_color' => 'TEST-ROJO',
                'image' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'image' => ['id', 'url', 'orden']
            ]);

        $dbImage = ProductoImagen::first();
        $this->assertNotNull($dbImage);
        $this->assertEquals('TEST-ROJO', $dbImage->sku_color);
        $this->assertEquals(1, $dbImage->orden);

        // Verify the file was stored in R2 (S3 fake)
        $fileName = parse_url($dbImage->url, PHP_URL_PATH);
        $relativeStoragePath = ltrim($fileName, '/');
        if (str_starts_with($relativeStoragePath, 'storage/')) {
            $relativeStoragePath = substr($relativeStoragePath, 8);
        }
        Storage::disk('s3')->assertExists($relativeStoragePath);
    }

    public function test_delete_image_removes_from_r2_and_database_and_reorders()
    {
        Storage::fake('s3');

        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Admin']);
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id,
        ]);

        // Put fake file in R2
        Storage::disk('s3')->put('productos/test-rojo_1.webp', 'fake content');
        Storage::disk('s3')->put('productos/test-rojo_2.webp', 'fake content');

        $img1 = ProductoImagen::create([
            'sku_color' => 'TEST-ROJO',
            'url' => 'https://r2.test.com/productos/test-rojo_1.webp',
            'orden' => 1,
        ]);

        $img2 = ProductoImagen::create([
            'sku_color' => 'TEST-ROJO',
            'url' => 'https://r2.test.com/productos/test-rojo_2.webp',
            'orden' => 2,
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson(route('admin.productos.images.delete', ['id' => $img1->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify first image is deleted from storage and DB
        Storage::disk('s3')->assertMissing('productos/test-rojo_1.webp');
        $this->assertDatabaseMissing('producto_imagenes', ['id' => $img1->id]);

        // Verify second image has reordered to 1
        $img2->refresh();
        $this->assertEquals(1, $img2->orden);
    }

    public function test_set_cover_image_sets_order_to_one()
    {
        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Admin']);
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id,
        ]);

        $img1 = ProductoImagen::create([
            'sku_color' => 'TEST-ROJO',
            'url' => 'https://r2.test.com/productos/test-rojo_1.webp',
            'orden' => 1,
        ]);

        $img2 = ProductoImagen::create([
            'sku_color' => 'TEST-ROJO',
            'url' => 'https://r2.test.com/productos/test-rojo_2.webp',
            'orden' => 2,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.productos.images.cover', ['id' => $img2->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $img1->refresh();
        $img2->refresh();

        $this->assertEquals(1, $img2->orden);
        $this->assertEquals(2, $img1->orden);
    }
}
