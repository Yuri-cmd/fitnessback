@extends('layouts.app')

@section('title', 'Control de Peso · POWER STACK')

@section('content')
<div class="space-y-8" x-data="{ showModal: false, showProfileModal: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold tracking-widest mb-1" style="color:#A1CD35;">SEGUIMIENTO</p>
            <h1 class="text-2xl font-black text-[#121212]">Control de Peso</h1>
        </div>
        <div class="flex gap-2">
            <button @click="showProfileModal = true"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-[#616161] hover:shadow-sm transition-all">
                <i class="fa-solid fa-user-pen"></i>
                <span class="hidden sm:inline">Perfil</span>
            </button>
            <button @click="showModal = true"
                    class="btn-primary flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Registrar peso
            </button>
        </div>
    </div>

    {{-- Métricas IMC --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="card p-5 text-center">
            <p class="text-xs font-bold text-[#616161] tracking-wider mb-2">PESO ACTUAL</p>
            <p class="text-2xl font-black" style="color:#A1CD35;">
                {{ $profile?->current_weight ? $profile->current_weight . ' kg' : '--' }}
            </p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-xs font-bold text-[#616161] tracking-wider mb-2">IMC</p>
            <p class="text-2xl font-black text-[#121212]">{{ $bmi ?? '--' }}</p>
            <p class="text-xs text-[#616161] mt-1">{{ $bmiCategory }}</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-xs font-bold text-[#616161] tracking-wider mb-2">META</p>
            <p class="text-2xl font-black text-[#121212]">
                @if($weightToLose > 0)
                    <span style="color:#F2994A;">-{{ $weightToLose }} kg</span>
                @elseif($bmi)
                    <span style="color:#A1CD35;">OK</span>
                @else
                    --
                @endif
            </p>
        </div>
    </div>

    {{-- Gráfica --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-black text-[#121212]">PROGRESO HISTÓRICO</h2>
                <p class="text-xs text-[#616161] mt-0.5">Evolución de tu peso en el tiempo</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full"
                  style="background:rgba(161,205,53,0.12); color:#A1CD35;">
                {{ $weightLogs->count() }} registros
            </span>
        </div>

        @if($chartData->count() > 0)
        <div class="relative" style="height:260px;">
            <canvas id="weightChart"></canvas>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                 style="background:rgba(161,205,53,0.1);">
                <i class="fa-solid fa-chart-line text-2xl" style="color:#A1CD35;"></i>
            </div>
            <p class="font-bold text-[#121212]">Sin datos aún</p>
            <p class="text-sm text-[#616161] mt-1">Registra tu primer peso para ver la gráfica</p>
        </div>
        @endif
    </div>

    {{-- Historial --}}
    <div>
        <h2 class="font-black text-[#121212] tracking-wide mb-4">TODOS LOS REGISTROS</h2>

        @if($weightLogs->isEmpty())
        <div class="card p-8 flex flex-col items-center justify-center text-center">
            <i class="fa-solid fa-scale-balanced text-4xl mb-4" style="color:rgba(161,205,53,0.4);"></i>
            <p class="font-bold text-[#121212]">Sin registros de peso</p>
            <p class="text-sm text-[#616161] mt-1 mb-4">Empieza a registrar tu peso hoy</p>
            <button @click="showModal = true" class="btn-primary">
                <i class="fa-solid fa-plus mr-2"></i>Primer registro
            </button>
        </div>
        @else
        <div class="space-y-3">
            @foreach($weightLogs as $log)
            <div class="card px-5 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(161,205,53,0.12);">
                    <i class="fa-solid fa-scale-balanced text-sm" style="color:#A1CD35;"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-[#121212] text-lg">{{ $log->weight }} kg</p>
                    <p class="text-xs text-[#616161]">{{ $log->created_at->format('d/m/Y') }} · {{ $log->created_at->diffForHumans() }}</p>
                </div>
                @if($loop->first)
                <span class="text-xs font-bold px-2 py-1 rounded-lg"
                      style="background:rgba(161,205,53,0.12); color:#A1CD35;">
                    ÚLTIMO
                </span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Modal: Registrar peso --}}
<div x-show="showModal"
     x-cloak
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     @click.self="showModal = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
         @click.stop>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-[#121212]">NUEVO REGISTRO</h3>
            <button @click="showModal = false"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-[#616161]">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('weight.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">PESO (kg)</label>
                <input type="number" name="weight" step="0.1" min="20" max="300"
                       class="form-input text-center text-2xl font-bold"
                       placeholder="75.5" required autofocus>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" @click="showModal = false" class="btn-ghost flex-1">CANCELAR</button>
                <button type="submit" class="btn-primary flex-1">GUARDAR</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Actualizar perfil --}}
<div x-show="showProfileModal"
     x-cloak
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     @click.self="showProfileModal = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-[#121212]">ACTUALIZAR DATOS</h3>
            <button @click="showProfileModal = false"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-[#616161]">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">TALLA (cm)</label>
                <input type="number" name="height" step="0.1" value="{{ $profile?->height }}"
                       class="form-input" placeholder="175">
            </div>
            <div>
                <label class="form-label">PESO ACTUAL (kg)</label>
                <input type="number" name="current_weight" step="0.1" value="{{ $profile?->current_weight }}"
                       class="form-input" placeholder="75">
            </div>
            <div>
                <label class="form-label">PESO META (kg)</label>
                <input type="number" name="goal_weight" step="0.1" value="{{ $profile?->goal_weight }}"
                       class="form-input" placeholder="70">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" @click="showProfileModal = false" class="btn-ghost flex-1">CANCELAR</button>
                <button type="submit" class="btn-primary flex-1">GUARDAR</button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    @if($chartData->count() > 0)
    const labels  = @json($chartData->pluck('date'));
    const weights = @json($chartData->pluck('weight'));

    new Chart(document.getElementById('weightChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Peso (kg)',
                data: weights,
                borderColor: '#A1CD35',
                backgroundColor: 'rgba(161,205,53,0.08)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#A1CD35',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} kg`
                    }
                }
            },
            scales: {
                y: {
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#9ca3af', font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af', font: { size: 11 } }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
