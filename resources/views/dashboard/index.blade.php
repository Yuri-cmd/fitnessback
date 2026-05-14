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

    {{-- BMI & TDEE Card --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        </div>

        <div class="rounded-2xl p-6 bg-white border border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="text-xs font-bold tracking-widest mb-2 text-secondary">CALORÍAS RECOMENDADAS</p>
                    <h2 class="text-2xl font-black text-[#121212]">
                        {{ $profile?->tdee ?? '---' }} <span class="text-sm font-bold text-[#616161]">kcal/día</span>
                    </h2>
                    <p class="text-[#616161] text-sm mt-1">
                        Basado en tu TDEE y nivel de actividad.
                    </p>
                    @if(!$profile?->tdee)
                        <p class="text-[10px] font-bold text-alert mt-2 uppercase tracking-tighter">
                            <i class="fa-solid fa-circle-info"></i> Completa tu perfil para calcular
                        </p>
                    @endif
                </div>
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(45,156,219,0.1);">
                    <i class="fa-solid fa-fire-flame-curved text-2xl text-secondary"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Módulos Intermedios: Streaks & Water --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Streak Card --}}
        <div class="card p-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center">
                <i class="fa-solid fa-fire text-2xl text-orange-500"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-[#616161] tracking-widest">RACHA ACTUAL</p>
                <p class="text-2xl font-black text-[#121212]">{{ $streak }} {{ $streak == 1 ? 'Día' : 'Días' }}</p>
            </div>
        </div>

        {{-- Water Card --}}
        <div class="card p-6 md:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-droplet text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-[#616161] tracking-widest uppercase">Hidratación Hoy</p>
                        <p class="text-lg font-black text-[#121212]">{{ $waterToday / 1000 }}L / 2.5L</p>
                    </div>
                </div>
                <form action="{{ route('water.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="amount" value="250">
                    <button type="submit" class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 transition-colors shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                    <p class="text-[10px] font-bold text-blue-500 self-center">+250ml</p>
                </form>
            </div>
            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                @php $waterPercent = min(100, ($waterToday / 2500) * 100); @endphp
                <div class="bg-blue-500 h-full transition-all duration-500" style="width: {{ $waterPercent }}%"></div>
            </div>
        </div>
    </div>

    {{-- Módulos --}}
    <div>
        <p class="text-xs font-bold text-[#616161] tracking-widest mb-4">MÓDULOS PRINCIPALES</p>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">

            <a href="{{ route('weight.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 transition-colors"
                     style="background:rgba(161,205,53,0.12);">
                    <i class="fa-solid fa-scale-balanced text-xl" style="color:#A1CD35;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-[11px]">PESO</p>
            </a>

            <a href="{{ route('routines.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
                     style="background:rgba(45,156,219,0.12);">
                    <i class="fa-solid fa-dumbbell text-xl" style="color:#2D9CDB;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-[11px]">RUTINAS</p>
            </a>

            <a href="{{ route('stats.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
                     style="background:rgba(155,81,224,0.12);">
                    <i class="fa-solid fa-chart-pie text-xl" style="color:#9B51E0;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-[11px]">STATS</p>
            </a>

            <a href="{{ route('wiki.index') }}"
               class="card p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow cursor-pointer group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
                     style="background:rgba(242,153,74,0.12);">
                    <i class="fa-solid fa-book-bookmark text-xl" style="color:#F2994A;"></i>
                </div>
                <p class="font-black text-[#121212] tracking-widest text-[11px]">WIKI</p>
            </a>
        </div>
    </div>

    {{-- Logros / Medallas (Full Width) --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold text-[#616161] tracking-widest uppercase">Tus Logros y Reconocimientos</p>
            <a href="{{ route('stats.index') }}" class="text-[10px] font-bold text-primary uppercase hover:underline">Ver todas las estadísticas →</a>
        </div>
        <div class="flex flex-wrap gap-4">
            @php
                $earnedIds = $user->achievements->pluck('id')->toArray();
            @endphp
            @foreach(\App\Models\Achievement::all() as $achievement)
                @php $isEarned = in_array($achievement->id, $earnedIds); @endphp
                <div class="flex-1 min-w-[120px] max-w-[160px] card p-4 flex flex-col items-center text-center transition-all group relative cursor-help
                    {{ $isEarned ? 'border-primary shadow-sm' : 'opacity-40 grayscale border-gray-100' }}">
                    
                    <div class="w-12 h-12 rounded-full mb-3 flex items-center justify-center {{ $isEarned ? 'bg-primary/10' : 'bg-gray-100' }}">
                        <i class="{{ $achievement->icon }} text-xl {{ $isEarned ? 'text-primary' : 'text-gray-400' }}"></i>
                    </div>
                    
                    <p class="text-[10px] font-black text-[#121212] leading-tight uppercase">{{ $achievement->name }}</p>
                    
                    {{-- Tooltip --}}
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 bg-[#121212] text-white text-[10px] p-3 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 text-center shadow-2xl border border-white/10">
                        <p class="font-black text-primary uppercase mb-1">{{ $achievement->name }}</p>
                        <p class="opacity-80">{{ $achievement->description }}</p>
                        @if($isEarned)
                            <div class="mt-2 pt-2 border-t border-white/10 text-primary font-bold">¡DESBLOQUEADO!</div>
                        @else
                            <div class="mt-2 pt-2 border-t border-white/10 text-gray-500 font-bold">BLOQUEADO</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Resumen semanal --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold text-[#616161] tracking-widest uppercase">Resumen Semanal de Actividad</p>
            <a href="{{ route('routines.index') }}" class="text-[10px] font-bold text-primary uppercase hover:underline">Historial completo →</a>
        </div>
        <div class="card p-8">
            @php
                $dayNames = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                $completedCount = collect($weeklyProgress)->filter()->count();
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-7 gap-4 mb-8">
                @foreach($dayNames as $i => $day)
                <div class="flex flex-col items-center gap-3">
                    <span class="text-[10px] text-[#616161] font-bold tracking-widest uppercase">{{ $day }}</span>
                    @if($weeklyProgress[$i + 1] ?? false)
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm"
                             style="background:#A1CD35;">
                            <i class="fa-solid fa-check text-white text-lg"></i>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-2xl border-2 border-gray-100 flex items-center justify-center bg-gray-50/50">
                            <span class="text-gray-300 text-sm font-black">{{ $i + 1 }}</span>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 pt-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#616161]">
                        Esta semana has completado <span class="font-black text-[#121212]">{{ $completedCount }}</span> entrenamientos.
                    </p>
                    <p class="text-xs text-[#A0A0A0] mt-1">¡Sigue con esa disciplina!</p>
                </div>
                <div class="flex gap-2">
                    @if($completedCount >= 5)
                        <span class="text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider" style="background:rgba(161,205,53,0.15); color:#A1CD35;">
                            🔥 SEMANA ELITE
                        </span>
                    @elseif($completedCount >= 3)
                        <span class="text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider" style="background:rgba(45,156,219,0.12); color:#2D9CDB;">
                            💪 BUEN RITMO
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Acceso rápido - botón perfil --}}
    <div class="flex justify-end">
        <button onclick="document.getElementById('modal-profile').showModal()"
                class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-[#616161] hover:bg-white hover:shadow-sm transition-all">
            <i class="fa-solid fa-user-pen"></i>
            Configuración Avanzada del Perfil
        </button>
    </div>
</div>

{{-- Modal Perfil --}}
<dialog id="modal-profile" class="rounded-2xl shadow-2xl p-0 w-full max-w-lg backdrop:bg-black/50">
    <div class="p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-black text-[#121212] tracking-wide">DATOS DE RENDIMIENTO</h3>
                <p class="text-sm text-[#616161]">Utilizamos estos datos para calcular tu TDEE e IMC.</p>
            </div>
            <button onclick="document.getElementById('modal-profile').close()"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-[#616161]">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">TALLA (cm)</label>
                    <input type="number" name="height" step="0.1" value="{{ $profile?->height }}"
                           class="form-input" placeholder="Ej. 175">
                </div>
                <div>
                    <label class="form-label">GÉNERO</label>
                    <select name="gender" class="form-input">
                        <option value="male" {{ $profile?->gender == 'male' ? 'selected' : '' }}>Hombre</option>
                        <option value="female" {{ $profile?->gender == 'female' ? 'selected' : '' }}>Mujer</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">PESO ACTUAL (kg)</label>
                    <input type="number" name="current_weight" step="0.1" value="{{ $profile?->current_weight }}"
                           class="form-input" placeholder="Ej. 75">
                </div>
                <div>
                    <label class="form-label">PESO META (kg)</label>
                    <input type="number" name="goal_weight" step="0.1" value="{{ $profile?->goal_weight }}"
                           class="form-input" placeholder="Ej. 70">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">FECHA NACIMIENTO</label>
                    <input type="date" name="birth_date" value="{{ $profile?->birth_date?->format('Y-m-d') }}"
                           class="form-input">
                </div>
                <div>
                    <label class="form-label">NIVEL ACTIVIDAD</label>
                    <select name="activity_level" class="form-input">
                        <option value="sedentary" {{ $profile?->activity_level == 'sedentary' ? 'selected' : '' }}>Sedentario</option>
                        <option value="lightly_active" {{ $profile?->activity_level == 'lightly_active' ? 'selected' : '' }}>Ligero (1-3 días)</option>
                        <option value="moderately_active" {{ $profile?->activity_level == 'moderately_active' ? 'selected' : '' }}>Moderado (3-5 días)</option>
                        <option value="very_active" {{ $profile?->activity_level == 'very_active' ? 'selected' : '' }}>Muy Activo (6-7 días)</option>
                        <option value="extra_active" {{ $profile?->activity_level == 'extra_active' ? 'selected' : '' }}>Atleta (2x día)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('modal-profile').close()"
                        class="btn-ghost flex-1 uppercase text-xs tracking-widest">CANCELAR</button>
                <button type="submit" class="btn-primary flex-1 uppercase text-xs tracking-widest">GUARDAR DATOS</button>
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
