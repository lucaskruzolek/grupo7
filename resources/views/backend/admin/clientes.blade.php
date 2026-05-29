@extends('backend.admin.layout')

@section('contenido')
<div class="container-fluid px-0">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 text-dark mb-1">Clientes</h1>
            <p class="text-muted mb-0">Gestión de clientes registrados en Pet Threads.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="surface-card p-5 text-center align-items-center justify-content-center shadow-sm" style="min-height: 400px;">
                <div class="fs-1 mb-3">👥</div>
                <h2 class="h3 text-dark fw-bold mb-2">Próximamente</h2>
                <p class="text-muted mb-0" style="max-width: 480px;">
                    Estamos trabajando en la implementación de la sección de clientes. Muy pronto podrás ver la información de los usuarios registrados, sus direcciones y su historial comercial.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
