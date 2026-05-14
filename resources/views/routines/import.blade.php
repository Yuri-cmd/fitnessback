@extends('layouts.app')

@section('title', 'Importar rutina · POWER STACK')

@section('content')
<script>var _allExercises = @json($exercises->pluck('name'));</script>

<div class="max-w-5xl mx-auto" x-data="importBuilder(_allExercises)">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('routines.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm transition-all text-[#616161]">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <p class="text-xs font-bold tracking-widest" style="color:#A1CD35;">NUEVA RUTINA</p>
            <h1 class="text-2xl font-black text-[#121212]">Importar desde foto</h1>
        </div>
    </div>

    <form action="{{ route('routines.import.store') }}" method="POST" @submit="prepareSubmit">
        @csrf

        <div class="lg:grid lg:grid-cols-2 lg:gap-6 lg:items-start space-y-5 lg:space-y-0">

            {{-- ── COLUMNA IZQUIERDA: Foto ── --}}
            <div class="lg:sticky lg:top-6 space-y-3">

                {{-- Zona de carga --}}
                <div x-show="!imageUrl" class="card">
                    <label for="routine-image"
                           class="flex flex-col items-center justify-center gap-4 p-10 cursor-pointer
                                  border-2 border-dashed border-gray-200 rounded-2xl
                                  hover:border-[#A1CD35] hover:bg-[rgba(161,205,53,0.03)] transition-all">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                             style="background:rgba(161,205,53,0.1);">
                            <i class="fa-solid fa-camera text-2xl" style="color:#A1CD35;"></i>
                        </div>
                        <div class="text-center">
                            <p class="font-black text-[#121212]">Cargar foto de rutina</p>
                            <p class="text-sm text-[#616161] mt-1">JPG, PNG — desde cámara o galería</p>
                        </div>
                        <span class="btn-primary text-sm px-5 py-2.5">
                            <i class="fa-solid fa-upload mr-2"></i>Seleccionar imagen
                        </span>
                    </label>
                    <input id="routine-image" type="file" accept="image/*" capture="environment"
                           class="hidden" @change="loadImage($event)">
                </div>

                {{-- Preview de la imagen + botón OCR --}}
                <div x-show="imageUrl" class="card overflow-hidden">
                    <div class="p-3 flex items-center justify-between border-b border-gray-100">
                        <p class="text-xs font-bold text-[#616161]">
                            <i class="fa-solid fa-image mr-1.5" style="color:#A1CD35;"></i>
                            Foto cargada
                        </p>
                        <label for="routine-image-2"
                               class="text-xs font-bold cursor-pointer hover:underline"
                               style="color:#2D9CDB;">Cambiar</label>
                        <input id="routine-image-2" type="file" accept="image/*" capture="environment"
                               class="hidden" @change="loadImage($event)">
                    </div>

                    <div class="relative overflow-hidden" style="max-height:55vh;">
                        <img :src="imageUrl" alt="Rutina" class="w-full object-contain" style="max-height:55vh;">
                        <button type="button" @click="showZoom = true"
                                class="absolute bottom-3 right-3 w-9 h-9 rounded-xl flex items-center justify-center shadow-lg"
                                style="background:rgba(0,0,0,0.55); color:#fff;">
                            <i class="fa-solid fa-expand text-sm"></i>
                        </button>
                    </div>

                    {{-- Botón OCR --}}
                    <div class="p-4 border-t border-gray-100">
                        {{-- Estado normal --}}
                        <button type="button" x-show="!analyzing && !ocrDone"
                                @click="runOCR()"
                                class="w-full py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all"
                                style="background:#121212; color:#fff;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            Reconocer texto automáticamente
                        </button>

                        {{-- Analizando --}}
                        <div x-show="analyzing" class="space-y-2">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold text-[#616161]">Leyendo imagen...</p>
                                <p class="text-xs font-bold" style="color:#A1CD35;" x-text="ocrProgress + '%'"></p>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                     style="background:#A1CD35;"
                                     :style="{ width: ocrProgress + '%' }"></div>
                            </div>
                            <p class="text-xs text-center text-[#9ca3af]" x-text="ocrStatus"></p>
                        </div>

                        {{-- Completado --}}
                        <div x-show="ocrDone && !analyzing" class="space-y-2">
                            <div class="flex items-center gap-2 p-3 rounded-xl"
                                 style="background:rgba(161,205,53,0.1);">
                                <i class="fa-solid fa-check-circle" style="color:#A1CD35;"></i>
                                <p class="text-sm font-bold" style="color:#A1CD35;" x-text="ocrMessage"></p>
                            </div>
                            <button type="button" @click="ocrDone = false; runOCR()"
                                    class="w-full py-2 rounded-xl text-xs font-bold border border-gray-200 text-[#616161] hover:bg-gray-50 transition-all">
                                <i class="fa-solid fa-rotate mr-1"></i> Volver a leer
                            </button>
                        </div>

                        {{-- Error --}}
                        <div x-show="ocrError && !analyzing"
                             class="p-3 rounded-xl" style="background:rgba(239,68,68,0.08);">
                            <p class="text-xs font-bold text-red-500 flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span x-text="ocrError"></span>
                            </p>
                            <button type="button" @click="ocrError = null; runOCR()"
                                    class="mt-2 text-xs font-bold text-red-400 hover:text-red-600">
                                Reintentar
                            </button>
                        </div>

                        <p class="text-[10px] text-center mt-2" style="color:#d1d5db;">
                            <i class="fa-solid fa-eye-slash mr-1"></i>
                            La imagen no se sube — el OCR corre en tu dispositivo
                        </p>
                    </div>
                </div>

                {{-- Tip --}}
                <div class="p-4 rounded-xl" style="background:rgba(45,156,219,0.06); border:1px solid rgba(45,156,219,0.15);">
                    <p class="text-xs font-bold mb-1" style="color:#2D9CDB;">
                        <i class="fa-solid fa-lightbulb mr-1.5"></i>Consejos para mejor resultado
                    </p>
                    <ul class="text-xs text-[#616161] space-y-1 leading-relaxed">
                        <li>· Foto bien iluminada y sin ángulo</li>
                        <li>· El texto debe ser legible (no borroso)</li>
                        <li>· Revisa siempre los resultados — el OCR puede fallar con formatos variados</li>
                    </ul>
                </div>
            </div>

            {{-- ── COLUMNA DERECHA: Formulario ── --}}
            <div class="space-y-5">

                {{-- Nombre --}}
                <div class="card p-5">
                    <h2 class="font-black text-[#121212] text-xs tracking-wider mb-3">NOMBRE DE LA RUTINA</h2>
                    <input type="text" name="name"
                           class="form-input text-lg font-bold"
                           placeholder="Ej. Día de piernas, Full Body..."
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ejercicios --}}
                <div class="card p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-black text-[#121212] text-xs tracking-wider">EJERCICIOS</h2>
                        <span class="text-xs text-[#616161]"
                              x-text="rows.length + ' ejercicio' + (rows.length !== 1 ? 's' : '')"></span>
                    </div>

                    {{-- Cabecera columnas --}}
                    <div class="grid grid-cols-12 gap-2 px-1">
                        <p class="col-span-6 text-[10px] font-bold text-[#9ca3af] tracking-widest">EJERCICIO</p>
                        <p class="col-span-2 text-[10px] font-bold text-[#9ca3af] tracking-widest text-center">SER.</p>
                        <p class="col-span-2 text-[10px] font-bold text-[#9ca3af] tracking-widest text-center">REPS</p>
                        <p class="col-span-2"></p>
                    </div>

                    {{-- Filas --}}
                    <div class="space-y-2.5">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-12 gap-2 items-start">
                                <div class="col-span-6 relative">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-black text-[#9ca3af]"
                                              x-text="index + 1"></span>
                                        <input type="text"
                                               :value="row.name"
                                               @input="row.name = $event.target.value; filterSuggestions($event.target.value, index)"
                                               @blur="hideSuggestions()"
                                               @keydown.escape="hideSuggestions()"
                                               @keydown.enter.prevent="suggestions.length && selectSuggestion(suggestions[0], index)"
                                               class="form-input text-sm py-2.5 pl-7"
                                               placeholder="Nombre del ejercicio"
                                               autocomplete="off">
                                    </div>
                                    <div x-show="activeRow === index && suggestions.length > 0"
                                         class="absolute top-full left-0 right-0 z-20 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden mt-1">
                                        <template x-for="(sug, si) in suggestions" :key="si">
                                            <button type="button"
                                                    @mousedown.prevent="selectSuggestion(sug, index)"
                                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors flex items-center gap-2">
                                                <i class="fa-solid fa-dumbbell text-xs" style="color:#A1CD35;"></i>
                                                <span x-text="sug"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <input type="number" x-model="row.sets" min="1" max="99"
                                           class="form-input text-sm py-2.5 text-center px-1">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" x-model="row.reps" min="1" max="999"
                                           class="form-input text-sm py-2.5 text-center px-1">
                                </div>
                                <div class="col-span-2 flex justify-center pt-1">
                                    <button type="button" @click="removeRow(index)"
                                            :disabled="rows.length === 1"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                            :class="rows.length === 1 ? 'text-gray-200' : 'hover:bg-red-50 text-red-400'">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addRow()"
                            class="w-full py-2.5 rounded-xl border-2 border-dashed border-gray-200 text-sm font-semibold text-[#616161]
                                   hover:border-[#A1CD35] hover:text-[#A1CD35] transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Agregar ejercicio
                    </button>

                    <div id="hidden-exercise-inputs"></div>
                </div>

                {{-- Acciones --}}
                <div class="flex gap-3">
                    <a href="{{ route('routines.index') }}" class="btn-ghost flex-1 text-center">CANCELAR</a>
                    <button type="submit"
                            :disabled="rows.some(r => !r.name.trim())"
                            class="btn-primary flex-1 flex items-center justify-center gap-2"
                            :class="rows.some(r => !r.name.trim()) ? 'opacity-50 cursor-not-allowed' : ''">
                        <i class="fa-solid fa-floppy-disk"></i>
                        GUARDAR RUTINA
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal zoom --}}
    <div x-show="showZoom && imageUrl" x-cloak
         @click="showZoom = false"
         class="fixed inset-0 z-50 bg-black/85 flex items-center justify-center p-4 cursor-zoom-out">
        <img :src="imageUrl" class="max-w-full max-h-full rounded-xl object-contain" @click.stop>
        <button type="button" @click="showZoom = false"
                class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center"
                style="background:rgba(255,255,255,0.15); color:#fff;">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
{{-- Tesseract.js — OCR en el navegador, sin servidor --}}
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
<script>
/**
 * ─── Fuzzy Matching Engine (Levenshtein + normalización) ───
 * Compara el texto OCR contra la base de ejercicios sin depender de IA.
 */
const FuzzyMatcher = {
    _cache: {},

    /** Normaliza texto: minúsculas, sin tildes, sin caracteres especiales */
    normalize(str) {
        return str
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    },

    /** Distancia de Levenshtein optimizada con early-exit */
    levenshtein(a, b) {
        if (a === b) return 0;
        if (!a.length) return b.length;
        if (!b.length) return a.length;

        // Early exit si la diferencia de longitud ya supera el umbral razonable
        if (Math.abs(a.length - b.length) > Math.max(a.length, b.length) * 0.6) return Infinity;

        const matrix = [];
        for (let i = 0; i <= b.length; i++) matrix[i] = [i];
        for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

        for (let i = 1; i <= b.length; i++) {
            for (let j = 1; j <= a.length; j++) {
                const cost = b[i - 1] === a[j - 1] ? 0 : 1;
                matrix[i][j] = Math.min(
                    matrix[i - 1][j] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j - 1] + cost
                );
            }
        }
        return matrix[b.length][a.length];
    },

    /** Similitud 0-1 basada en Levenshtein */
    similarity(a, b) {
        const na = this.normalize(a);
        const nb = this.normalize(b);
        if (na === nb) return 1;
        const maxLen = Math.max(na.length, nb.length);
        if (maxLen === 0) return 1;
        return 1 - (this.levenshtein(na, nb) / maxLen);
    },

    /** Verifica si una cadena contiene las palabras clave de otra */
    containsKeywords(ocrText, exerciseName) {
        const ocrWords = this.normalize(ocrText).split(' ').filter(w => w.length > 2);
        const exWords = this.normalize(exerciseName).split(' ').filter(w => w.length > 2);
        if (exWords.length === 0) return 0;
        let matches = 0;
        for (const ew of exWords) {
            for (const ow of ocrWords) {
                if (ow.includes(ew) || ew.includes(ow) || this.levenshtein(ow, ew) <= 2) {
                    matches++;
                    break;
                }
            }
        }
        return matches / exWords.length;
    },

    /**
     * Busca el mejor match en la lista de ejercicios.
     * Retorna { name, score } o null si no hay match aceptable.
     * threshold: mínimo de similitud (0-1) para aceptar un match.
     */
    bestMatch(ocrText, exerciseList, threshold = 0.45) {
        if (!ocrText || ocrText.length < 2) return null;

        const cacheKey = this.normalize(ocrText);
        if (this._cache[cacheKey]) return this._cache[cacheKey];

        let best = null;
        let bestScore = 0;

        for (const name of exerciseList) {
            // Score combinado: 60% Levenshtein + 40% keyword matching
            const simScore = this.similarity(ocrText, name);
            const kwScore = this.containsKeywords(ocrText, name);
            const combined = simScore * 0.6 + kwScore * 0.4;

            if (combined > bestScore) {
                bestScore = combined;
                best = name;
            }
        }

        const result = bestScore >= threshold ? { name: best, score: bestScore } : null;
        this._cache[cacheKey] = result;
        return result;
    },

    /** Limpia la caché (útil si cambia la lista de ejercicios) */
    clearCache() { this._cache = {}; }
};

/**
 * ─── Preprocesamiento de imagen con Canvas ───
 * Mejora la calidad de la imagen antes de pasarla a Tesseract.
 */
const ImagePreprocessor = {
    /**
     * Preprocesa la imagen: escala de grises → contraste → binarización (Otsu).
     * Retorna un Blob listo para Tesseract.
     */
    async preprocess(file) {
        const img = await this._loadImage(file);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Escalar a un ancho óptimo para OCR (1500-2000px)
        const targetWidth = Math.min(Math.max(img.width, 1500), 2500);
        const scale = targetWidth / img.width;
        canvas.width = targetWidth;
        canvas.height = Math.round(img.height * scale);

        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        // Obtener datos de píxeles
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;

        // Paso 1: Escala de grises
        for (let i = 0; i < data.length; i += 4) {
            const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = data[i + 1] = data[i + 2] = gray;
        }

        // Paso 2: Aumentar contraste (CLAHE simplificado)
        this._enhanceContrast(data, 1.5);

        // Paso 3: Binarización con umbral Otsu
        const threshold = this._otsuThreshold(data);
        for (let i = 0; i < data.length; i += 4) {
            const val = data[i] < threshold ? 0 : 255;
            data[i] = data[i + 1] = data[i + 2] = val;
        }

        ctx.putImageData(imageData, 0, 0);

        return new Promise(resolve => {
            canvas.toBlob(blob => resolve(blob), 'image/png');
        });
    },

    _loadImage(file) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
        });
    },

    _enhanceContrast(data, factor) {
        const intercept = 128 * (1 - factor);
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, Math.min(255, data[i] * factor + intercept));
            data[i + 1] = data[i];
            data[i + 2] = data[i];
        }
    },

    _otsuThreshold(data) {
        // Histograma
        const histogram = new Array(256).fill(0);
        for (let i = 0; i < data.length; i += 4) {
            histogram[data[i]]++;
        }
        const total = data.length / 4;

        let sum = 0;
        for (let i = 0; i < 256; i++) sum += i * histogram[i];

        let sumB = 0, wB = 0, wF = 0;
        let maxVariance = 0, threshold = 0;

        for (let t = 0; t < 256; t++) {
            wB += histogram[t];
            if (wB === 0) continue;
            wF = total - wB;
            if (wF === 0) break;

            sumB += t * histogram[t];
            const mB = sumB / wB;
            const mF = (sum - sumB) / wF;
            const variance = wB * wF * (mB - mF) * (mB - mF);

            if (variance > maxVariance) {
                maxVariance = variance;
                threshold = t;
            }
        }
        return threshold;
    }
};

function importBuilder(allExercises) {
    return {
        rows:        [{ name: '', sets: 3, reps: 15 }],
        imageUrl:    null,
        imageFile:   null,
        showZoom:    false,
        suggestions: [],
        activeRow:   -1,
        analyzing:   false,
        ocrProgress: 0,
        ocrStatus:   '',
        ocrDone:     false,
        ocrMessage:  '',
        ocrError:    null,

        loadImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.imageFile  = file;
            this.ocrDone    = false;
            this.ocrError   = null;
            this.ocrMessage = '';
            const reader = new FileReader();
            reader.onload = (e) => { this.imageUrl = e.target.result; };
            reader.readAsDataURL(file);
        },

        async runOCR() {
            if (!this.imageFile) return;
            this.analyzing   = true;
            this.ocrProgress = 0;
            this.ocrError    = null;
            this.ocrDone     = false;
            this.ocrStatus   = 'Preprocesando imagen...';

            try {
                // ── Paso 1: Preprocesar imagen ──
                const processedBlob = await ImagePreprocessor.preprocess(this.imageFile);
                this.ocrProgress = 10;
                this.ocrStatus = 'Imagen optimizada, iniciando OCR...';

                // ── Paso 2: OCR con Tesseract (PSM 6 = bloque de texto uniforme) ──
                const result = await Tesseract.recognize(
                    processedBlob,
                    'spa+eng',
                    {
                        logger: (m) => {
                            if (m.status === 'recognizing text') {
                                this.ocrProgress = 10 + Math.round(m.progress * 70);
                                this.ocrStatus = 'Reconociendo texto...';
                            } else if (m.status === 'loading tesseract core') {
                                this.ocrStatus = 'Cargando motor OCR...';
                            } else if (m.status === 'loading language traineddata') {
                                this.ocrStatus = 'Cargando idioma...';
                            }
                        },
                        tessedit_pageseg_mode: '6',
                    }
                );

                this.ocrProgress = 85;
                this.ocrStatus = 'Identificando ejercicios...';

                // ── Paso 3: Parsear texto ──
                const detected = this.parseOCRText(result.data.text);

                // ── Paso 4: Fuzzy match contra base de ejercicios ──
                const matched = this.applyFuzzyMatch(detected);

                this.ocrProgress = 100;

                if (matched.length === 0) {
                    this.ocrError = 'No se detectaron ejercicios. Asegúrate de que la foto sea nítida y el texto sea legible.';
                } else {
                    this.rows       = matched;
                    this.ocrDone    = true;
                    this.ocrMessage = matched.length + ' ejercicios detectados — revisa y corrige si hace falta.';
                }
            } catch (err) {
                this.ocrError = 'Error al procesar: ' + (err.message || 'intenta con otra foto');
            } finally {
                this.analyzing = false;
            }
        },

        /**
         * Aplica fuzzy matching a cada ejercicio detectado.
         * Si encuentra un match con score >= 0.45, reemplaza el nombre.
         */
        applyFuzzyMatch(exercises) {
            return exercises.map(ex => {
                const match = FuzzyMatcher.bestMatch(ex.name, allExercises, 0.45);
                if (match) {
                    return { ...ex, name: match.name };
                }
                return ex;
            });
        },

        parseOCRText(rawText) {
            const exercises = [];

            // Limpiar y dividir en líneas
            const lines = rawText
                .split('\n')
                .map(l => l.trim())
                .filter(l => l.length >= 3);

            // Líneas a ignorar (headers de tabla, títulos)
            const skipLine = /^(ejercicio|series?\s*y\s*rep|repeticion|músculo|muscle|día\s*\d|day\s*\d|workout|rutina|programa|series?\s*rep)/i;
            // Líneas que son solo ruido (timestamps, UI elements)
            const noiseLine = /^(\d{1,2}:\d{2}|[<>«»]|atrás|atras|back|\|\s*$)/i;

            for (const line of lines) {
                if (skipLine.test(line)) continue;
                if (noiseLine.test(line)) continue;

                let name = line;
                let sets = 3;
                let reps = 12;
                let matched = false;

                // Patrón 1: "N series x M repeticiones" / "N series: M" / "NxM" / "N×M"
                const p1 = line.match(/(\d+)\s*(?:series?)\s*[:\-x×*]\s*(\d+)/i);
                if (p1) {
                    sets    = parseInt(p1[1]);
                    reps    = parseInt(p1[2]);
                    name    = line.substring(0, line.search(/\d+\s*series?/i)).trim();
                    matched = true;
                }

                // Patrón 2: "N series" seguido de número
                if (!matched) {
                    const p2 = line.match(/(\d+)\s*series?/i);
                    if (p2) {
                        sets = parseInt(p2[1]);
                        const afterSeries = line.slice(line.search(/\d+\s*series?/i) + p2[0].length);
                        const repsN = afterSeries.match(/(\d+)/);
                        if (repsN) reps = parseInt(repsN[1]);
                        name    = line.substring(0, line.search(/\d+\s*series?/i)).trim();
                        matched = true;
                    }
                }

                // Patrón 3: Tabla con separación por espacios múltiples "Nombre    4    12"
                if (!matched) {
                    const p3 = line.match(/^(.+?)\s{2,}(\d+)\s+(\d+)\s*$/);
                    if (p3) {
                        name    = p3[1].trim();
                        sets    = parseInt(p3[2]);
                        reps    = parseInt(p3[3]);
                        matched = true;
                    }
                }

                // Patrón 4: "Nombre | 4 | 12" o "Nombre  4x12"
                if (!matched) {
                    const p4 = line.match(/^(.+?)\s*[\|│]\s*(\d+)\s*[\|│x×]\s*(\d+)/i);
                    if (p4) {
                        name    = p4[1].trim();
                        sets    = parseInt(p4[2]);
                        reps    = parseInt(p4[3]);
                        matched = true;
                    }
                }

                // Patrón 5: "Nombre 4x12" / "Nombre 4×12" al final
                if (!matched) {
                    const p5 = line.match(/^(.+?)\s+(\d+)\s*[x×]\s*(\d+)\s*$/i);
                    if (p5) {
                        name    = p5[1].trim();
                        sets    = parseInt(p5[2]);
                        reps    = parseInt(p5[3]);
                        matched = true;
                    }
                }

                // Patrón 6: "N pasos en total (X de ida y X de vuelta)" → reps especiales
                if (!matched) {
                    const p6 = line.match(/(\d+)\s*pasos?\s*(en\s*total)?/i);
                    if (p6) {
                        reps = parseInt(p6[1]);
                        sets = 1;
                        name = line.replace(/\d+\s*pasos?.*$/i, '').trim();
                        matched = true;
                    }
                }

                // Patrón 7: Dos números al final separados por espacio simple
                if (!matched) {
                    const p7 = line.match(/^(.+?)\s+(\d{1,2})\s+(\d{1,3})\s*$/);
                    if (p7 && p7[1].length >= 3) {
                        name    = p7[1].trim();
                        sets    = parseInt(p7[2]);
                        reps    = parseInt(p7[3]);
                        matched = true;
                    }
                }

                // Limpiar nombre: quitar numeración inicial ("1. ", "1) ", "- ", etc.)
                name = name.replace(/^\d+[\.\)\-\s]+/, '').trim();
                name = name.replace(/^[-–—•]\s*/, '').trim();
                // Quitar caracteres sueltos al inicio (basura OCR)
                name = name.replace(/^[^a-záéíóúñA-ZÁÉÍÓÚÑ]+/, '').trim();

                // Validar: nombre >= 3 chars, no solo números, no parece timestamp
                if (name.length >= 3 && !/^\d+$/.test(name) && !/^\d{1,2}:\d{2}/.test(name)) {
                    // Sanity check en sets/reps
                    if (sets < 1 || sets > 20) sets = 3;
                    if (reps < 1 || reps > 200) reps = 12;
                    exercises.push({ name, sets, reps });
                }
            }

            return exercises;
        },

        addRow() {
            const last = this.rows[this.rows.length - 1];
            this.rows.push({ name: '', sets: last?.sets ?? 3, reps: last?.reps ?? 15 });
        },

        removeRow(idx) {
            if (this.rows.length > 1) this.rows.splice(idx, 1);
        },

        filterSuggestions(value, rowIdx) {
            this.activeRow = rowIdx;
            const q = value.trim().toLowerCase();
            if (q.length < 2) { this.suggestions = []; return; }

            // Búsqueda mejorada: primero exacta (includes), luego fuzzy
            const exact = allExercises.filter(n => n.toLowerCase().includes(q));
            if (exact.length > 0) {
                this.suggestions = exact.slice(0, 7);
                return;
            }

            // Fuzzy fallback para cuando el usuario escribe con errores
            const fuzzyResults = allExercises
                .map(n => ({ name: n, score: FuzzyMatcher.similarity(q, n) }))
                .filter(r => r.score >= 0.4)
                .sort((a, b) => b.score - a.score)
                .slice(0, 7)
                .map(r => r.name);

            this.suggestions = fuzzyResults;
        },

        selectSuggestion(name, rowIdx) {
            this.rows[rowIdx].name = name;
            this.suggestions = [];
            this.activeRow   = -1;
        },

        hideSuggestions() {
            setTimeout(() => { this.suggestions = []; this.activeRow = -1; }, 150);
        },

        prepareSubmit(event) {
            if (this.rows.some(r => !r.name.trim())) {
                event.preventDefault();
                alert('Completa el nombre de todos los ejercicios.');
                return;
            }
            const container = document.getElementById('hidden-exercise-inputs');
            container.innerHTML = '';
            this.rows.forEach((row, i) => {
                [['name', row.name.trim()], ['sets', row.sets], ['reps', row.reps]].forEach(([f, v]) => {
                    const inp = document.createElement('input');
                    inp.type  = 'hidden';
                    inp.name  = `exercises[${i}][${f}]`;
                    inp.value = v;
                    container.appendChild(inp);
                });
            });
        },
    };
}
</script>
@endpush
