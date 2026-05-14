@extends('layouts.app')

@section('title', 'Dashboard · POWER STACK')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <p class="text-xs font-bold tracking-widest mb-1" style="color:#A1CD35;">HOLA, ATLETA</p>
        <h1 class="text-3xl font-black text-[#121212]">
            Bienvenido de nuevo,<br>
            <span style="color:#A1CD35;">{{ Auth::user()->name }}</span>
        </h1>
    </div>

    {{-- Tendencia de peso corporal --}}
    @if($weightTrend)
    <div class="rounded-2xl p-4 flex items-center gap-4"
         style="background:{{ $weightTrend['direction'] === 'down' ? 'rgba(161,205,53,0.1)' : 'rgba(242,153,74,0.1)' }};">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:{{ $weightTrend['direction'] === 'down' ? 'rgba(161,205,53,0.2)' : 'rgba(242,153,74,0.2)' }};">
            <i class="fa-solid {{ $weightTrend['direction'] === 'down' ? 'fa-arrow-trend-down' : 'fa-arrow-trend-up' }} text-lg"
               style="color:{{ $weightTrend['direction'] === 'down' ? '#A1CD35' : '#F2994A' }};"></i>
        </div>
        <div class="flex-1">
            @if($weightTrend['direction'] === 'down')
                <p class="font-black text-[#121212]">¡Bajaste {{ $weightTrend['diff'] }} kg! 🎉</p>
                <p class="text-sm text-[#616161]">Peso actual: <strong>{{ $weightTrend['current'] }} kg</strong> — ¡Vas muy bien, sigue así!</p>
            @else
                <p class="font-black text-[#121212]">Subiste {{ $weightTrend['diff'] }} kg</p>
                <p class="text-sm text-[#616161]">Peso actual: <strong>{{ $weightTrend['current'] }} kg</strong> — Revisa tu alimentación e hidratación.</p>
            @endif
        </div>
        <a href="{{ route('weight.index') }}" class="text-xs font-bold flex-shrink-0"
           style="color:{{ $weightTrend['direction'] === 'down' ? '#A1CD35' : '#F2994A' }};">
            Ver historial
        </a>
    </div>
    @endif

    {{-- BMI Card --}}
    <div class="rounded-2xl p-6" style="background:rgba(161,205,53,0.1);">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <p class="text-xs font-bold tracking-widest mb-2" style="color:#A1CD35;">TU ESTADO FÍSICO</p>
                <h2 class="text-2xl font-black text-[#121212]">{{ strtoupper($bmiCategory) }}</h2>
                <p class="text-[#616161] text-sm mt-1">
                    IMC: <span class="font-bold text-[#121212]">{{ $bmi ?? '--' }}</span>
                    @if($profile?->current_weight)
                        &nbsp;·&nbsp; Peso actual:
                        <span class="font-bold text-[#121212]">{{ $profile->current_weight }} kg</span>
                    @endif
                </p>
                @if($weightToLose > 0)
                    <p class="text-sm text-[#616161] mt-1">
                        Para llegar a peso ideal: bajar
                        <span class="font-bold" style="color:#A1CD35;">{{ $weightToLose }} kg</span>
                    </p>
                @elseif($bmi && $bmiCategory === 'Normal')
                    <p class="text-sm font-semibold mt-1" style="color:#A1CD35;">¡Peso ideal! Sigue así.</p>
                @endif
            </div>
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(161,205,53,0.2);">
                <i class="fa-solid fa-gauge-high text-2xl" style="color:#A1CD35;"></i>
            </div>
        </div>

        @if(!$profile || !$profile->height || !$profile->current_weight)
        <div class="mt-4 bg-white rounded-xl p-3 flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation" style="color:#F2994A;"></i>
            <p class="text-sm text-[#616161] flex-1">Completa tu perfil para calcular tu IMC.</p>
            <button onclick="document.getElementById('modal-profile').showModal()"
                    class="text-xs font-bold px-3 py-1.5 rounded-lg"
                    style="background:#A1CD35; color:#121212;">
                Completar
            </button>
        </div>
        @endif
    </div>

    {{-- Módulos --}}
    <div>
        <p class="text-xs font-bold text-[#616161] tracking-widest mb-4">MÓDULOS PRINCIPALES</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <a href="{{ route('weight.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 transition-colors"
                     style="background:rgba(161,205,53,0.12);">
                    <i class="fa-solid fa-scale-balanced text-2xl" style="color:#A1CD35;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-sm">PESO</p>
                <p class="text-xs text-[#616161] mt-1">
                    {{ $profile?->current_weight ? $profile->current_weight . ' kg actual' : 'Sin registros' }}
                </p>
                <p class="text-xs mt-3 font-semibold group-hover:underline" style="color:#A1CD35;">Ver control →</p>
            </a>

            <a href="{{ route('routines.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                     style="background:rgba(45,156,219,0.12);">
                    <i class="fa-solid fa-dumbbell text-2xl" style="color:#2D9CDB;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-sm">RUTINAS</p>
                <p class="text-xs text-[#616161] mt-1">Planes de entrenamiento</p>
                <p class="text-xs mt-3 font-semibold group-hover:underline" style="color:#2D9CDB;">Ver rutinas →</p>
            </a>

            <a href="{{ route('goals.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                     style="background:rgba(242,153,74,0.12);">
                    <i class="fa-solid fa-trophy text-2xl" style="color:#F2994A;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-sm">METAS</p>
                <p class="text-xs text-[#616161] mt-1">Objetivos fitness</p>
                <p class="text-xs mt-3 font-semibold group-hover:underline" style="color:#F2994A;">Ver metas →</p>
            </a>
        </div>
    </div>

    {{-- Resumen semanal --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold text-[#616161] tracking-widest">RESUMEN SEMANAL</p>
            <a href="{{ route('routines.index') }}" class="text-xs font-semibold" style="color:#A1CD35;">
                Ver historial →
            </a>
        </div>
        <div class="card p-6">
            @php
                $dayNames = ['L','M','M','J','V','S','D'];
                $completedCount = collect($weeklyProgress)->filter()->count();
            @endphp

            <div class="grid grid-cols-7 gap-2 mb-4">
                @foreach($dayNames as $i => $day)
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xs text-[#616161] font-medium">{{ $day }}</span>
                    @if($weeklyProgress[$i + 1] ?? false)
                        <div class="w-9 h-9 rounded-full flex items-center justify-center"
                             style="background:#A1CD35;">
                            <i class="fa-solid fa-check text-white text-xs"></i>
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-full border-2 border-gray-200 flex items-center justify-center">
                            <span class="text-gray-300 text-xs font-bold">{{ $i + 1 }}</span>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                <p class="text-sm text-[#616161]">
                    Esta semana: <span class="font-bold text-[#121212]">{{ $completedCount }}</span>
                    {{ $completedCount === 1 ? 'entrenamiento' : 'entrenamientos' }}
                </p>
                @if($completedCount >= 5)
                    <span class="text-xs font-bold px-2 py-1 rounded-lg" style="background:rgba(161,205,53,0.15); color:#A1CD35;">
                        ¡Semana excelente!
                    </span>
                @elseif($completedCount >= 3)
                    <span class="text-xs font-bold px-2 py-1 rounded-lg" style="background:rgba(45,156,219,0.12); color:#2D9CDB;">
                        Buen ritmo
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Acceso rápido - botón perfil --}}
    <div class="flex justify-end">
        <button onclick="document.getElementById('modal-profile').showModal()"
                class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-[#616161] hover:bg-white hover:shadow-sm transition-all">
            <i class="fa-solid fa-user-pen"></i>
            Actualizar perfil
        </button>
    </div>
</div>

{{-- Modal Perfil --}}
<dialog id="modal-profile" class="rounded-2xl shadow-2xl p-0 w-full max-w-md backdrop:bg-black/50">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-[#121212] tracking-wide">DATOS BÁSICOS</h3>
            <button onclick="document.getElementById('modal-profile').close()"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-[#616161]">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">TALLA (cm)</label>
                <input type="number" name="height" step="0.1" value="{{ $profile?->height }}"
                       class="form-input" placeholder="Ej. 175">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">PESO ACTUAL (kg)</label>
                <input type="number" name="current_weight" step="0.1" value="{{ $profile?->current_weight }}"
                       class="form-input" placeholder="Ej. 75">
            </div>
            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">PESO META (kg)</label>
                <input type="number" name="goal_weight" step="0.1" value="{{ $profile?->goal_weight }}"
                       class="form-input" placeholder="Ej. 70">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-profile').close()"
                        class="btn-ghost flex-1">CANCELAR</button>
                <button type="submit" class="btn-primary flex-1">GUARDAR</button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
    // Abrir modal si perfil incompleto al cargar
    @if(!$profile || !$profile->height || !$profile->current_weight)
    // No auto-open, the user can click the button
    @endif
</script>
@endpush
@endsection
