@extends('frontend.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
@endsection

@section('contenido')
<section class="container my-3 py-3 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="p-5 rounded-4 shadow-sm anim-fade-down theme-green" style="background-color: var(--green-100); border: 1px solid var(--color-border); max-width: 550px; margin: 0 auto;">
                
                <!-- Animación e Icono -->
                <div class="d-flex justify-content-center mb-4">
                    <div style="background-color: var(--neutral-50); width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                        <!-- Icono check verde -->
                        <div class="icon-mask" style="width: 60px; height: 60px; background-color: var(--green-500); -webkit-mask-image: url('{{ asset('img/icons/check.svg') }}'); mask-image: url('{{ asset('img/icons/check.svg') }}');"></div>
                    </div>
                </div>

                <h1 class="mb-3 fw-bold" style="color: var(--brand-dark); font-family: var(--font-heading); font-size: 2.25rem;">¡Compra Confirmada!</h1>
                
                <p class="mb-4" style="color: var(--color-text-secondary); font-size: 1.05rem;">
                    ¡Muchas gracias por elegirnos! Tu pedido ha sido procesado de manera exitosa.
                </p>

                <!-- Tarjeta con resumen del Pedido -->
                <div class="p-4 rounded-3 text-start mb-2" style="background-color: var(--neutral-50); border: 1px solid rgba(0,0,0,0.05);">
                    <h5 class="mb-3 fw-semibold border-bottom pb-2" style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text); font-family: var(--font-main);">
                        Resumen del Pedido
                    </h5>
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                        <span class="text-secondary">Nro. de Pedido:</span>
                        <span class="fw-bold text-dark">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                        <span class="text-secondary">Forma de Pago:</span>
                        <span class="fw-semibold text-dark">{{ $venta->formaPago ? $venta->formaPago->descripcion : 'No especificada' }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top mt-2" style="font-size: 1.1rem;">
                        <span class="fw-bold text-dark">Total:</span>
                        <span class="fw-bold text-success">${{ number_format($venta->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('compras.factura', $venta->id) }}" target="_blank" class="btn btn-download-link w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <span>Descargar Factura</span>
                    </a>
                    
                    <a href="{{ route('usuario.cuenta') }}" class="btn btn-outline-secondary w-100 py-2.5 px-3 rounded-3 fw-semibold" style="border-width: 2px;">
                        Ver mis compras
                    </a>

                    <a href="{{ route('productos.index') }}" class="btn btn-primary w-100 py-2.5 px-3 rounded-3 fw-semibold">
                        Seguir comprando
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
