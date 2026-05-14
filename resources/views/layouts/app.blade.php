<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POWER STACK')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#A1CD35',
                        secondary: '#2D9CDB',
                        alert:     '#F2994A',
                        danger:    '#D32F2F',
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.15s ease;
            color: #616161;
        }
        .nav-link:hover  { background-color: rgba(161, 205, 53, 0.1); color: #121212; }
        .nav-link.active { background-color: #A1CD35; color: #121212; }
        .nav-link .icon  { width: 18px; text-align: center; }

        .card { background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }

        dialog::backdrop { background: rgba(0,0,0,0.5); }

        .btn-primary {
            background-color: #A1CD35;
            color: #121212;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: opacity 0.15s;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover { opacity: 0.88; }

        .btn-ghost {
            background: transparent;
            color: #616161;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.15s;
        }
        .btn-ghost:hover { background: #f9fafb; }

        .form-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            color: #121212;
            background: #fff;
            transition: border-color 0.15s;
            outline: none;
        }
        .form-input:focus { border-color: #A1CD35; }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #616161;
            letter-spacing: 1px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
    </style>

    @stack('head')
</head>
<body class="bg-[#F8F9FA]" x-data="{ sidebarOpen: false }">

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-screen w-64 bg-white border-r border-gray-100 z-30 flex flex-col
                  -translate-x-full lg:translate-x-0 transition-transform duration-200"
           :class="{ 'translate-x-0': sidebarOpen }">

        {{-- Logo --}}
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color:#A1CD35;">
                    <i class="fa-solid fa-bolt text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 tracking-widest leading-none">PODER</p>
                    <p class="text-xl font-black text-[#121212] tracking-wider leading-tight">STACK</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house icon"></i>
                Dashboard
            </a>
            <a href="{{ route('weight.index') }}"
               class="nav-link {{ request()->routeIs('weight.*') ? 'active' : '' }}">
                <i class="fa-solid fa-scale-balanced icon"></i>
                Control de Peso
            </a>
            <a href="{{ route('routines.index') }}"
               class="nav-link {{ request()->routeIs('routines.*') ? 'active' : '' }}">
                <i class="fa-solid fa-dumbbell icon"></i>
                Rutinas
            </a>
            <a href="{{ route('goals.index') }}"
               class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                <i class="fa-solid fa-trophy icon"></i>
                Mis Metas
            </a>
            <a href="{{ route('strength.index') }}"
               class="nav-link {{ request()->routeIs('strength.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line icon"></i>
                Progreso Fuerza
            </a>
            <a href="{{ route('stats.index') }}"
               class="nav-link {{ request()->routeIs('stats.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie icon"></i>
                Estadísticas
            </a>
            <a href="{{ route('wiki.index') }}"
               class="nav-link {{ request()->routeIs('wiki.*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-bookmark icon"></i>
                Wiki Ejercicios
            </a>
        </nav>

        {{-- User --}}
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                     style="background-color:#A1CD35; color:#121212;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#121212] truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-[#616161] truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-all">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Top bar mobile --}}
        <header class="lg:hidden bg-white border-b border-gray-100 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
            <button @click="sidebarOpen = true" class="p-2 rounded-lg hover:bg-gray-100 text-[#121212]">
                <i class="fa-solid fa-bars"></i>
            </button>
            <span class="font-black text-[#121212] tracking-widest text-sm">POWER STACK</span>
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                 style="background-color:#A1CD35; color:#121212;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </header>

        {{-- Flash success --}}
        @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="fixed top-4 right-4 z-50 bg-white border-l-4 rounded-xl shadow-lg p-4 flex items-center gap-3 max-w-sm"
             style="border-color:#A1CD35;">
            <i class="fa-solid fa-circle-check" style="color:#A1CD35;"></i>
            <p class="text-sm font-medium text-[#121212] flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        @endif

        {{-- Flash errors --}}
        @if(session('error') || $errors->any())
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-cloak
             class="fixed top-4 right-4 z-50 bg-white border-l-4 border-red-500 rounded-xl shadow-lg p-4 flex items-center gap-3 max-w-sm">
            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            <p class="text-sm font-medium text-[#121212] flex-1">
                {{ session('error') ?? $errors->first() }}
            </p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 p-5 lg:p-8 max-w-6xl w-full mx-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
