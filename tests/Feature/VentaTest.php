<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Color;
use App\Models\FormaPago;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaTest extends TestCase
{
    use RefreshDatabase;

    protected $client;
    protected $admin;
    protected $producto;
    protected $formaPago;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar SQLite en memoria para pruebas rápidas
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]]);

        $this->artisan('migrate');

        // Roles
        $rolCliente = Rol::create(['nombre' => 'client', 'descripcion' => 'Cliente']);
        $rolAdmin = Rol::create(['nombre' => 'admin', 'descripcion' => 'Admin']);

        // Usuarios
        $this->client = Usuario::create([
            'nombre'   => 'Juan',
            'apellido' => 'Perez',
            'email'    => 'juan@test.com',
            'password' => bcrypt('password'),
            'rol_id'   => $rolCliente->id,
        ]);

        $this->admin = Usuario::create([
            'nombre'   => 'Admin',
            'apellido' => 'Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id'   => $rolAdmin->id,
        ]);

        // Categoria, Color, FormaPago
        $cat = Categoria::create(['nombre' => 'Ropa', 'pide_talle' => true, 'pide_color' => true]);
        $color = Color::create(['nombre' => 'Rojo', 'hex_code' => '#FF0000']);
        $this->formaPago = FormaPago::create(['id' => 1, 'descripcion' => 'Efectivo']);

        // Producto (Variante)
        $this->producto = Producto::create([
            'categoria_id' => $cat->id,
            'color_id'     => $color->id,
            'nombre'       => 'Buzo Polar Rojo M',
            'sku_base'     => 'BUZO-POLAR',
            'sku_color'    => 'BUZO-POLAR-ROJO',
            'sku'          => 'BUZO-POLAR-ROJO-M',
            'talle'        => 'M',
            'stock'        => 10,
            'precio'       => 1500.00,
        ]);
    }

    public function test_add_to_cart_creates_cart_and_stores_item()
    {
        $response = $this->actingAs($this->client)
            ->post(route('carrito.agregar'), [
                'producto_id' => $this->producto->id,
                'cantidad'    => 2,
            ]);

        $response->assertRedirect(route('carrito.ver'));
        $response->assertSessionHas('exito');

        $this->assertDatabaseHas('ventas', [
            'usuario_id' => $this->client->id,
            'estado'     => 'CARRITO',
            'total'      => 3000.00,
        ]);

        $this->assertDatabaseHas('venta_detalles', [
            'producto_id'     => $this->producto->id,
            'cantidad'        => 2,
            'precio_unitario' => 1500.00,
            'subtotal'        => 3000.00,
        ]);
    }

    public function test_add_to_cart_fails_if_quantity_exceeds_stock()
    {
        $response = $this->actingAs($this->client)
            ->post(route('carrito.agregar'), [
                'producto_id' => $this->producto->id,
                'cantidad'    => 11, // Excede el stock de 10
            ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('ventas', [
            'usuario_id' => $this->client->id,
        ]);
    }

    public function test_update_cart_quantity_updates_totals()
    {
        // Generar carrito de forma manual
        $venta = Venta::create([
            'usuario_id' => $this->client->id,
            'estado'     => 'CARRITO',
            'total'      => 1500.00,
        ]);

        $detalle = VentaDetalle::create([
            'venta_id'        => $venta->id,
            'producto_id'     => $this->producto->id,
            'cantidad'        => 1,
            'precio_unitario' => 1500.00,
            'subtotal'        => 1500.00,
        ]);

        $response = $this->actingAs($this->client)
            ->patch(route('carrito.actualizar', $detalle->id), [
                'cantidad' => 3,
            ]);

        $response->assertRedirect(route('carrito.ver'));
        $response->assertSessionHas('exito');

        $this->assertDatabaseHas('venta_detalles', [
            'id'       => $detalle->id,
            'cantidad' => 3,
            'subtotal' => 4500.00,
        ]);

        $this->assertDatabaseHas('ventas', [
            'id'    => $venta->id,
            'total' => 4500.00,
        ]);
    }

    public function test_update_cart_quantity_fails_if_exceeds_stock()
    {
        $venta = Venta::create([
            'usuario_id' => $this->client->id,
            'estado'     => 'CARRITO',
            'total'      => 1500.00,
        ]);

        $detalle = VentaDetalle::create([
            'venta_id'        => $venta->id,
            'producto_id'     => $this->producto->id,
            'cantidad'        => 1,
            'precio_unitario' => 1500.00,
            'subtotal'        => 1500.00,
        ]);

        $response = $this->actingAs($this->client)
            ->patch(route('carrito.actualizar', $detalle->id), [
                'cantidad' => 12, // Excede stock
            ]);

        $response->assertSessionHasErrors();
        
        $this->assertDatabaseHas('venta_detalles', [
            'id'       => $detalle->id,
            'cantidad' => 1,
        ]);
    }

    public function test_remove_from_cart_deletes_item()
    {
        $venta = Venta::create([
            'usuario_id' => $this->client->id,
            'estado'     => 'CARRITO',
            'total'      => 1500.00,
        ]);

        $detalle = VentaDetalle::create([
            'venta_id'        => $venta->id,
            'producto_id'     => $this->producto->id,
            'cantidad'        => 1,
            'precio_unitario' => 1500.00,
            'subtotal'        => 1500.00,
        ]);

        $response = $this->actingAs($this->client)
            ->delete(route('carrito.eliminar', $detalle->id));

        $response->assertRedirect(route('carrito.ver'));
        $this->assertDatabaseMissing('venta_detalles', [
            'id' => $detalle->id,
        ]);
    }

    public function test_checkout_converts_cart_and_deducts_stock()
    {
        $venta = Venta::create([
            'usuario_id' => $this->client->id,
            'estado'     => 'CARRITO',
            'total'      => 4500.00,
        ]);

        VentaDetalle::create([
            'venta_id'        => $venta->id,
            'producto_id'     => $this->producto->id,
            'cantidad'        => 3,
            'precio_unitario' => 1500.00,
            'subtotal'        => 4500.00,
        ]);

        $response = $this->actingAs($this->client)
            ->post(route('carrito.checkout'), [
                'forma_pago_id' => $this->formaPago->id,
            ]);

        $response->assertRedirect(route('inicio'));
        $response->assertSessionHas('exito');

        $this->assertDatabaseHas('ventas', [
            'id'            => $venta->id,
            'estado'        => 'CONFIRMADO',
            'forma_pago_id' => $this->formaPago->id,
        ]);

        // Stock original: 10. Se compraron 3. Nuevo stock: 7.
        $this->producto->refresh();
        $this->assertEquals(7, $this->producto->stock);
    }

    public function test_checkout_fails_if_stock_became_insufficient()
    {
        $venta = Venta::create([
            'usuario_id' => $this->client->id,
            'estado'     => 'CARRITO',
            'total'      => 15000.00,
        ]);

        VentaDetalle::create([
            'venta_id'        => $venta->id,
            'producto_id'     => $this->producto->id,
            'cantidad'        => 8,
            'precio_unitario' => 1500.00,
            'subtotal'        => 12000.00,
        ]);

        // Simular que otro usuario compró stock en paralelo reduciéndolo a 5
        $this->producto->update(['stock' => 5]);

        $response = $this->actingAs($this->client)
            ->post(route('carrito.checkout'), [
                'forma_pago_id' => $this->formaPago->id,
            ]);

        $response->assertSessionHasErrors();
        
        $venta->refresh();
        $this->assertEquals('CARRITO', $venta->estado);

        $this->producto->refresh();
        $this->assertEquals(5, $this->producto->stock); // El stock se mantiene intacto
    }
}
