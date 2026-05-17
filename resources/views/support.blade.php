@extends('layouts.app')

@section('title', 'Soporte — POWER STACK')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="text-center py-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
             style="background-color:rgba(161,205,53,0.12);">
            <i class="fa-solid fa-headset text-2xl" style="color:#A1CD35;"></i>
        </div>
        <h1 class="text-2xl font-black text-[#121212] tracking-wide">Centro de Soporte</h1>
        <p class="text-[#616161] mt-1 text-sm">Encuentra respuestas o contáctanos directamente.</p>
    </div>

    {{-- Contacto rápido --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(161,205,53,0.12);">
                <i class="fa-solid fa-envelope" style="color:#A1CD35;"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#616161] tracking-widest uppercase">Correo</p>
                <a href="mailto:soporte@powerstack.app"
                   class="text-sm font-bold text-[#121212] hover:underline">
                    soporte@powerstack.app
                </a>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(45,156,219,0.12);">
                <i class="fa-solid fa-clock" style="color:#2D9CDB;"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#616161] tracking-widest uppercase">Respuesta</p>
                <p class="text-sm font-bold text-[#121212]">Menos de 24 h hábiles</p>
            </div>
        </div>
    </div>

    {{-- Formulario de contacto --}}
    <div class="card p-6">
        <h2 class="text-base font-black text-[#121212] tracking-wide mb-5 flex items-center gap-2">
            <i class="fa-solid fa-paper-plane" style="color:#A1CD35;"></i>
            Envíanos un mensaje
        </h2>

        @if(session('support_sent'))
        <div class="rounded-xl p-4 mb-5 text-sm font-semibold flex items-center gap-2"
             style="background:rgba(161,205,53,0.1); color:#5a7a0a;">
            <i class="fa-solid fa-circle-check"></i>
            Tu mensaje fue enviado. Te responderemos pronto.
        </div>
        @endif

        <form action="{{ route('support.send') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name"
                           value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}"
                           class="form-input" placeholder="Tu nombre" required>
                </div>
                <div>
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email"
                           value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}"
                           class="form-input" placeholder="tu@correo.com" required>
                </div>
            </div>
            <div>
                <label class="form-label">Asunto</label>
                <select name="subject" class="form-input">
                    <option value="bug">Reportar un error</option>
                    <option value="account">Problema con mi cuenta</option>
                    <option value="feature">Sugerencia de mejora</option>
                    <option value="data">Solicitud de datos / eliminación</option>
                    <option value="other">Otro</option>
                </select>
            </div>
            <div>
                <label class="form-label">Mensaje</label>
                <textarea name="message" rows="5"
                          class="form-input resize-none"
                          placeholder="Describe tu problema o pregunta con el mayor detalle posible..."
                          required>{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="btn-primary w-full sm:w-auto">
                <i class="fa-solid fa-paper-plane mr-2"></i>Enviar mensaje
            </button>
        </form>
    </div>

    {{-- FAQ --}}
    <div class="card p-6">
        <h2 class="text-base font-black text-[#121212] tracking-wide mb-5 flex items-center gap-2">
            <i class="fa-solid fa-circle-question" style="color:#A1CD35;"></i>
            Preguntas frecuentes
        </h2>

        <div class="space-y-2" x-data="{ open: null }">
            @foreach([
                ['¿Cómo creo una rutina?',
                 'Ve a <strong>Rutinas</strong> en el menú lateral y haz clic en <em>Nueva Rutina</em>. Elige los ejercicios, define series y repeticiones, y guarda.'],
                ['¿Cómo registro mi peso corporal?',
                 'En <strong>Control de Peso</strong> encontrarás el formulario para agregar un nuevo registro. Se almacena con fecha y hora automáticamente.'],
                ['¿Cómo funciona el registro de agua?',
                 'Desde el <strong>Dashboard</strong> puedes registrar vasos de agua directamente. La meta diaria es 2,000 ml y el progreso se muestra en tiempo real.'],
                ['¿Qué son los logros?',
                 'Los logros se otorgan automáticamente al cumplir metas: primera rutina completada, rachas de días consecutivos, meta de hidratación diaria, entre otros.'],
                ['¿Puedo exportar mis rutinas?',
                 'Sí. Desde la sección <strong>Rutinas</strong> tienes la opción de exportar a PDF o importar desde un archivo JSON.'],
                ['¿Cómo elimino mi cuenta?',
                 'Envíanos un correo a <strong>soporte@powerstack.app</strong> con el asunto "Eliminar cuenta". Procesaremos la solicitud en máximo 5 días hábiles.'],
            ] as $i => [$q, $a])
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <button type="button"
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 transition-colors">
                    <span class="font-semibold text-sm text-[#121212]">{{ $q }}</span>
                    <i class="fa-solid text-gray-400 text-xs transition-transform duration-200"
                       :class="open === {{ $i }} ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="open === {{ $i }}"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-4 pb-4 text-sm text-[#616161] leading-relaxed border-t border-gray-50">
                    <p class="pt-3">{!! $a !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Footer legal --}}
    <p class="text-center text-xs text-gray-400 pb-4">
        Power Stack v1.0.0 &mdash;
        <a href="{{ route('privacy') }}" class="hover:underline">Política de Privacidad</a>
    </p>

</div>
@endsection
