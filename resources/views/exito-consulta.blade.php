@extends('layout')
@section('contenido')
<section class="container my-5 py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="p-5 rounded shadow-sm anim-fade-down" style="background-color: var(--green-100); border: 1px solid var(--border-color);">
                <img src="{{ asset('img/icons/sparkles.svg') }}" alt="Éxito" class="mb-4" style="width: 80px; height: 80px;">
                <h1 class="mb-3 fw-bold" style="color: var(--text-main-title);">¡Recibimos tu consulta!</h1>
                <p class="mb-4" style="color: var(--text-secondary);">
                    Nuestro equipo revisará tu mensaje y se pondrá en contacto contigo a la brevedad. ¡Gracias por comunicarte con Petthreads!
                </p>
                <a href="{{ url('/productos') }}" class="btn px-4 py-2 fw-bold btn-nature">
                    Seguir explorando productos
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
