@extends('layouts.app')

@section('title', 'Progreso de Fuerza · POWER STACK')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('routines.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm transition-all text-[#616161]">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <p class="text-xs font-bold tracking-widest" style="color:#A1CD35;">ESTADÍSTICAS</p>
            <h1 class="text-2xl font-black text-[#121212]">Progreso de Fuerza</h1>
        </div>
    </div>

    @if($exerciseProgress->isEmpty())
    {{-- Estado vacío --}}
    <div class="card p-16 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-5"
             style="background:rgba(161,205,53,0.1);">
            <i class="fa-solid fa-dumbbell text-4xl" style="color:rgba(161,205,53,0.4);"></i>
        </div>
        <p class="text-xl font-black text-[#121212]">Sin datos aún</p>
        <p class="text-sm text-[#616161] mt-2 max-w-xs">
            Completa entrenamientos registrando los pesos de cada serie para ver tu progresión aquí
        </p>
        <a href="{{ route('routines.index') }}" class="btn-primary mt-6">
            <i class="fa-solid fa-dumbbell mr-2"></i>Ir a entrenar
        </a>
    </div>
    @else

    {{-- Resumen rápido --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card p-4 text-center">
            <p class="text-2xl font-black text-[#121212]">{{ $exerciseProgress->count() }}</p>
            <p class="text-xs font-bold text-[#616161] mt-1">EJERCICIOS</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-black text-[#121212]">{{ $exerciseProgress->sum('sessions') }}</p>
            <p class="text-xs font-bold text-[#616161] mt-1">SESIONES</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-black" style="color:#A1CD35;">
                {{ $exerciseProgress->where('trend', 1)->count() }}
            </p>
            <p class="text-xs font-bold text-[#616161] mt-1">MEJORAS</p>
        </div>
    </div>

    {{-- Lista de ejercicios --}}
    <div class="space-y-5">
        @foreach($exerciseProgress as $item)
        @php
            $chartId    = 'chart-' . $item['exercise']->id;
            $trendColor = match($item['trend']) { 1 => '#A1CD35', -1 => '#ef4444', default => '#9ca3af' };
            $trendIcon  = match($item['trend']) { 1 => 'fa-arrow-trend-up', -1 => 'fa-arrow-trend-down', default => 'fa-minus' };
            $trendLabel = match($item['trend']) { 1 => '+' . round($item['latest_weight'] - $item['prev_weight'], 1) . ' kg', -1 => round($item['latest_weight'] - $item['prev_weight'], 1) . ' kg', default => 'Sin cambio' };
        @endphp

        <div class="card overflow-hidden">
            {{-- Exercise header --}}
            <div class="p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:rgba(161,205,53,0.1);">
                        <i class="fa-solid fa-dumbbell text-sm" style="color:#A1CD35;"></i>
                    </div>
                    <div>
                        <p class="font-black text-[#121212]">{{ $item['exercise']->name }}</p>
                        <p class="text-xs text-[#616161]">{{ $item['exercise']->muscle_group }} · {{ $item['sessions'] }} sesiones</p>
                    </div>
                </div>

                {{-- Max weight + trend --}}
                <div class="text-right">
                    <p class="text-2xl font-black text-[#121212]">{{ $item['latest_weight'] }} <span class="text-sm font-normal text-[#616161]">kg</span></p>
                    @if($item['prev_weight'] !== null)
                    <p class="text-xs font-bold flex items-center justify-end gap-1" style="color:{{ $trendColor }};">
                        <i class="fa-solid {{ $trendIcon }} text-[10px]"></i>
                        {{ $trendLabel }}
                    </p>
                    @else
                    <p class="text-xs text-[#9ca3af]">Primera sesión</p>
                    @endif
                </div>
            </div>

            {{-- Chart --}}
            @if($item['dates']->count() > 1)
            <div class="px-5 pb-5">
                <canvas id="{{ $chartId }}" height="80"></canvas>
            </div>
            @else
            <div class="px-5 pb-5">
                <p class="text-xs text-center text-[#9ca3af] py-4">Entrena más sesiones para ver la progresión</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const exercises = @json($exerciseProgress);

    exercises.forEach(item => {
        if (item.dates.length < 2) return;

        const ctx = document.getElementById('chart-' + item.exercise.id);
        if (!ctx) return;

        const labels  = item.dates.map(d => {
            const [y, m, day] = d.split('-');
            return day + '/' + m;
        });
        const data    = item.max_weights;
        const maxW    = Math.max(...data);
        const minW    = Math.min(...data);
        const padding = Math.max(5, (maxW - minW) * 0.3);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Peso máx. (kg)',
                    data,
                    borderColor:     '#A1CD35',
                    backgroundColor: 'rgba(161,205,53,0.08)',
                    borderWidth:     2.5,
                    pointBackgroundColor: '#A1CD35',
                    pointRadius:     4,
                    pointHoverRadius: 6,
                    tension:         0.35,
                    fill:            true,
                }],
            },
            options: {
                responsive:          true,
                maintainAspectRatio: true,
                plugins: {
                    legend:  { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' kg',
                        },
                    },
                },
                scales: {
                    x: {
                        grid:  { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' },
                    },
                    y: {
                        min:   Math.max(0, minW - padding),
                        max:   maxW + padding,
                        grid:  { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 11 }, color: '#9ca3af', callback: v => v + ' kg' },
                    },
                },
            },
        });
    });
})();
</script>
@endpush
