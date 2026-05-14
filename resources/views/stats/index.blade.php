@extends('layouts.app')

@section('title', 'Estadísticas · POWER STACK')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .heatmap-container {
        display: grid;
        grid-template-columns: repeat(53, 1fr);
        grid-auto-rows: 12px;
        gap: 3px;
        overflow-x: auto;
        padding: 4px;
    }
    .heatmap-cell {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        background-color: #ebedf0;
    }
    .heatmap-cell.active-1 { background-color: #d6e685; }
    .heatmap-cell.active-2 { background-color: #8cc665; }
    .heatmap-cell.active-3 { background-color: #44a340; }
    .heatmap-cell.active-4 { background-color: #1e6823; }
</style>
@endpush

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <p class="text-xs font-bold tracking-widest mb-1 text-primary">ANALYTICS</p>
        <h1 class="text-3xl font-black text-[#121212]">Tu Progreso en <span class="text-primary">Datos</span></h1>
    </div>

    {{-- Top Cards: Totals & Heatmap --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Activity Heatmap --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-6">
                <p class="text-xs font-bold text-[#616161] tracking-widest">ACTIVIDAD ESTE AÑO</p>
                <span class="text-xs font-bold px-2 py-1 rounded bg-primary/10 text-primary">
                    {{ $activity->sum() }} Sesiones
                </span>
            </div>
            
            <div class="heatmap-container mb-4">
                @php
                    $start = now()->startOfYear();
                    $end = now()->endOfYear();
                    $current = $start->copy();
                @endphp
                @while($current->lte($end))
                    @php
                        $dateStr = $current->format('Y-m-d');
                        $count = $activity[$dateStr] ?? 0;
                        $level = $count > 0 ? min(4, ceil($count / 1)) : 0;
                    @endphp
                    <div class="heatmap-cell {{ $level > 0 ? 'active-'.$level : '' }}" title="{{ $dateStr }}: {{ $count }} entrenos"></div>
                    @php $current->addDay(); @endphp
                @endwhile
            </div>
            <div class="flex justify-between items-center text-[10px] text-[#A0A0A0]">
                <div class="flex gap-4">
                    <span>Ene</span><span>Feb</span><span>Mar</span><span>Abr</span><span>May</span><span>Jun</span><span>Jul</span><span>Ago</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dic</span>
                </div>
                <div class="flex items-center gap-1">
                    <span>Menos</span>
                    <div class="w-2.5 h-2.5 rounded-sm bg-[#ebedf0]"></div>
                    <div class="w-2.5 h-2.5 rounded-sm bg-[#d6e685]"></div>
                    <div class="w-2.5 h-2.5 rounded-sm bg-[#8cc665]"></div>
                    <div class="w-2.5 h-2.5 rounded-sm bg-[#44a340]"></div>
                    <div class="w-2.5 h-2.5 rounded-sm bg-[#1e6823]"></div>
                    <span>Más</span>
                </div>
            </div>
        </div>

        {{-- Personal Records (PRs) --}}
        <div class="card p-6 overflow-hidden">
            <p class="text-xs font-bold text-[#616161] tracking-widest mb-4">TOP 5 RECORDS PERSONALES</p>
            <div class="space-y-4">
                @forelse($prs->take(5) as $pr)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-[#121212] truncate w-32">{{ $pr->name }}</p>
                        <p class="text-[10px] text-[#616161] font-bold">PESO MÁXIMO</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-black text-primary">{{ $pr->max_weight }} <span class="text-xs">kg</span></p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-[#616161] italic">Entrena para ver tus records.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Weight Chart --}}
        <div class="card p-6">
            <p class="text-xs font-bold text-[#616161] tracking-widest mb-6">HISTORIAL DE PESO (Últimos registros)</p>
            <div class="relative h-[300px]">
                <canvas id="weightChart"></canvas>
            </div>
        </div>

        {{-- Volume by Muscle Group --}}
        <div class="card p-6">
            <p class="text-xs font-bold text-[#616161] tracking-widest mb-6">VOLUMEN POR GRUPO MUSCULAR (30 días)</p>
            <div class="relative h-[300px]">
                <canvas id="muscleChart"></canvas>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Weight Chart
    const weightCtx = document.getElementById('weightChart').getContext('2d');
    new Chart(weightCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($weightHistory->pluck('logged_at')->map(fn($d) => $d->format('d M'))) !!},
            datasets: [{
                label: 'Peso (kg)',
                data: {!! json_encode($weightHistory->pluck('weight')) !!},
                borderColor: '#A1CD35',
                backgroundColor: 'rgba(161, 205, 53, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#FFF',
                pointBorderColor: '#A1CD35',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: false, grid: { color: '#F0F0F0' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Muscle Chart
    const muscleCtx = document.getElementById('muscleChart').getContext('2d');
    new Chart(muscleCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($volumeByMuscle->pluck('muscle_group')) !!},
            datasets: [{
                data: {!! json_encode($volumeByMuscle->pluck('total_volume')) !!},
                backgroundColor: [
                    '#A1CD35', '#2D9CDB', '#F2994A', '#EB5757', '#9B51E0', '#2196F3'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } }
            }
        }
    });
</script>
@endpush
@endsection
