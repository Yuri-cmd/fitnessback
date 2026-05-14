<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta · POWER STACK</title>
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
<body class="bg-[#F8F9FA] min-h-screen flex items-center justify-center p-4" x-data="{ showPass: false, showConfirm: false }">

<div class="w-full max-w-4xl flex bg-white rounded-3xl shadow-xl overflow-hidden min-h-[620px]">

    {{-- Panel izquierdo --}}
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
            <p class="text-xs font-bold tracking-widest mb-3" style="color:#A1CD35;">EMPIEZA HOY</p>
            <h2 class="text-3xl font-black text-white leading-tight mb-4">
                Únete a miles de atletas que ya mejoran cada día.
            </h2>
            <ul class="space-y-3">
                <li class="flex items-center gap-2 text-sm text-gray-300">
                    <i class="fa-solid fa-check-circle" style="color:#A1CD35;"></i>
                    Control de peso e IMC
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-300">
                    <i class="fa-solid fa-check-circle" style="color:#A1CD35;"></i>
                    Rutinas personalizadas
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-300">
                    <i class="fa-solid fa-check-circle" style="color:#A1CD35;"></i>
                    Seguimiento de metas
                </li>
            </ul>
        </div>

        <p class="text-xs text-gray-500">Totalmente gratis · Sin tarjeta de crédito</p>
    </div>

    {{-- Formulario --}}
    <div class="flex-1 flex flex-col justify-center p-8 lg:p-12">
        <div class="mb-7">
            <div class="flex items-center gap-2 mb-6 md:hidden">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color:#A1CD35;">
                    <i class="fa-solid fa-bolt text-white text-xs"></i>
                </div>
                <span class="font-black text-[#121212] tracking-wider">POWER STACK</span>
            </div>

            <p class="text-xs font-bold tracking-widest mb-1" style="color:#A1CD35;">REGISTRO</p>
            <h1 class="text-2xl font-black text-[#121212]">Crea tu cuenta</h1>
            <p class="text-sm text-[#616161] mt-1">Solo toma un minuto</p>
        </div>

        <form action="/register" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">NOMBRE</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-input @error('name') border-red-400 @enderror"
                       placeholder="Tu nombre completo" required autofocus>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">CORREO</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-input @error('email') border-red-400 @enderror"
                       placeholder="tu@correo.com" required>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">CONTRASEÑA</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password"
                           class="form-input @error('password') border-red-400 @enderror pr-12"
                           placeholder="Mínimo 8 caracteres" required>
                    <button type="button" @click="showPass = !showPass"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#A1CD35] transition-colors">
                        <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-[#616161] tracking-wider mb-2">CONFIRMAR CONTRASEÑA</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                           class="form-input pr-12"
                           placeholder="Repite la contraseña" required>
                    <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#A1CD35] transition-colors">
                        <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-xl font-bold text-sm tracking-wide transition-opacity hover:opacity-90 mt-2"
                    style="background-color:#A1CD35; color:#121212;">
                CREAR CUENTA
            </button>
        </form>

        <p class="text-center text-sm text-[#616161] mt-6">
            ¿Ya tienes cuenta?
            <a href="/login" class="font-bold hover:underline" style="color:#A1CD35;">Inicia sesión</a>
        </p>
    </div>
</div>

</body>
</html>
