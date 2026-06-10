<?php
 
namespace Tests\Feature;
 
use App\Models\Consulta;
use App\Models\Rol;
use App\Models\Usuario;
use Tests\TestCase;
 
class ConsultaTest extends TestCase
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
 
    public function test_user_can_submit_valid_consulta()
    {
        $data = [
            'nombre' => 'Lucas Pérez',
            'email' => 'lucas@example.com',
            'telefono' => '1122334455',
            'pedido' => '#12345',
            'asunto' => 'consulta',
            'mensaje' => 'Hola, tengo una pregunta sobre el stock de buzos.',
        ];
 
        $response = $this->post(route('consultas.store'), $data);
 
        $response->assertStatus(200);
        $response->assertViewIs('frontend.exito-consulta');
 
        $this->assertDatabaseHas('consultas', [
            'nombre' => 'Lucas Pérez',
            'email' => 'lucas@example.com',
            'asunto' => 'consulta',
            'mensaje' => 'Hola, tengo una pregunta sobre el stock de buzos.',
        ]);
    }
 
    public function test_user_cannot_submit_invalid_consulta()
    {
        // Falta nombre y mensaje
        $data = [
            'email' => 'correo-invalido',
            'asunto' => 'invalido',
        ];
 
        $response = $this->post(route('consultas.store'), $data);
 
        $response->assertSessionHasErrors(['nombre', 'email', 'asunto', 'mensaje']);
        $this->assertDatabaseCount('consultas', 0);
    }
 
    public function test_admin_can_toggle_leido()
    {
        $consulta = Consulta::create([
            'nombre' => 'Juan Gómez',
            'email' => 'juan@gomez.com',
            'asunto' => 'reclamo',
            'mensaje' => 'Tuve un inconveniente con el envío.',
            'leido' => false,
        ]);
 
        $response = $this->actingAs($this->admin)
            ->post(route('admin.consultas.toggle-leido', $consulta->id));
 
        $response->assertRedirect();
        $response->assertSessionHas('exito');
 
        $this->assertTrue($consulta->fresh()->leido);
    }
 
    public function test_admin_can_toggle_respondido()
    {
        $consulta = Consulta::create([
            'nombre' => 'Juan Gómez',
            'email' => 'juan@gomez.com',
            'asunto' => 'reclamo',
            'mensaje' => 'Tuve un inconveniente con el envío.',
            'respondido' => false,
        ]);
 
        $response = $this->actingAs($this->admin)
            ->post(route('admin.consultas.toggle-respondido', $consulta->id));
 
        $response->assertRedirect();
        $response->assertSessionHas('exito');
 
        $this->assertTrue($consulta->fresh()->respondido);
    }
 
    public function test_admin_can_delete_consulta()
    {
        $consulta = Consulta::create([
            'nombre' => 'Juan Gómez',
            'email' => 'juan@gomez.com',
            'asunto' => 'reclamo',
            'mensaje' => 'Tuve un inconveniente con el envío.',
        ]);
 
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.consultas.destroy', $consulta->id));
 
        $response->assertRedirect();
        $response->assertSessionHas('exito');
 
        $this->assertSoftDeleted('consultas', [
            'id' => $consulta->id,
        ]);
    }

    public function test_guest_cannot_access_admin_consultas()
    {
        $response = $this->get(route('admin.consultas'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_consultas_and_filter_by_period()
    {
        // Crear algunas consultas en fechas distintas
        Consulta::create([
            'nombre' => 'Consulta Reciente',
            'email' => 'reciente@test.com',
            'asunto' => 'consulta',
            'mensaje' => 'Mensaje reciente.',
        ]);

        $antigua = Consulta::create([
            'nombre' => 'Consulta Antigua',
            'email' => 'antigua@test.com',
            'asunto' => 'reclamo',
            'mensaje' => 'Mensaje antiguo.',
        ]);
        $antigua->created_at = now()->subDays(10);
        $antigua->save();

        // Probar acceso sin filtros (por defecto period=month)
        $response = $this->actingAs($this->admin)->get(route('admin.consultas'));
        $response->assertStatus(200);
        $response->assertViewIs('backend.admin.consultas');
        $response->assertViewHas('consultas');
        $response->assertViewHas('totalConsultas');

        // Probar filtro period=today
        $responseToday = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'today']));
        $responseToday->assertStatus(200);
        // Debería ver sólo la consulta reciente
        $this->assertEquals(1, $responseToday->viewData('totalConsultas'));

        // Probar filtro period=all
        $responseAll = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'all']));
        $responseAll->assertStatus(200);
        $this->assertEquals(2, $responseAll->viewData('totalConsultas'));
    }

    public function test_admin_can_filter_consultas_by_estado_asunto_and_search()
    {
        // Limpiar consultas previas creadas en setUp
        Consulta::truncate();

        // 1. Create a "nuevo" (unread, not responded) consulta
        $cNuevo = Consulta::create([
            'nombre' => 'María Luján',
            'email' => 'maria@lujan.com',
            'asunto' => 'consulta',
            'mensaje' => 'Hola, tengo una pregunta.',
            'leido' => false,
            'respondido' => false,
        ]);

        // 2. Create a "leido" (read, not responded) consulta
        $cLeido = Consulta::create([
            'nombre' => 'Carlos M.',
            'email' => 'carlos@m.com',
            'asunto' => 'reclamo',
            'mensaje' => 'Tengo un problema.',
            'leido' => true,
            'respondido' => false,
        ]);

        // 3. Create a "respondido" (read and responded) consulta
        $cRespondido = Consulta::create([
            'nombre' => 'Jorge Ramírez',
            'email' => 'jorge@ramirez.com',
            'asunto' => 'devolucion',
            'mensaje' => 'Quiero una devolución.',
            'leido' => true,
            'respondido' => true,
        ]);

        // Filter by state "nuevo"
        $response = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'all', 'estado' => 'nuevo']));
        $response->assertStatus(200);
        $consultas = $response->viewData('consultas');
        $this->assertEquals(1, $consultas->count());
        $this->assertEquals($cNuevo->id, $consultas->first()->id);

        // Filter by state "leido"
        $response = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'all', 'estado' => 'leido']));
        $response->assertStatus(200);
        $consultas = $response->viewData('consultas');
        $this->assertEquals(1, $consultas->count());
        $this->assertEquals($cLeido->id, $consultas->first()->id);

        // Filter by state "respondido"
        $response = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'all', 'estado' => 'respondido']));
        $response->assertStatus(200);
        $consultas = $response->viewData('consultas');
        $this->assertEquals(1, $consultas->count());
        $this->assertEquals($cRespondido->id, $consultas->first()->id);

        // Filter by asunto "reclamo"
        $response = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'all', 'asunto' => 'reclamo']));
        $response->assertStatus(200);
        $consultas = $response->viewData('consultas');
        $this->assertEquals(1, $consultas->count());
        $this->assertEquals($cLeido->id, $consultas->first()->id);

        // Filter by search query "devolución"
        $response = $this->actingAs($this->admin)->get(route('admin.consultas', ['period' => 'all', 'search' => 'devolución']));
        $response->assertStatus(200);
        $consultas = $response->viewData('consultas');
        $this->assertEquals(1, $consultas->count());
        $this->assertEquals($cRespondido->id, $consultas->first()->id);
    }
}
