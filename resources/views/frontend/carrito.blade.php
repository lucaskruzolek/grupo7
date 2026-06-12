@extends('frontend.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
@endsection

@section('contenido')
    @php
        $user = auth()->user();
        $tieneDatosCompletos = $user && !empty($user->telefono) && !empty($user->direccion) && !empty($user->localidad) && !empty($user->provincia) && !empty($user->codigo_postal);
    @endphp
    <!-- Banner Hero del Carrito -->
    <section class="banner-hero theme-neutral py-1 py-md-4 mb-3">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col text-center text-md-start">
                    <h1 class="banner-title mb-2 d-inline-block anim-fade-down" style="--anim-order: 1;">
                        Tu Carrito <span class="paw-icon"></span>
                    </h1>
                    <p class="banner-subtitle anim-fade-down mb-0" style="--anim-order: 2;">
                        Revisá tus productos seleccionados y confirmá tu pedido.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido del Carrito -->
    <div class="container cart-container mb-5">
        <!-- Contenedor de Alertas -->
        @if (session('exito'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
                <strong>¡Éxito!</strong> {{ session('exito') }}
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
            <div class="row g-2 g-md-4">
                <!-- Columna Izquierda: Listado de Productos -->
                <div class="col-lg-9 col-xl-8">
                    <div class="cart-items-container">
                        <!-- Cabecera de la tabla de items (solo visible en desktop) -->
                        <div class="cart-header-row">
                            <div class="flex-grow-1 ps-3">Producto</div>
                            <div class="d-flex align-items-center justify-content-end gap-2 gap-md-3 gap-xl-4 w-md-auto">
                                <div class="cart-col-price text-end">Precio</div>
                                <div class="cart-col-quantity text-center">Cantidad</div>
                                <div class="subtotal-container text-end">Total</div>
                                <div class="cart-col-action"></div>
                            </div>
                        </div>

                        @foreach ($carrito->detalles as $index => $detalle)
                            @php
                                $img = $detalle->producto->imagenes->sortBy('orden')->first();
                                $imgUrl = $img ? $img->url : asset('img/placeholder-petthreads.jpg');
                            @endphp
                            <div class="cart-item-row d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3">
                                <!-- Imagen e Info del Producto -->
                                <div class="d-flex align-items-center gap-3 flex-grow-1">
                                    <div class="cart-item-img-container">
                                        <img src="{{ $imgUrl }}" alt="{{ $detalle->producto->nombre }}" class="cart-item-img">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="cart-item-title mb-3">{{ $detalle->producto->nombre_base }}</h3>
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
                                <div class="d-flex align-items-center justify-content-between justify-content-md-end flex-md-grow-0 gap-2 gap-md-3 gap-xl-4 w-100 w-md-auto">
                                    <!-- Precio Unitario -->
                                    <div class="text-md-end cart-col-price">
                                        <span class="text-muted d-block d-md-none small">Precio</span>
                                        <span class="price-tag">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</span>
                                    </div>

                                    <!-- Selector de Cantidad -->
                                    <div class="cart-col-quantity">
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
                                    <div class="text-md-end subtotal-container">
                                        <span class="text-muted d-block d-md-none small">Subtotal</span>
                                        <span class="subtotal-tag">${{ number_format($detalle->subtotal, 0, ',', '.') }}</span>
                                    </div>

                                    <!-- Eliminar Ítem -->
                                    <div class="cart-col-action text-center">
                                        <form action="{{ route('carrito.eliminar', $detalle->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-remove-item" title="Eliminar del carrito">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M4 7H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M6 7H12H18V18C18 19.6569 16.6569 21 15 21H9C7.34315 21 6 19.6569 6 18V7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7H9V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                <div class="col-lg-3 col-xl-4">
                    <div class="cart-summary-card" >
                        <h5 class="summary-heading">Resumen del pedido</h2>

                        <div class="summary-row">
                            <span class="text-secondary">Subtotal</span>
                            <span class="fw-semibold text-dark">${{ number_format($carrito->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-secondary">Envío</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>

                        <div class="summary-row total-row">
                            <span>Total</span>
                            <span>${{ number_format($carrito->total, 0, ',', '.') }}</span>
                        </div>

                        <!-- Formulario de Checkout / Confirmar Compra -->
                        @if ($tieneDatosCompletos)
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
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm mt-4 p-3" role="alert" style="background-color: #fffbeb; color: #b45309; border-radius: 12px;">
                                <div class="d-flex gap-2 align-items-start text-start">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0 mt-0.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <strong class="d-block mb-1 small fw-bold">Información de envío incompleta</strong>
                                        <span class="small d-block mb-2">Para poder realizar la compra, necesitamos tus datos de contacto y dirección de entrega:</span>
                                        <ul class="mb-0 ps-3 small text-start">
                                            @if (empty($user->telefono)) <li>Teléfono de contacto</li> @endif
                                            @if (empty($user->direccion)) <li>Dirección de entrega</li> @endif
                                            @if (empty($user->localidad)) <li>Localidad</li> @endif
                                            @if (empty($user->provincia)) <li>Provincia</li> @endif
                                            @if (empty($user->codigo_postal)) <li>Código Postal</li> @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('usuario.cuenta') }}" class="btn btn-warning w-100 py-3 mt-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2" style="background-color: #f59e0b; border-color: #d97706; color: #fff;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/>
                                </svg>
                                <span>Completar datos de envío</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
