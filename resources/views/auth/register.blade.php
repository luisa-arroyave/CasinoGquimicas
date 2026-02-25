<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrarse - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { theme: { extend: { fontFamily: { sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } } };
        </script>
    @endif
    <style>
        body{font-family:'Instrument Sans',ui-sans-serif,system-ui,sans-serif}
        input:not([type=checkbox]):not([type=radio]), select, textarea {
            border-radius: 0.25rem !important;
            min-height: 3rem !important;
            padding: 0.625rem 1rem !important;
            font-size: 1rem !important;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center py-6 px-4 sm:py-12 sm:px-6 font-sans overflow-x-hidden">
    <div class="w-full max-w-md min-w-0">
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ config('app.name') }}</h1>
            <p class="mt-1 text-sm sm:text-base text-slate-600">Sistema empresarial</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-5 sm:p-8">
            <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-5 sm:mb-6">Crear cuenta</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4 sm:space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 sm:py-2.5 text-base text-slate-900 min-h-[48px] focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                </div>
                <div>
                    <label for="documento" class="block text-sm font-medium text-slate-700 mb-1">Número de documento</label>
                    <input type="text" name="documento" id="documento" value="{{ old('documento') }}" required autocomplete="username"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 sm:py-2.5 text-base text-slate-900 min-h-[48px] focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20" placeholder="Ej: 12345678">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 sm:py-2.5 text-base text-slate-900 min-h-[48px] focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                    <p class="mt-1 text-xs text-slate-500">Mínimo 8 caracteres</p>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 sm:py-2.5 text-base text-slate-900 min-h-[48px] focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                </div>
                <button type="submit" class="w-full flex justify-center items-center min-h-[48px] py-3 px-4 rounded-lg text-base font-medium text-white bg-slate-800 hover:bg-slate-700 active:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors touch-manipulation">
                    Registrarse
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="font-medium text-slate-800 hover:text-slate-600 underline">Iniciar sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
