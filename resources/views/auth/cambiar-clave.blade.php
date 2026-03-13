<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cambiar contraseña - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: { extend: { fontFamily: { sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } }
            };
        </script>
    @endif
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Segoe UI', Roboto, sans-serif; }
        input:not([type=checkbox]):not([type=radio]), select, textarea {
            border-radius: 0.25rem !important;
            min-height: 3rem !important;
            padding: 0.625rem 1rem !important;
            font-size: 1rem !important;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center py-6 px-4 sm:py-12 sm:px-6 overflow-x-hidden antialiased">
    <div class="w-full max-w-md min-w-0">
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-800">{{ config('app.name') }}</h1>
            <p class="mt-2 text-sm sm:text-base text-slate-500">Debe cambiar su contraseña</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/80 p-6 sm:p-8">
            <h2 class="text-lg sm:text-xl font-semibold text-slate-800 mb-2">Cambiar contraseña</h2>
            <p class="text-sm text-slate-600 mb-6">Por seguridad, debe establecer una nueva contraseña antes de continuar.</p>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('cambiar-clave.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="password_actual" class="block text-sm font-medium text-slate-700 mb-2">Contraseña actual</label>
                    <input type="password" name="password_actual" id="password_actual" required autofocus
                           placeholder="Su contraseña actual (el documento)"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/25 min-h-[48px]">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Nueva contraseña</label>
                    <input type="password" name="password" id="password" required minlength="6"
                           placeholder="Mínimo 6 caracteres"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/25 min-h-[48px]">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                           placeholder="Repita la nueva contraseña"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/25 min-h-[48px]">
                </div>
                <button type="submit" class="w-full flex justify-center items-center min-h-[48px] py-3 px-4 rounded-xl text-base font-semibold text-white bg-slate-800 shadow-sm hover:bg-slate-700 active:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition touch-manipulation">
                    Cambiar contraseña
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-sm text-slate-500 hover:text-slate-700">Cerrar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
