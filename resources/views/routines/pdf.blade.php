<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 20px; }
    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 11px;
        color: #333;
        margin: 0;
        padding: 20px;
    }
    
    /* Utilidades de tabla para layout (Reemplazo de Flexbox) */
    .table-layout { width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px; }
    .table-layout td { vertical-align: top; border: none; padding: 0; }

    /* ── Header ── */
    .header-table { border-bottom: 3px solid #A1CD35; padding-bottom: 10px; margin-bottom: 25px; }
    .brand-title { font-size: 24px; font-weight: bold; color: #121212; }
    .brand-subtitle { font-size: 10px; color: #666; letter-spacing: 2px; }
    .header-meta { text-align: right; font-size: 10px; color: #666; }
    .header-meta strong { color: #000; }

    /* ── Resumen ── */
    .summary-table { margin-bottom: 30px; }
    .summary-box { 
        border: 1px solid #ddd; 
        border-radius: 8px; 
        padding: 10px; 
        text-align: center; 
        width: 24%;
    }
    .summary-val { font-size: 18px; font-weight: bold; color: #121212; display: block; }
    .summary-label { font-size: 8px; color: #999; text-transform: uppercase; margin-top: 3px; }

    /* ── Bloque de Rutina ── */
    .routine-container { margin-bottom: 40px; page-break-inside: avoid; }
    
    .routine-title-bar { 
        background: #121212; 
        color: #fff; 
        padding: 10px 15px; 
        border-radius: 8px 8px 0 0;
    }
    .routine-name { font-size: 16px; font-weight: bold; }
    .routine-stats { font-size: 10px; color: #ccc; float: right; margin-top: 4px; }

    /* ── Tabla de Ejercicios ── */
    .exercise-table { 
        width: 100%; 
        border-collapse: collapse; 
        border: 1px solid #ddd; 
        border-top: none;
    }
    .exercise-table th { 
        background: #f8f9fa; 
        color: #666; 
        font-size: 9px; 
        text-transform: uppercase; 
        padding: 8px; 
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .exercise-table td { 
        padding: 10px 8px; 
        border-bottom: 1px solid #eee; 
        vertical-align: middle;
    }
    .exercise-table tr:last-child td { border-bottom: none; }
    
    .ex-info { font-weight: bold; font-size: 12px; }
    .ex-muscle { font-size: 9px; color: #888; display: block; margin-top: 2px; }
    .text-center { text-align: center; }
    
    /* Cajas para anotar */
    .log-container { text-align: center; }
    .log-box { 
        display: inline-block; 
        width: 25px; 
        height: 20px; 
        border: 1px solid #ccc; 
        border-radius: 3px; 
        margin: 1px;
        line-height: 20px;
        font-size: 8px;
        color: #bbb;
    }

    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        font-size: 9px;
        color: #aaa;
        text-align: center;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }
</style>
</head>
<body>

<table class="table-layout header-table">
    <tr>
        <td>
            <div class="brand-title">POWER STACK</div>
            <div class="brand-subtitle">FITNESS TRACKER</div>
        </td>
        <td class="header-meta">
            Atleta: <strong>{{ $user->name }}</strong><br>
            Fecha: <strong>{{ now()->format('d/m/Y') }}</strong><br>
            Plan: <strong>Rutinas de Entrenamiento</strong>
        </td>
    </tr>
</table>

<table class="table-layout summary-table">
    <tr>
        <td class="summary-box" style="border-left: 4px solid #A1CD35;">
            <span class="summary-val">{{ $routines->count() }}</span>
            <span class="summary-label">Rutinas</span>
        </td>
        <td style="width: 1.33%;"></td>
        <td class="summary-box">
            <span class="summary-val">{{ $routines->sum(fn($r) => $r->exercises->count()) }}</span>
            <span class="summary-label">Ejercicios</span>
        </td>
        <td style="width: 1.33%;"></td>
        <td class="summary-box">
            <span class="summary-val">{{ $routines->sum(fn($r) => $r->exercises->sum('pivot.sets')) }}</span>
            <span class="summary-label">Series</span>
        </td>
        <td style="width: 1.33%;"></td>
        <td class="summary-box">
            <span class="summary-val">{{ $routines->sum(fn($r) => $r->exercises->sum(fn($e) => $e->pivot->sets * $e->pivot->reps)) }}</span>
            <span class="summary-label">Reps Totales</span>
        </td>
    </tr>
</table>

@forelse($routines as $index => $routine)
    <div class="routine-container">
        <div class="routine-title-bar">
            <span class="routine-stats">
                {{ $routine->exercises->count() }} ejercicios | {{ $routine->exercises->sum('pivot.sets') }} series totales
            </span>
            <span class="routine-name">{{ strtoupper($routine->name) }}</span>
        </div>
        
        <table class="exercise-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Ejercicio</th>
                    <th style="width: 10%; text-align: center;">Series</th>
                    <th style="width: 10%; text-align: center;">Reps</th>
                    <th style="width: 40%; text-align: center;">Seguimiento (Peso kg)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($routine->exercises as $ex)
                <tr>
                    <td>
                        <span class="ex-info">{{ $ex->name }}</span>
                        <span class="ex-muscle">{{ $ex->muscle_group }}</span>
                    </td>
                    <td class="text-center" style="font-size: 14px; font-weight: bold;">{{ $ex->pivot->sets }}</td>
                    <td class="text-center" style="font-size: 14px; color: #666;">x {{ $ex->pivot->reps }}</td>
                    <td>
                        <div class="log-container">
                            @for($s = 1; $s <= $ex->pivot->sets; $s++)
                                <div class="log-box">S{{ $s }}</div>
                            @endfor
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{-- Salto de página opcional si la rutina es muy larga o después de cada rutina --}}
    {{-- @if(!$loop->last) <div style="page-break-after: always;"></div> @endif --}}
@empty
    <div style="text-align: center; padding: 50px; color: #999; border: 1px dashed #ccc; border-radius: 10px;">
        No hay rutinas registradas para mostrar.
    </div>
@endforelse

<div class="footer">
    POWER STACK · Generado automáticamente para {{ $user->name }} · Página 1
</div>

</body>
</html>
