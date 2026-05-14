@extends('layouts.app')

@section('title', $exercise->name . ' · POWER STACK')

@section('content')
<div class="space-y-8">

    {{-- Breadcrumbs / Back --}}
    <a href="{{ route('wiki.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#616161] hover:text-primary transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        VOLVER A LA BIBLIOTECA
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        
        {{-- Multimedia --}}
        <div>
            @if($exercise->video_url)
                @php
                    preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $exercise->video_url, $match);
                    $youtube_id = $match[1] ?? null;
                @endphp
                @if($youtube_id)
                    <div class="aspect-video w-full rounded-2xl overflow-hidden shadow-xl bg-black">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtube_id }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="aspect-video w-full rounded-2xl bg-gray-100 flex items-center justify-center">
                        <i class="fa-solid fa-video-slash text-4xl text-gray-300"></i>
                    </div>
                @endif
            @else
                <div class="aspect-video w-full rounded-2xl bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-image text-4xl text-gray-300"></i>
                </div>
            @endif

            <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="card p-4 text-center">
                    <p class="text-[10px] font-bold text-[#616161] tracking-widest mb-1">GRUPO MUSCULAR</p>
                    <p class="font-black text-[#121212]">{{ strtoupper($exercise->muscle_group) }}</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-[10px] font-bold text-[#616161] tracking-widest mb-1">EQUIPAMIENTO</p>
                    <p class="font-black text-[#121212]">{{ strtoupper($exercise->equipment) }}</p>
                </div>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="space-y-6">
            <div>
                <h1 class="text-4xl font-black text-[#121212] leading-tight mb-2">{{ $exercise->name }}</h1>
                <p class="text-[#616161] leading-relaxed">{{ $exercise->description }}</p>
            </div>

            <div class="pt-6 border-t border-gray-100">
                <h3 class="text-xs font-bold text-primary tracking-widest mb-4">INSTRUCCIONES PASO A PASO</h3>
                <div class="space-y-4">
                    @php
                        $instructions = is_string($exercise->instructions) ? json_decode($exercise->instructions, true) : $exercise->instructions;
                    @endphp
                    @if($instructions && is_array($instructions))
                        @foreach($instructions as $index => $step)
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 text-primary font-black text-sm">
                                {{ $index + 1 }}
                            </div>
                            <p class="text-sm text-[#121212] pt-1.5">{{ $step }}</p>
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-[#616161] italic">No hay instrucciones detalladas disponibles.</p>
                    @endif
                </div>
            </div>

            @if($exercise->video_url)
            <div class="pt-6">
                <a href="{{ $exercise->video_url }}" target="_blank" class="btn-ghost w-full flex items-center justify-center gap-2">
                    <i class="fa-brands fa-youtube text-red-500"></i>
                    VER EN YOUTUBE
                </a>
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
