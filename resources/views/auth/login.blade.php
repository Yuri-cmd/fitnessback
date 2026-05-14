<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · POWER STACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        .form-input {
            width: 100%; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 13px 16px; font-size: 14px; color: #121212;
            background: #fff; outline: none; transition: border-color 0.15s;
        }
        .form-input:focus { border-color: #A1CD35; box-shadow: 0 0 0 3px rgba(161,205,53,0.12); }
    </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen flex items-center justify-center p-4" x-data="{ showPass: false }">

<div class="w-full max-w-4xl flex bg-white rounded-3xl shadow-xl overflow-hidden min-h-[560px]">

    {{-- Panel izquierdo - Branding --}}
    <div class="hidden md:flex flex-col justify-between w-5/12 p-10"
         style="background: linear-gradient(135deg, #121212 0%, #1e1e1e 100%);">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color:#A1CD35;">
                <i class="fa-solid fa-bolt text-white text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-widest" style="color:#A1CD35;">PODER</p>
                <p class="text-xl font-black text-white tracking-wider leading-tight">STACK</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-bold tracking-widest mb-3" style="color:#A1CD35;">TU APP DE FITNESS</p>
            <h2 class="text-3xl font-black text-white leading-tight mb-4">
                Transforma tu<br>cuerpo, un día<br>a la vez.
            </h2>
            <p class="text-gray-400 text-sm leading-relaxed">
                Registra tu peso, crea rutinas de entrenamiento y alcanza tus metas fitness.
            </p>
        </div>

        <div class="flex gap-6">
            <div>
                <p class="text-2xl font-black text-white">IMC</p>
                <p class="text-xs text-gray-400">Seguimiento</p>
            </div>
            <div>
                <p class="text-2xl font-black text-white">7/7</p>
                <p class="text-xs text-gray-400">Días activo</p>
            </div>
            <div>
                <p class="text-2xl font-black" style="color:#A1CD35;">∞</p>
                <p class="text-xs text-gray-400">Rutinas</p>
            </div>
        </div>
    </div>

    {{-- Panel derecho - Formulario --}}
    <div class="flex-1 flex flex-col justify-center p-8 lg:p-12">
        <div class="mb-8">
            {{-- Logo mobile --}}
            <div class="flex items-center gap-2 mb-6 md:hidden">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color:#A1CD35;">
                    <i class="fa-solid fa-bolt text-white text-xs"></i>
                </div>
                <span class="font-black text-[#121212] tracking-wider">POWER STACK</span>
            </div>

            <p class="text-xs font-bold tracking-widest mb-1" style="color:#A1CD35;">BIENVENIDO</p>
            <h1 class="text-2xl font-black text-[#121212]">Inicia sesión</h1>
            <p class="text-sm text-[#616161] mt-1">Ingresa tus credenciales para continuar</p>
        </div>

        <form action="/login" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">CORREO</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-input @error('email') border-red-400 @enderror"
                       placeholder="atleta@ejemplo.com" required autofocus>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">CONTRASEÑA</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password"
                           class="form-input pr-12"
                           placeholder="••••••••" required>
                    <button type="button" @click="showPass = !showPass"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#A1CD35] transition-colors">
                        <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember"
                       class="rounded" style="accent-color:#A1CD35;">
                <label for="remember" class="text-sm text-[#616161]">Recordarme</label>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-xl font-bold text-sm tracking-wide transition-opacity hover:opacity-90"
                    style="background-color:#A1CD35; color:#121212;">
                ENTRAR
            </button>
        </form>

        <p class="text-center text-sm text-[#616161] mt-6">
            ¿Sin cuenta?
            <a href="/register" class="font-bold hover:underline" style="color:#A1CD35;">Regístrate gratis</a>
        </p>
    </div>
</div>

</body>
</html>
