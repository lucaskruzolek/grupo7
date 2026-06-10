@extends('frontend.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
@endsection

@section('contenido')
    <!-- Banner Hero del Carrito -->
    <section class="banner-hero theme-neutral py-4 py-md-5 mb-5">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col text-center text-md-start">
                    <h1 class="banner-title mb-3 d-inline-block anim-fade-down" style="--anim-order: 1;">
                        Tu Carrito <span class="paw-icon"></span>
                    </h1>
                    <p class="banner-subtitle anim-fade-down mb-0" style="--anim-order: 2;">
                        Revisá tus productos seleccionados y confirmá tu pedido de Pet Threads.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido del Carrito -->
    <div class="container mb-5">
        <!-- Contenedor de Alertas -->
        @if (session('exito'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
                <strong>✨ ¡Éxito!</strong> {{ session('exito') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
                <strong>⚠️ Error:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
                <strong>⚠️ Errores de validación:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (!$carrito || $carrito->detalles->isEmpty())
            <!-- Estado Carrito Vacío -->
            <div class="empty-cart-container my-5 py-5 anim-fade-down">
                <div class="d-flex justify-content-center mb-3">
                    <div style="background-color: var(--coral-100); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="paw-icon" style="font-size: 2.5rem; opacity: 0.9;"></span>
                    </div>
                </div>
                <h2 class="empty-cart-title h3 mt-3">Tu carrito está vacío</h2>
                <p class="empty-cart-text text-muted">Parece que aún no has agregado ningún producto a tu carrito. ¡Explorá nuestras colecciones y encontrá las mejores prendas para tu mascota!</p>
                <a href="{{ route('productos.index') }}" class="btn btn-primary rounded-pill px-4 py-2 mt-2">
                    Ir a la tienda
                </a>
            </div>
        @else
            <!-- Carrito con items -->
            <div class="row g-4">
                <!-- Columna Izquierda: Listado de Productos -->
                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($carrito->detalles as $index => $detalle)
                            @php
                                $img = $detalle->producto->imagenes->sortBy('orden')->first();
                                $imgUrl = $img ? $img->url : asset('img/placeholder-petthreads.jpg');
                            @endphp
                            <div class="cart-item-card d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3" style="animation-delay: {{ $index * 0.05 }}s;">
                                <!-- Imagen e Info del Producto -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="cart-item-img-container">
                                        <img src="{{ $imgUrl }}" alt="{{ $detalle->producto->nombre }}" class="cart-item-img">
                                    </div>
                                    <div>
                                        <h3 class="cart-item-title mb-1">{{ $detalle->producto->nombre_base }}</h3>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-light text-secondary border">Talle: {{ $detalle->producto->talle }}</span>
                                            @if($detalle->producto->color)
                                                <span class="badge bg-light text-secondary border d-flex align-items-center gap-1">
                                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: {{ $detalle->producto->color->hex_code }}; border: 1px solid #ccc;"></span>
                                                    Color: {{ $detalle->producto->color->nombre }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($detalle->producto->stock < 5)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">¡Sólo {{ $detalle->producto->stock }} unidades!</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Precio, Cantidad, Subtotal y Acción -->
                                <div class="d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1 flex-md-grow-0 gap-3 gap-md-4 w-100 w-md-auto">
                                    <!-- Precio Unitario -->
                                    <div class="text-md-end">
                                        <span class="text-muted d-block d-md-none small">Precio</span>
                                        <span class="price-tag">${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</span>
                                    </div>

                                    <!-- Selector de Cantidad -->
                                    <div>
                                        <span class="text-muted d-block d-md-none small mb-1">Cantidad</span>
                                        <div class="quantity-selector">
                                            <!-- Decrementar -->
                                            <form action="{{ route('carrito.actualizar', $detalle->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cantidad" value="{{ $detalle->cantidad - 1 }}">
                                                <button type="submit" class="quantity-btn" {{ $detalle->cantidad <= 1 ? 'disabled' : '' }} title="Disminuir cantidad">
                                                    &minus;
                                                </button>
                                            </form>

                                            <!-- Input numérico directo -->
                                            <form action="{{ route('carrito.actualizar', $detalle->id) }}" method="POST" class="m-0" id="form-qty-{{ $detalle->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="cantidad" value="{{ $detalle->cantidad }}" min="1" max="{{ $detalle->producto->stock }}" 
                                                       class="quantity-input" onchange="this.form.submit()" title="Cantidad">
                                            </form>

                                            <!-- Incrementar -->
                                            <form action="{{ route('carrito.actualizar', $detalle->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cantidad" value="{{ $detalle->cantidad + 1 }}">
                                                <button type="submit" class="quantity-btn" {{ $detalle->cantidad >= $detalle->producto->stock ? 'disabled' : '' }} title="Aumentar cantidad">
                                                    &plus;
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Subtotal del Item -->
                                    <div class="text-md-end" style="min-width: 90px;">
                                        <span class="text-muted d-block d-md-none small">Subtotal</span>
                                        <span class="subtotal-tag">${{ number_format($detalle->subtotal, 2, ',', '.') }}</span>
                                    </div>

                                    <!-- Eliminar Ítem -->
                                    <div>
                                        <form action="{{ route('carrito.eliminar', $detalle->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-remove-item" title="Eliminar del carrito">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Columna Derecha: Resumen del Pedido -->
                <div class="col-lg-4">
                    <div class="cart-summary-card position-sticky" style="top: 100px;">
                        <h2 class="summary-heading h4">Resumen del pedido</h2>

                        <div class="summary-row">
                            <span class="text-secondary">Subtotal</span>
                            <span class="fw-semibold text-dark">${{ number_format($carrito->total, 2, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-secondary">Envío</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>

                        <div class="summary-row total-row">
                            <span>Total</span>
                            <span>${{ number_format($carrito->total, 2, ',', '.') }}</span>
                        </div>

                        <!-- Formulario de Checkout / Confirmar Compra -->
                        <form action="{{ route('carrito.checkout') }}" method="POST" class="mt-4" id="form-checkout">
                            @csrf
                            
                            <div class="mb-4">
                                <h3 class="payment-methods-title">Forma de Pago</h3>
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($formasPago as $fp)
                                        <label for="fp_{{ $fp->id }}" class="payment-method-card align-items-center">
                                            <input type="radio" name="forma_pago_id" id="fp_{{ $fp->id }}" value="{{ $fp->id }}" 
                                                   class="payment-method-radio form-check-input" required {{ $loop->first ? 'checked' : '' }}>
                                            <span class="payment-method-label">{{ $fp->descripcion }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                                <span>Confirmar compra</span>
                                <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/paw.svg') }}'); mask-image: url('{{ asset('img/icons/paw.svg') }}'); width: 16px; height: 16px; background-color: currentColor;"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
