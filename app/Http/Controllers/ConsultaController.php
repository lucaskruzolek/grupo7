<?php
 
namespace App\Http\Controllers;
 
use App\Models\Consulta;
use Illuminate\Http\Request;
 
class ConsultaController extends Controller
{
    /**
     * Muestra el listado de consultas en el panel de administración.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        $startDate = null;
        $endDate = null;

        if ($period !== 'all') {
            switch ($period) {
                case 'today':
                    $startDate = \Carbon\Carbon::today()->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfDay();
                    break;
                case '7days':
                    $startDate = \Carbon\Carbon::today()->subDays(6)->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfDay();
                    break;
                case 'month':
                    $startDate = \Carbon\Carbon::today()->startOfMonth()->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfMonth()->endOfDay();
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
                        $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
                    } else {
                        // Caemos por defecto en el mes actual si es custom pero no hay fechas
                        $startDate = \Carbon\Carbon::today()->startOfMonth()->startOfDay();
                        $endDate = \Carbon\Carbon::today()->endOfMonth()->endOfDay();
                    }
                    break;
            }
        }

        // Construir queries base
        $queryTotal = Consulta::query();
        $queryNuevas = Consulta::where('leido', false);
        $queryRespondidas = Consulta::where('respondido', true);
        $queryLeidas = Consulta::where('leido', true)->where('respondido', false);

        $queryList = Consulta::latest();

        // Aplicar filtros de fecha si aplican
        if ($startDate && $endDate) {
            $queryTotal->whereBetween('created_at', [$startDate, $endDate]);
            $queryNuevas->whereBetween('created_at', [$startDate, $endDate]);
            $queryRespondidas->whereBetween('created_at', [$startDate, $endDate]);
            $queryLeidas->whereBetween('created_at', [$startDate, $endDate]);
            $queryList->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Aplicar filtro por estado si aplica
        if ($request->filled('estado') && $request->input('estado') !== 'all') {
            $estado = $request->input('estado');
            if ($estado === 'nuevo') {
                $queryList->where('leido', false);
            } elseif ($estado === 'leido') {
                $queryList->where('leido', true)->where('respondido', false);
            } elseif ($estado === 'respondido') {
                $queryList->where('respondido', true);
            }
        }

        // Aplicar filtro por asunto si aplica
        if ($request->filled('asunto') && $request->input('asunto') !== 'all') {
            $queryList->where('asunto', $request->input('asunto'));
        }

        // Aplicar filtro por búsqueda de texto
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $queryList->where(function ($q) use ($search) {
                $q->where('id', 'like', $search)
                  ->orWhere('nombre', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('mensaje', 'like', $search)
                  ->orWhere('pedido', 'like', $search);
            });
        }

        // Obtener KPIs agregados
        $totalConsultas = $queryTotal->count();
        $nuevasConsultas = $queryNuevas->count();
        $respondidasConsultas = $queryRespondidas->count();
        $leidasConsultas = $queryLeidas->count();

        $consultas = $queryList->paginate(10);

        return view('backend.admin.consultas', compact(
            'consultas',
            'totalConsultas',
            'nuevasConsultas',
            'respondidasConsultas',
            'leidasConsultas'
        ));
    }

    /**
     * Almacena una nueva consulta enviada desde el formulario público.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'pedido'   => 'nullable|string|max:50',
            'asunto'   => 'required|string|in:consulta,reclamo,devolucion,otro',
            'mensaje'  => 'required|string',
        ]);
 
        // Crear consulta y asociar el ID del usuario si está autenticado
        Consulta::create(array_merge($validated, [
            'usuario_id' => auth()->id(),
        ]));
 
        return view('frontend.exito-consulta');
    }
 
    /**
     * Alterna el estado de lectura de una consulta desde el backend.
     */
    public function toggleLeido($id)
    {
        $consulta = Consulta::findOrFail($id);
        $newLeido = !$consulta->leido;
        $update = [
            'leido' => $newLeido,
        ];
        if (!$newLeido) {
            // Si se marca como no leído (Nuevo), no puede estar respondido
            $update['respondido'] = false;
        }
        $consulta->update($update);
 
        return redirect()->back()->with('exito', 'El estado de lectura de la consulta fue actualizado.');
    }
 
    /**
     * Alterna el estado de respuesta de una consulta desde el backend.
     */
    public function toggleRespondido($id)
    {
        $consulta = Consulta::findOrFail($id);
        $newRespondido = !$consulta->respondido;
        $update = [
            'respondido' => $newRespondido,
        ];
        if ($newRespondido) {
            // Respondido presupone leído
            $update['leido'] = true;
        }
        $consulta->update($update);
 
        return redirect()->back()->with('exito', 'El estado de respuesta de la consulta fue actualizado.');
    }
 
    /**
     * Elimina (soft delete) una consulta desde el panel de administración.
     */
    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();
 
        return redirect()->back()->with('exito', 'La consulta fue eliminada correctamente.');
    }
}
