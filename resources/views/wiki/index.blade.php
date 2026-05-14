@extends('layouts.app')

@section('title', 'Wiki de Ejercicios · POWER STACK')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold tracking-widest mb-1 text-primary">BIBLIOTECA</p>
            <h1 class="text-3xl font-black text-[#121212]">Wiki de <span class="text-primary">Ejercicios</span></h1>
        </div>

        <form action="{{ route('wiki.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Buscar ejercicio..." 
                   class="form-input md:w-64">
            <button type="submit" class="btn-primary flex items-center justify-center w-12 px-0">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('wiki.index') }}" 
           class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ !request('muscle') ? 'bg-primary text-[#121212]' : 'bg-white text-[#616161] border border-gray-100 hover:bg-gray-50' }}">
            TODOS
        </a>
        @foreach($muscles as $muscle)
        <a href="{{ route('wiki.index', ['muscle' => $muscle, 'search' => request('search')]) }}" 
           class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ request('muscle') == $muscle ? 'bg-primary text-[#121212]' : 'bg-white text-[#616161] border border-gray-100 hover:bg-gray-50' }}">
            {{ strtoupper($muscle) }}
        </a>
        @endforeach
    </div>

    {{-- Exercise Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exercises as $exercise)
        <a href="{{ route('wiki.show', $exercise) }}" class="card group hover:shadow-lg transition-all p-4">
            <div class="w-full aspect-video bg-gray-100 rounded-xl mb-4 flex items-center justify-center overflow-hidden">
                @if($exercise->video_url)
                    @php
                        // Simple youtube thumbnail extractor
                        preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $exercise->video_url, $match);
                        $youtube_id = $match[1] ?? null;
                    @endphp
                    @if($youtube_id)
                        <img src="https://img.youtube.com/vi/{{ $youtube_id }}/mqdefault.jpg" alt="{{ $exercise->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    @else
                        <i class="fa-solid fa-dumbbell text-3xl text-gray-300"></i>
                    @endif
                @else
                    <i class="fa-solid fa-dumbbell text-3xl text-gray-300"></i>
                @endif
            </div>
            <p class="text-xs font-bold text-primary tracking-widest mb-1">{{ strtoupper($exercise->muscle_group) }}</p>
            <h3 class="text-lg font-black text-[#121212] leading-tight mb-2">{{ $exercise->name }}</h3>
            <div class="flex items-center gap-4 text-[10px] text-[#616161] font-bold">
                <span class="flex items-center gap-1">
                    <i class="fa-solid fa-screwdriver-wrench text-gray-400"></i>
                    {{ strtoupper($exercise->equipment) }}
                </span>
            </div>
        </a>
        @empty
        <div class="col-span-full py-12 text-center">
            <p class="text-gray-400 italic">No se encontraron ejercicios con esos filtros.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $exercises->appends(request()->all())->links() }}
    </div>

</div>
@endsection
