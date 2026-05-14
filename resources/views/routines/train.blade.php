@extends('layouts.app')

@section('title', "Entrenando · {$routine->name}")

@php
    $exercisesJson = $routine->exercises->map(fn($ex) => [
        'id'           => $ex->id,
        'name'         => $ex->name,
        'muscle_group' => $ex->muscle_group,
        'sets'         => $ex->pivot->sets,
        'reps'         => $ex->pivot->reps,
    ])->values();
@endphp

@section('content')
<script>
    var _trainExercises = @json($exercisesJson);
    var _lastWeights    = @json($lastWeights);
</script>

<div class="max-w-md mx-auto pb-8" x-data="trainingSession(_trainExercises, _lastWeights)">

    {{-- ══════════════════════════════════════
         FASE: ENTRENAMIENTO COMPLETADO
    ══════════════════════════════════════ --}}
    <div x-show="phase === 'finished'" class="text-center space-y-6 pt-8">
        <div class="w-28 h-28 rounded-full flex items-center justify-center mx-auto"
             style="background:rgba(161,205,53,0.15);">
            <i class="fa-solid fa-flag-checkered text-5xl" style="color:#A1CD35;"></i>
        </div>
        <div>
            <p class="text-xs font-bold tracking-widest mb-2" style="color:#A1CD35;">¡ENTRENAMIENTO COMPLETADO!</p>
            <h1 class="text-3xl font-black text-[#121212]">{{ $routine->name }}</h1>
            <p class="text-[#616161] mt-3 text-sm">
                {{ $routine->exercises->count() }} ejercicios completados
                &nbsp;·&nbsp;
                <span class="font-bold text-[#121212]" x-text="formatTime(elapsed)"></span>
            </p>
        </div>

        {{-- Resumen de pesos --}}
        <div class="card p-5 text-left">
            <p class="text-xs font-bold tracking-wider text-[#616161] mb-3">RESUMEN</p>
            <div class="space-y-2">
                <template x-for="(ex, idx) in exercises" :key="idx">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center flex-shrink-0"
                             style="background:#A1CD35;">
                            <i class="fa-solid fa-check text-[10px] text-black"></i>
                        </div>
                        <p class="text-sm font-semibold text-[#121212] flex-1" x-text="ex.name"></p>
                        <p class="text-xs font-bold" style="color:#2D9CDB;"
                           x-text="getExMaxWeight(idx) ? getExMaxWeight(idx) + ' kg' : '—'"></p>
                    </div>
                </template>
            </div>
        </div>

        <form id="complete-form" action="{{ route('routines.complete', $routine->id) }}" method="POST">
            @csrf
            <div id="set-data"></div>
            <button type="button" @click="prepareAndSubmit()"
                    class="w-full py-4 rounded-2xl font-black text-base tracking-wide"
                    style="background:#A1CD35; color:#121212;">
                <i class="fa-solid fa-floppy-disk mr-2"></i>GUARDAR ENTRENAMIENTO
            </button>
        </form>
        <p>
            <a href="{{ route('routines.index') }}"
               onclick="return confirm('¿Salir sin guardar?')"
               class="text-xs" style="color:#d1d5db;">Descartar y salir</a>
        </p>
    </div>

    {{-- ══════════════════════════════════════
         FASE: ENTRENANDO
    ══════════════════════════════════════ --}}
    <div x-show="phase !== 'finished'" class="space-y-4">

        {{-- Header --}}
        <div class="flex items-center gap-3 pt-1">
            <a href="{{ route('routines.index') }}"
               onclick="return confirm('¿Abandonar el entrenamiento?')"
               class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm transition-all"
               style="color:#9ca3af;">
                <i class="fa-solid fa-xmark"></i>
            </a>
            <div class="flex-1 text-center">
                <p class="font-black text-[#121212] text-sm">{{ $routine->name }}</p>
            </div>
            <div class="bg-white rounded-xl px-3 py-1.5 shadow-sm text-right">
                <p class="text-[10px] text-[#9ca3af] font-bold leading-none">TIEMPO</p>
                <p class="text-sm font-black text-[#121212] font-mono" x-text="formatTime(elapsed)">00:00</p>
            </div>
        </div>

        {{-- Progress bar + exercise dots --}}
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <p class="text-[10px] font-bold text-[#9ca3af] tracking-widest">PROGRESO</p>
                <p class="text-[10px] font-bold" style="color:#A1CD35;">
                    <span x-text="exDoneCount"></span>/{{ $routine->exercises->count() }}
                </p>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden mb-2">
                <div class="h-full rounded-full transition-all duration-500" style="background:#A1CD35;"
                     :style="{ width: progressPct + '%' }"></div>
            </div>
            <div class="flex gap-1.5 justify-center">
                <template x-for="(ex, idx) in exercises" :key="idx">
                    <div class="rounded-full transition-all duration-300"
                         :style="idx === currentExIdx
                             ? 'background:#A1CD35; width:18px; height:5px;'
                             : (isExDone(idx)
                                 ? 'background:#A1CD35; opacity:0.3; width:5px; height:5px;'
                                 : 'background:#e5e7eb; width:5px; height:5px;')">
                    </div>
                </template>
            </div>
        </div>

        {{-- ──────────────────────────────────
             FASE: DESCANSANDO
        ────────────────────────────────── --}}
        <div x-show="phase === 'resting'">
            <div class="card p-6 text-center">

                {{-- Completado badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-5"
                     style="background:rgba(161,205,53,0.12);">
                    <i class="fa-solid fa-check text-xs" style="color:#A1CD35;"></i>
                    <p class="text-sm font-bold" style="color:#A1CD35;">
                        Serie <span x-text="completedSetNum"></span> completada
                    </p>
                </div>

                <p class="text-[10px] font-bold tracking-widest text-[#9ca3af] mb-1">DESCANSANDO</p>
                <p class="text-7xl font-black font-mono mb-4 tabular-nums" style="color:#121212;"
                   x-text="formatRestTime(restRemaining)">1:30</p>

                {{-- Rest progress arc --}}
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden mx-6 mb-6">
                    <div class="h-full rounded-full transition-all duration-1000"
                         style="background:#A1CD35;"
                         :style="{ width: ((restTime - restRemaining) / restTime * 100) + '%' }"></div>
                </div>

                {{-- Adjust rest --}}
                <div class="flex items-center justify-center gap-3 mb-5">
                    <button type="button" @click="adjustRest(-15)"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all hover:bg-gray-50"
                            style="border-color:#e5e7eb; color:#616161;">−15s</button>
                    <p class="text-xs text-[#9ca3af]">ajustar descanso</p>
                    <button type="button" @click="adjustRest(15)"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all hover:bg-gray-50"
                            style="border-color:#e5e7eb; color:#616161;">+15s</button>
                </div>

                <button type="button" @click="skipRest()"
                        class="w-full py-4 rounded-2xl font-black text-sm tracking-wide transition-all"
                        style="background:#A1CD35; color:#121212;">
                    <span x-text="nextActionLabel"></span>
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        {{-- ──────────────────────────────────
             FASE: SERIE ACTIVA
        ────────────────────────────────── --}}
        <div x-show="phase === 'ready'">
            <div class="card overflow-hidden">

                {{-- Ejercicio info --}}
                <div class="p-5 pb-4">
                    <p class="text-[10px] font-bold tracking-widest mb-1" style="color:#A1CD35;"
                       x-text="'EJERCICIO ' + (currentExIdx + 1) + ' DE ' + exercises.length"></p>
                    <h2 class="text-2xl font-black text-[#121212] leading-tight" x-text="currentEx.name"></h2>
                    <p class="text-sm mt-0.5" style="color:#9ca3af;" x-text="currentEx.muscle_group"></p>

                    <div class="mt-2 flex items-center gap-2" x-show="lastWeights[currentEx.id]">
                        <i class="fa-solid fa-clock-rotate-left text-[10px]" style="color:#2D9CDB;"></i>
                        <p class="text-xs" style="color:#9ca3af;">
                            Última sesión:
                            <span class="font-bold" style="color:#2D9CDB;"
                                  x-text="lastWeights[currentEx.id] + ' kg'"></span>
                        </p>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Serie actual --}}
                <div class="px-5 pt-5 pb-2 text-center">

                    {{-- Dots de series --}}
                    <div class="flex gap-2 justify-center mb-4">
                        <template x-for="si in Array.from({length: currentEx.sets}, (_, i) => i)" :key="si">
                            <div class="rounded-full transition-all duration-300"
                                 :style="si < currentSetIdx
                                     ? 'background:#A1CD35; width:10px; height:10px;'
                                     : (si === currentSetIdx
                                         ? 'background:#A1CD35; width:28px; height:10px; border-radius:5px;'
                                         : 'background:#e5e7eb; width:10px; height:10px;')">
                            </div>
                        </template>
                    </div>

                    <p class="text-[10px] font-bold tracking-widest text-[#9ca3af] mb-4"
                       x-text="'SERIE ' + (currentSetIdx + 1) + ' DE ' + currentEx.sets"></p>

                    {{-- Reps objetivo --}}
                    <div class="mb-5">
                        <span class="text-6xl font-black text-[#121212]" x-text="currentEx.reps"></span>
                        <span class="text-xl font-bold ml-2" style="color:#9ca3af;">reps</span>
                    </div>

                    {{-- Peso --}}
                    <div class="relative mb-1">
                        <input type="number"
                               :value="currentSet.weight"
                               @input="updateCurrentWeight($event.target.value)"
                               step="0.5" min="0"
                               class="w-full text-center font-black text-4xl py-4 rounded-2xl border-2 outline-none transition-all"
                               style="border-color:#e5e7eb; color:#121212; background:#fafafa;"
                               placeholder="0"
                               @focus="$el.style.borderColor='#A1CD35'; $el.style.background='#fff';"
                               @blur="$el.style.borderColor='#e5e7eb'; $el.style.background='#fafafa';">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-xl font-bold"
                              style="color:#d1d5db;">kg</span>
                    </div>
                    <p class="text-xs mb-6" style="color:#d1d5db;">peso a usar (opcional)</p>
                </div>

                {{-- CTA principal --}}
                <div class="px-5 pb-5 space-y-3">
                    <button type="button" @click="completeSet()"
                            class="w-full py-5 rounded-2xl font-black text-lg tracking-wide transition-all active:scale-95"
                            style="background:#A1CD35; color:#121212; box-shadow: 0 4px 14px rgba(161,205,53,0.35);">
                        <i class="fa-solid fa-check mr-2"></i>COMPLETAR SERIE
                    </button>

                    <div class="flex items-center justify-between px-1">
                        <button type="button" @click="goBack()"
                                class="text-sm font-semibold flex items-center gap-1.5 transition-colors"
                                :style="canGoBack ? 'color:#9ca3af;' : 'color:#e5e7eb;'"
                                :disabled="!canGoBack">
                            <i class="fa-solid fa-rotate-left text-xs"></i>Anterior
                        </button>
                        <button type="button" @click="skipSet()"
                                class="text-sm font-semibold flex items-center gap-1.5 transition-colors"
                                style="color:#9ca3af;">
                            Saltar <i class="fa-solid fa-forward-step text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Ejercicios ya completados --}}
            <div x-show="exDoneCount > 0" class="card p-4 mt-4">
                <p class="text-[10px] font-bold tracking-widest text-[#9ca3af] mb-3">YA COMPLETADOS</p>
                <div class="space-y-2.5">
                    <template x-for="(ex, idx) in exercises" :key="idx">
                        <div x-show="isExDone(idx)" class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0"
                                 style="background:#A1CD35;">
                                <i class="fa-solid fa-check" style="font-size:8px; color:#121212;"></i>
                            </div>
                            <p class="text-sm font-semibold text-[#121212] flex-1" x-text="ex.name"></p>
                            <p class="text-xs font-bold" style="color:#2D9CDB;"
                               x-text="getExMaxWeight(idx) ? getExMaxWeight(idx) + ' kg máx' : ''"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Abandonar (discreto) --}}
        <p class="text-center pt-2">
            <a href="{{ route('routines.index') }}"
               onclick="return confirm('¿Abandonar? No se guardará el progreso.')"
               class="text-xs" style="color:#e5e7eb;">
                Abandonar sesión
            </a>
        </p>

        {{-- Formulario oculto --}}
        <form id="complete-form" action="{{ route('routines.complete', $routine->id) }}" method="POST">
            @csrf
            <div id="set-data"></div>
        </form>

    </div>
</div>

@push('scripts')
<script>
function trainingSession(exercises, lastWeights) {
    const DEFAULT_REST = 90;

    const sets = exercises.map(ex =>
        Array.from({length: ex.sets}, () => ({ done: false, weight: '' }))
    );

    return {
        exercises,
        lastWeights,
        elapsed:        0,
        timer:          null,
        currentExIdx:   0,
        currentSetIdx:  0,
        phase:          'ready',   // 'ready' | 'resting' | 'finished'
        completedSetNum: 0,
        restTime:       DEFAULT_REST,
        restRemaining:  DEFAULT_REST,
        restTimer:      null,
        sets,

        get currentEx()    { return this.exercises[this.currentExIdx]; },
        get currentSet()   { return this.sets[this.currentExIdx][this.currentSetIdx]; },
        get isLastSet()    { return this.currentSetIdx >= this.currentEx.sets - 1; },
        get isLastEx()     { return this.currentExIdx >= this.exercises.length - 1; },
        get isLastOfAll()  { return this.isLastEx && this.isLastSet; },
        get canGoBack()    { return this.currentExIdx > 0 || this.currentSetIdx > 0; },

        get nextActionLabel() {
            if (this.isLastSet) return this.isLastEx ? 'TERMINAR' : 'SIGUIENTE EJERCICIO';
            return 'SIGUIENTE SERIE';
        },
        get exDoneCount() {
            return this.sets.filter(s => s.every(x => x.done)).length;
        },
        get progressPct() {
            return this.exercises.length ? (this.exDoneCount / this.exercises.length * 100) : 0;
        },

        isExDone(idx) {
            return this.sets[idx] && this.sets[idx].length > 0 && this.sets[idx].every(s => s.done);
        },
        getExMaxWeight(idx) {
            const ws = this.sets[idx].map(s => parseFloat(s.weight) || 0).filter(w => w > 0);
            return ws.length ? Math.max(...ws) : null;
        },

        completeSet() {
            const copy = this.sets.map(r => r.map(s => ({...s})));
            copy[this.currentExIdx][this.currentSetIdx].done = true;
            this.sets = copy;
            this.completedSetNum = this.currentSetIdx + 1;

            if (navigator.vibrate) navigator.vibrate(80);

            if (this.isLastOfAll) {
                this.phase = 'finished';
            } else {
                this.phase = 'resting';
                this.restRemaining = this.restTime;
                this.startRestTimer();
            }
        },

        skipSet() {
            if (this.isLastOfAll) {
                this.phase = 'finished';
            } else {
                this.advanceSet();
            }
        },

        startRestTimer() {
            if (this.restTimer) clearInterval(this.restTimer);
            this.restTimer = setInterval(() => {
                this.restRemaining--;
                if (this.restRemaining <= 0) {
                    clearInterval(this.restTimer);
                    this.skipRest();
                }
            }, 1000);
        },

        skipRest() {
            if (this.restTimer) clearInterval(this.restTimer);
            this.restTimer = null;
            this.phase = 'ready';
            this.advanceSet();
        },

        adjustRest(delta) {
            this.restRemaining = Math.max(5, this.restRemaining + delta);
            this.restTime      = Math.max(5, this.restTime + delta);
        },

        advanceSet() {
            if (!this.isLastSet) {
                this.currentSetIdx++;
            } else {
                this.currentExIdx++;
                this.currentSetIdx = 0;
            }
            this.autoFillCurrentWeight();
        },

        autoFillCurrentWeight() {
            const cur = this.sets[this.currentExIdx][this.currentSetIdx];
            if (cur.weight) return;

            if (this.currentSetIdx > 0) {
                const prev = this.sets[this.currentExIdx][this.currentSetIdx - 1];
                if (prev.weight) { this.updateCurrentWeight(prev.weight); return; }
            }
            const lastW = this.lastWeights[this.currentEx.id];
            if (lastW) this.updateCurrentWeight(String(lastW));
        },

        updateCurrentWeight(value) {
            const copy = this.sets.map(r => r.map(s => ({...s})));
            copy[this.currentExIdx][this.currentSetIdx].weight = value;
            this.sets = copy;
        },

        goBack() {
            if (!this.canGoBack) return;
            if (this.phase === 'resting') {
                if (this.restTimer) clearInterval(this.restTimer);
                this.restTimer = null;
                this.phase = 'ready';
                return;
            }
            const copy = this.sets.map(r => r.map(s => ({...s})));
            if (this.currentSetIdx > 0) {
                this.currentSetIdx--;
                copy[this.currentExIdx][this.currentSetIdx].done = false;
            } else {
                this.currentExIdx--;
                this.currentSetIdx = this.currentEx.sets - 1;
                copy[this.currentExIdx][this.currentSetIdx].done = false;
            }
            this.sets = copy;
        },

        formatTime(s) {
            const m = String(Math.floor(s / 60)).padStart(2, '0');
            return m + ':' + String(s % 60).padStart(2, '0');
        },
        formatRestTime(s) {
            return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
        },

        prepareAndSubmit() {
            const container = document.getElementById('set-data');
            container.innerHTML = '';
            this.sets.forEach((exSets, exIdx) => {
                const ex = this.exercises[exIdx];
                exSets.forEach((set, setIdx) => {
                    const fields = {
                        exercise_id: ex.id, set_number: setIdx + 1,
                        done: set.done ? 1 : 0, weight: set.weight || 0, reps: ex.reps,
                    };
                    Object.entries(fields).forEach(([f, v]) => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = `set_logs[${exIdx}][${setIdx}][${f}]`;
                        inp.value = v;
                        container.appendChild(inp);
                    });
                });
            });
            document.getElementById('complete-form').submit();
        },

        init() {
            this.timer = setInterval(() => this.elapsed++, 1000);
            this.autoFillCurrentWeight();
        },
    };
}
</script>
@endpush
@endsection
