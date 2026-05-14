@extends('layouts.app')

@section('title', 'Rutinas · POWER STACK')

@php
    $workoutDates = $workoutHistory->map(fn($log) => $log->completed_at->format('Y-m-d'))
        ->unique()->values()->toArray();
@endphp

@section('content')
<script>var _wpDates = @json($workoutDates);</script>
<div class="space-y-6" x-data="historialCalendar(_wpDates)">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold tracking-widest mb-1" style="color:#A1CD35;">ENTRENAMIENTO</p>
            <h1 class="text-2xl font-black" style="color:#121212;">Rutinas</h1>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto sm:overflow-visible pb-2 sm:pb-0">
            @if($routines->isNotEmpty())
            <a href="{{ route('routines.pdf') }}" target="_blank"
               class="flex items-center gap-2 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border-2 font-bold text-sm transition-all hover:shadow-sm shrink-0"
               style="border-color:#e5e7eb; color:#616161;"
               title="Descargar PDF">
                <i class="fa-solid fa-file-pdf text-base" style="color:#ef4444;"></i>
                <span class="hidden md:inline">PDF</span>
            </a>
            @endif
            <a href="{{ route('routines.import') }}"
               class="flex items-center gap-2 px-3 py-2.5 sm:px-4 sm:py-3 rounded-xl border-2 font-bold text-sm transition-all hover:shadow-sm shrink-0"
               style="border-color:#e5e7eb; color:#616161;"
               title="Importar desde foto">
                <i class="fa-solid fa-camera text-base" style="color:#2D9CDB;"></i>
                <span class="hidden md:inline">Desde foto</span>
            </a>
            <a href="{{ route('routines.create') }}" class="btn-primary flex items-center gap-2 shrink-0 px-3 py-2.5 sm:px-5 sm:py-3 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva rutina</span>
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 p-1 bg-gray-100 rounded-xl w-fit">
        <button @click="tab = 'planes'"
                class="px-5 py-2 rounded-lg text-sm font-bold transition-all"
                :class="tab === 'planes' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'"
                :style="tab === 'planes' ? 'color:#121212;' : 'color:#616161;'">
            <i class="fa-solid fa-dumbbell mr-1.5"></i>
            Mis Planes
        </button>
        <button @click="tab = 'historial'"
                class="px-5 py-2 rounded-lg text-sm font-bold transition-all"
                :class="tab === 'historial' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'"
                :style="tab === 'historial' ? 'color:#121212;' : 'color:#616161;'">
            <i class="fa-solid fa-clock-rotate-left mr-1.5"></i>
            Historial
        </button>
    </div>

    {{-- ===== TAB: MIS PLANES ===== --}}
    <div x-show="tab === 'planes'" x-cloak>
        @if($routines->isEmpty())
        <div class="card p-12 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-5"
                 style="background:rgba(45,156,219,0.1);">
                <i class="fa-solid fa-dumbbell text-4xl" style="color:rgba(45,156,219,0.4);"></i>
            </div>
            <p class="text-xl font-black" style="color:#121212;">No hay rutinas creadas</p>
            <p class="text-sm mt-2 mb-6" style="color:#616161;">Empieza creando tu primer plan de entrenamiento</p>
            <a href="{{ route('routines.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus mr-2"></i>Crear rutina
            </a>
        </div>
        @else
        <div class="space-y-4">
            @foreach($routines as $routine)
            @php $doneToday = $todayLogs->contains($routine->id); @endphp
            <div class="card overflow-hidden" x-data="{ open: false }">
                {{-- Cabecera --}}
                <div class="p-5 flex items-center gap-4 cursor-pointer select-none" @click="open = !open">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="{{ $doneToday ? 'background:#A1CD35;' : 'background:#f3f4f6;' }}">
                        @if($doneToday)
                            <i class="fa-solid fa-check text-white text-sm"></i>
                        @else
                            <i class="fa-solid fa-bolt text-gray-400 text-sm"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-black text-lg truncate" style="color:#121212;">{{ $routine->name }}</h3>
                            @if($doneToday)
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg"
                                  style="background:rgba(161,205,53,0.15); color:#A1CD35;">¡HECHA HOY!</span>
                            @endif
                        </div>
                        <p class="text-xs mt-0.5" style="color:#616161;">
                            {{ $routine->exercises->count() }} ejercicio{{ $routine->exercises->count() !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('routines.edit', $routine->id) }}"
                           @click.stop
                           class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
                           style="color:#616161;">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>
                        <form action="{{ route('routines.destroy', $routine->id) }}" method="POST"
                              @click.stop onsubmit="return confirm('¿Eliminar «{{ $routine->name }}»?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-50 text-red-400 transition-colors">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                        <i class="fa-solid fa-chevron-down text-gray-300 text-sm transition-transform duration-200"
                           :class="open ? 'rotate-180' : ''"></i>
                    </div>
                </div>

                {{-- Ejercicios expandibles — sin x-collapse, solo x-show --}}
                <div x-show="open" class="border-t border-gray-100">
                    @foreach($routine->exercises as $exercise)
                    <div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background:rgba(45,156,219,0.1);">
                            <i class="fa-solid fa-circle-dot text-xs" style="color:#2D9CDB;"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold" style="color:#121212;">{{ $exercise->name }}</p>
                            <p class="text-xs" style="color:#616161;">{{ $exercise->muscle_group }}</p>
                        </div>
                        <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded-lg" style="color:#616161;">
                            {{ $exercise->pivot->sets }} × {{ $exercise->pivot->reps }}
                        </span>
                    </div>
                    @endforeach

                    <div class="p-4 pt-3">
                        @if($doneToday)
                        <div class="w-full py-3 rounded-xl text-center text-sm font-bold text-gray-400 bg-gray-100">
                            <i class="fa-solid fa-check mr-1"></i>Ya entrenado hoy
                        </div>
                        @else
                        <a href="{{ route('routines.train', $routine->id) }}"
                           class="btn-primary w-full flex items-center justify-center gap-2 text-center">
                            <i class="fa-solid fa-play"></i>
                            ¡A ENTRENAR!
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ===== TAB: HISTORIAL ===== --}}
    <div x-show="tab === 'historial'" x-cloak>

        {{-- Calendario --}}
        <div class="card p-5 mb-6">
            {{-- Navegación mes --}}
            <div class="flex items-center justify-between mb-5">
                <button @click="prevMonth()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
                        style="color:#616161;">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <div class="text-center">
                    <p class="font-black text-base" style="color:#121212;"
                       x-text="monthNames[calMonth] + ' ' + calYear"></p>
                    <p class="text-xs mt-0.5" style="color:#616161;"
                       x-text="workoutDates.filter(d => d.startsWith(calYear + '-' + String(calMonth+1).padStart(2,'0'))).length + ' entrenamientos este mes'"></p>
                </div>
                <button @click="nextMonth()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
                        style="color:#616161;">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>

            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 mb-1">
                <template x-for="d in dayNames" :key="d">
                    <div class="text-center text-xs font-bold py-1" style="color:#616161;" x-text="d"></div>
                </template>
            </div>

            {{-- Grilla de días --}}
            <div class="grid grid-cols-7 gap-1">
                <template x-for="(day, i) in calDays" :key="i">
                    <div>
                        <template x-if="day === null">
                            <div class="h-9"></div>
                        </template>
                        <template x-if="day !== null">
                            <button @click="toggleDay(day.dateStr)"
                                    class="w-full h-9 rounded-xl flex flex-col items-center justify-center relative transition-all text-sm font-semibold"
                                    :style="calDayStyle(day)">
                                <span x-text="day.day"></span>
                                <span x-show="day.hasWorkout && selectedDate !== day.dateStr"
                                      class="absolute bottom-1 w-1.5 h-1.5 rounded-full"
                                      style="background:#A1CD35;"></span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Leyenda --}}
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-5 text-xs" style="color:#616161;">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full" style="background:#A1CD35;"></span>
                        Con entrenamiento
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-gray-900"></span>
                        Hoy
                    </span>
                </div>
                <button x-show="selectedDate"
                        x-cloak
                        @click="selectedDate = null"
                        class="text-xs font-semibold px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors"
                        style="color:#A1CD35;">
                    Ver todo ×
                </button>
            </div>
        </div>

        {{-- Logs --}}
        @if($workoutHistory->isEmpty())
        <div class="card p-12 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-5"
                 style="background:rgba(161,205,53,0.1);">
                <i class="fa-solid fa-calendar-check text-4xl" style="color:rgba(161,205,53,0.4);"></i>
            </div>
            <p class="text-xl font-black" style="color:#121212;">Historial vacío</p>
            <p class="text-sm mt-2" style="color:#616161;">Tus entrenamientos completados aparecerán aquí</p>
        </div>
        @else
        @php
            $grouped = $workoutHistory->groupBy(fn($log) => $log->completed_at->format('Y-m-d'));
        @endphp

        <div x-show="selectedDate && !workoutDates.includes(selectedDate)"
             x-cloak
             class="card p-8 flex flex-col items-center text-center mb-4">
            <i class="fa-regular fa-calendar-xmark text-4xl mb-3" style="color:rgba(161,205,53,0.3);"></i>
            <p class="font-bold" style="color:#121212;">Sin entrenamiento este día</p>
            <p class="text-sm mt-1" style="color:#616161;">No registraste ninguna sesión aquí</p>
        </div>

        <div class="space-y-4">
            @foreach($grouped as $date => $logs)
            @php
                $dateObj     = \Carbon\Carbon::parse($date);
                $isToday     = $dateObj->isToday();
                $isYesterday = $dateObj->isYesterday();
                $label       = $isToday ? 'Hoy' : ($isYesterday ? 'Ayer' : ucfirst($dateObj->translatedFormat('l, d \d\e F')));
            @endphp
            <div x-show="!selectedDate || selectedDate === '{{ $date }}'">
                <div class="flex items-center gap-3 mb-3">
                    <p class="text-xs font-bold tracking-wider" style="color:#616161;">
                        {{ strtoupper($label) }}
                    </p>
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-xs" style="color:#616161;">
                        {{ $logs->count() }} {{ $logs->count() === 1 ? 'sesión' : 'sesiones' }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($logs as $log)
                    <div class="card px-5 py-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(161,205,53,0.12);">
                            <i class="fa-solid fa-dumbbell text-sm" style="color:#A1CD35;"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold" style="color:#121212;">
                                {{ $log->routine?->name ?? 'Rutina eliminada' }}
                            </p>
                            <p class="text-xs mt-0.5" style="color:#616161;">
                                <i class="fa-regular fa-clock mr-1"></i>
                                {{ $log->completed_at->format('H:i') }} · {{ $log->completed_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                             style="background:rgba(161,205,53,0.12);">
                            <i class="fa-solid fa-check text-xs" style="color:#A1CD35;"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function historialCalendar(workoutDates) {
    const today = new Date();
    return {
        tab:          'planes',
        workoutDates,
        calYear:      today.getFullYear(),
        calMonth:     today.getMonth(),
        selectedDate: null,

        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                     'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        dayNames:   ['L','M','M','J','V','S','D'],

        get calDays() {
            const year  = this.calYear;
            const month = this.calMonth;
            let firstDay = new Date(year, month, 1).getDay();
            const padStart   = (firstDay === 0 ? 6 : firstDay - 1);
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const days = Array(padStart).fill(null);
            for (let d = 1; d <= daysInMonth; d++) {
                const mm      = String(month + 1).padStart(2, '0');
                const dd      = String(d).padStart(2, '0');
                const dateStr = year + '-' + mm + '-' + dd;
                const isToday = new Date(year, month, d).toDateString() === today.toDateString();
                days.push({ day: d, dateStr, hasWorkout: this.workoutDates.includes(dateStr), isToday });
            }
            return days;
        },

        calDayStyle(day) {
            if (this.selectedDate === day.dateStr)
                return 'background:#A1CD35; color:#121212; font-weight:900;';
            if (day.isToday)
                return 'background:#121212; color:#fff; font-weight:900;';
            if (day.hasWorkout)
                return 'background:rgba(161,205,53,0.15); color:#121212;';
            return 'color:#616161;';
        },

        prevMonth() {
            if (this.calMonth === 0) { this.calYear--; this.calMonth = 11; }
            else this.calMonth--;
            this.selectedDate = null;
        },
        nextMonth() {
            if (this.calMonth === 11) { this.calYear++; this.calMonth = 0; }
            else this.calMonth++;
            this.selectedDate = null;
        },
        toggleDay(dateStr) {
            this.selectedDate = (this.selectedDate === dateStr) ? null : dateStr;
        },
    };
}
</script>
@endpush
@endsection
