@extends('layouts.app')

@section('title', 'Mis Metas · POWER STACK')

@section('content')
<div class="space-y-8" x-data="{ showModal: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold tracking-widest mb-1" style="color:#A1CD35;">OBJETIVOS</p>
            <h1 class="text-2xl font-black text-[#121212]">Mis Metas</h1>
        </div>
        <button @click="showModal = true" class="btn-primary flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Nueva meta
        </button>
    </div>

    {{-- Lista de metas --}}
    @if($goals->isEmpty())
    <div class="card p-12 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-5"
             style="background:rgba(242,153,74,0.1);">
            <i class="fa-solid fa-trophy text-4xl" style="color:rgba(242,153,74,0.4);"></i>
        </div>
        <p class="text-xl font-black text-[#121212]">Sin metas aún</p>
        <p class="text-sm text-[#616161] mt-2 mb-6 max-w-xs">
            Define objetivos para mantenerte motivado y medir tu progreso
        </p>
        <button @click="showModal = true" class="btn-primary">
            <i class="fa-solid fa-plus mr-2"></i>Crear primera meta
        </button>
    </div>
    @else
    <div class="grid gap-5 sm:grid-cols-2">
        @foreach($goals as $goal)
        @php
            $isWeight     = $goal->type === 'weight';
            $label        = $isWeight ? 'Peso Objetivo' : 'Entrenamientos Semanales';
            $targetLabel  = $isWeight ? $goal->target_value . ' kg' : $goal->target_value . ' sesiones';
            $currentLabel = $isWeight ? ($goal->current_value . ' kg') : ($goal->current_value . ' / ' . $goal->target_value);
            $iconClass    = $isWeight ? 'fa-scale-balanced' : 'fa-fire';
            $accentColor  = $isWeight ? '#2D9CDB' : '#F2994A';
            $bgColor      = $isWeight ? 'rgba(45,156,219,0.1)' : 'rgba(242,153,74,0.1)';

            // Progress calculation
            if ($isWeight && $goal->current_value && $goal->target_value) {
                // For weight goal: completed if current <= target (losing) or current >= target (gaining)
                $progress = min(1, $goal->current_value > 0 ? ($goal->target_value / $goal->current_value) : 0);
                $progress = max(0, min(1, $progress));
            } else {
                $progress = $goal->target_value > 0 ? min(1, $goal->current_value / $goal->target_value) : 0;
            }
            $progressPct = round($progress * 100);
        @endphp

        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background:{{ $bgColor }};">
                        <i class="fa-solid {{ $iconClass }}" style="color:{{ $accentColor }};"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold tracking-wider" style="color:{{ $accentColor }};">
                            {{ strtoupper($label) }}
                        </p>
                        <p class="text-2xl font-black text-[#121212]">{{ $targetLabel }}</p>
                    </div>
                </div>
                <form action="{{ route('goals.destroy', $goal->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta meta?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-red-50 text-red-400 transition-colors">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </form>
            </div>

            {{-- Progress bar --}}
            <div class="space-y-2">
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="width:{{ $progressPct }}%; background:{{ $accentColor }};"></div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-[#616161]">Actual: {{ $currentLabel }}</p>
                    <p class="text-xs font-bold" style="color:{{ $accentColor }};">{{ $progressPct }}%</p>
                </div>
            </div>

            @if($goal->deadline)
            <div class="mt-3 flex items-center gap-1.5 text-xs text-[#616161]">
                <i class="fa-regular fa-calendar"></i>
                Meta para: {{ \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') }}
            </div>
            @endif

            @if($goal->is_completed)
            <div class="mt-3 flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-lg w-fit"
                 style="background:rgba(161,205,53,0.15); color:#A1CD35;">
                <i class="fa-solid fa-check-circle"></i> ¡Meta alcanzada!
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- Modal: Nueva meta --}}
<div x-show="showModal"
     x-cloak
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     @click.self="showModal = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-[#121212]">NUEVA META</h3>
            <button @click="showModal = false"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-[#616161]">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('goals.store') }}" method="POST" class="space-y-4"
              x-data="{ type: 'weight' }">
            @csrf

            <div>
                <label class="form-label">TIPO DE META</label>
                <select name="type" x-model="type" class="form-input">
                    <option value="weight">Peso objetivo (kg)</option>
                    <option value="workouts_weekly">Sesiones semanales</option>
                </select>
            </div>

            <div>
                <label class="form-label" x-text="type === 'weight' ? 'PESO OBJETIVO (kg)' : 'SESIONES POR SEMANA'"></label>
                <input type="number" name="target_value" step="0.1" min="1" required
                       class="form-input"
                       :placeholder="type === 'weight' ? 'Ej. 70' : 'Ej. 4'">
            </div>

            <div>
                <label class="form-label">FECHA LÍMITE (opcional)</label>
                <input type="date" name="deadline" class="form-input"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" @click="showModal = false" class="btn-ghost flex-1">CANCELAR</button>
                <button type="submit" class="btn-primary flex-1">GUARDAR</button>
            </div>
        </form>
    </div>

</div>
@endsection
