@extends('layouts.app')

@section('title', isset($routine) ? 'Editar rutina · POWER STACK' : 'Nueva rutina · POWER STACK')

@section('content')
<div class="max-w-2xl mx-auto space-y-6"
     x-data="routineBuilder()"
     x-init="init()">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('routines.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm transition-all text-[#616161]">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <p class="text-xs font-bold tracking-widest" style="color:#A1CD35;">
                {{ isset($routine) ? 'EDITAR' : 'CREAR' }}
            </p>
            <h1 class="text-2xl font-black text-[#121212]">
                {{ isset($routine) ? 'Editar rutina' : 'Nueva rutina' }}
            </h1>
        </div>
    </div>

    <form action="{{ isset($routine) ? route('routines.update', $routine->id) : route('routines.store') }}"
          method="POST"
          @submit="prepareSubmit">
        @csrf
        @if(isset($routine)) @method('PUT') @endif

        {{-- Nombre --}}
        <div class="card p-5 space-y-3 mb-5">
            <h2 class="font-black text-[#121212] text-sm tracking-wider">NOMBRE DE LA RUTINA</h2>
            <input type="text" name="name"
                   class="form-input text-lg font-bold"
                   placeholder="Ej. Día de empuje, Full Body, Piernas..."
                   value="{{ old('name', $routine?->name) }}"
                   required>
            @error('name')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ejercicios --}}
        <div class="card p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-[#121212] text-sm tracking-wider">EJERCICIOS</h2>
                <span class="text-xs text-[#616161]"
                      x-text="selected.length + ' ejercicio' + (selected.length !== 1 ? 's' : '')"></span>
            </div>

            @error('exercises')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

            {{-- Filas de ejercicios --}}
            <div class="space-y-3" x-show="selected.length > 0">
                <template x-for="(item, index) in selected" :key="index">
                    <div class="flex gap-2 items-start p-3 rounded-xl border border-gray-100 bg-gray-50">
                        {{-- Número --}}
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-bold"
                             style="background:#A1CD35; color:#121212;"
                             x-text="index + 1"></div>

                        {{-- Select ejercicio --}}
                        <div class="flex-1 min-w-0">
                            <select @change="item.exercise_id = $event.target.value"
                                    class="form-input text-sm py-2 px-3">
                                <option value="">-- Selecciona ejercicio --</option>
                                <template x-for="ex in allExercises" :key="ex.id">
                                    <option :value="String(ex.id)"
                                            :selected="String(ex.id) === String(item.exercise_id)"
                                            x-text="ex.muscle_group + ': ' + ex.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Sets --}}
                        <div class="w-16 flex-shrink-0">
                            <input type="number" x-model="item.sets" min="1" max="99"
                                   class="form-input text-sm py-2 px-2 text-center"
                                   placeholder="Sets">
                            <p class="text-[10px] text-center text-[#616161] mt-0.5">SERIES</p>
                        </div>

                        {{-- Reps --}}
                        <div class="w-16 flex-shrink-0">
                            <input type="number" x-model="item.reps" min="1" max="999"
                                   class="form-input text-sm py-2 px-2 text-center"
                                   placeholder="Reps">
                            <p class="text-[10px] text-center text-[#616161] mt-0.5">REPS</p>
                        </div>

                        {{-- Eliminar --}}
                        <button type="button" @click="remove(index)"
                                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-50 text-red-400 transition-colors flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Estado vacío --}}
            <div x-show="selected.length === 0"
                 class="py-8 flex flex-col items-center text-center">
                <i class="fa-solid fa-dumbbell text-3xl mb-3" style="color:rgba(45,156,219,0.3);"></i>
                <p class="text-sm font-semibold text-[#616161]">Sin ejercicios aún</p>
                <p class="text-xs text-[#616161] mt-1">Agrega al menos un ejercicio a tu rutina</p>
            </div>

            {{-- Botón agregar --}}
            <button type="button" @click="add()"
                    class="w-full py-2.5 rounded-xl border-2 border-dashed border-gray-200 text-sm font-semibold text-[#616161]
                           hover:border-[#A1CD35] hover:text-[#A1CD35] transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Agregar ejercicio
            </button>

            {{-- Hidden inputs para submit --}}
            <div id="hidden-inputs" style="display:none;"></div>
        </div>

        {{-- Acciones --}}
        <div class="flex gap-3 mt-6">
            <a href="{{ route('routines.index') }}" class="btn-ghost flex-1 text-center">CANCELAR</a>
            <button type="submit" class="btn-primary flex-1"
                    :disabled="selected.length === 0 || selected.some(s => !s.exercise_id)">
                {{ isset($routine) ? 'ACTUALIZAR' : 'CREAR RUTINA' }}
            </button>
        </div>
    </form>
</div>

@php
    $preSelected = isset($routine)
        ? $routine->exercises->map(fn($ex) => [
            'exercise_id' => $ex->id,
            'sets'        => $ex->pivot->sets,
            'reps'        => $ex->pivot->reps,
          ])->values()
        : collect();
@endphp

@push('scripts')
<script>
    var _exercises   = @json($exercises);
    var _preSelected = @json($preSelected);
</script>
<script>
function buildGroups(exercises) {
    const groups = {};
    exercises.forEach(ex => {
        const g = ex.muscle_group || 'Otros';
        if (!groups[g]) groups[g] = { name: g, exercises: [] };
        groups[g].exercises.push(ex);
    });
    return Object.values(groups).sort((a, b) => a.name.localeCompare(b.name));
}

function routineBuilder() {
    // Los grupos se calculan ANTES de retornar el objeto,
    // así los <option> ya existen cuando x-model evalúa el select.
    const exerciseGroups = buildGroups(_exercises);

    // exercise_id como string para que coincida con el value del DOM
    const preSelected = _preSelected.map(s => ({
        exercise_id: String(s.exercise_id),
        sets: s.sets,
        reps: s.reps,
    }));

    return {
        selected:       preSelected,
        allExercises:   _exercises,
        exerciseGroups,

        init() { /* grupos ya listos */ },

        add() {
            this.selected.push({ exercise_id: '', sets: 3, reps: 12 });
        },


        remove(index) {
            this.selected.splice(index, 1);
        },

        prepareSubmit(event) {
            // Limpiar y regenerar hidden inputs
            const container = document.getElementById('hidden-inputs');
            container.innerHTML = '';

            this.selected.forEach((item, i) => {
                ['exercise_id', 'sets', 'reps'].forEach(field => {
                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = `exercises[${i}][${field}]`;
                    input.value = item[field];
                    container.appendChild(input);
                });
            });

            // Validación básica
            if (this.selected.length === 0) {
                event.preventDefault();
                alert('Agrega al menos un ejercicio.');
                return;
            }
            if (this.selected.some(s => !s.exercise_id)) {
                event.preventDefault();
                alert('Selecciona un ejercicio en cada fila.');
                return;
            }
        }
    };
}
</script>
@endpush
@endsection
