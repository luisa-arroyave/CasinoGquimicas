<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
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
        body{font-family:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,'Segoe UI',Roboto,sans-serif}
        /* Campos de texto cuadrados y un poco más grandes */
        input:not([type=checkbox]):not([type=radio]), select, textarea {
            border-radius: 0.25rem !important;
            min-height: 3rem !important;
            padding: 0.625rem 1rem !important;
            font-size: 1rem !important;
        }
        textarea { min-height: 5rem !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen font-sans overflow-x-hidden">
    <div class="flex min-h-screen min-w-0">
        {{-- Overlay móvil: cierra sidebar al tocar fuera --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-200 lg:!hidden" aria-hidden="true" onclick="var s=document.getElementById('sidebar'); var o=this; s.classList.add('-translate-x-full'); o.classList.add('opacity-0','pointer-events-none'); o.classList.remove('opacity-100','pointer-events-auto');"></div>

        {{-- Sidebar: colapsable en móvil, fijo en lg --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 max-w-[85vw] sm:max-w-none bg-slate-900 text-white shadow-xl transform transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0">
            <div class="flex flex-col h-full">
                <div class="flex items-center h-16 px-5 border-b border-slate-700/80">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-white">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        {{ config('app.name') }}
                    </a>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Dashboard</span>
                    </a>

                    @role('administrador')
                    <div class="space-y-0.5">
                        <p class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold text-slate-400 uppercase tracking-wider cursor-default">Administración</p>
                        <a href="{{ route('admin.empresas.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.empresas.*') ? 'bg-slate-800 text-white' : '' }}">Empresas</a>
                        <a href="{{ route('admin.casinos.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.casinos.*') ? 'bg-slate-800 text-white' : '' }}">Casinos</a>
                        <a href="{{ route('admin.horarios.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.horarios.*') ? 'bg-slate-800 text-white' : '' }}">Horarios</a>
                        <a href="{{ route('admin.visitantes.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.visitantes.*') ? 'bg-slate-800 text-white' : '' }}">Visitantes</a>
                        <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.usuarios.*') ? 'bg-slate-800 text-white' : '' }}">Usuarios</a>
                        <a href="{{ route('admin.consumos-manuales.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.consumos-manuales.*') ? 'bg-slate-800 text-white' : '' }}">Registro manual consumos</a>
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-slate-800 text-white' : '' }}">Roles</a>
                        <a href="{{ route('admin.tipos-usuario.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.tipos-usuario.*') ? 'bg-slate-800 text-white' : '' }}">Tipos de usuario</a>
                        <a href="{{ route('admin.sedes.index') }}" class="flex items-center gap-3 pl-4 pr-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('admin.sedes.*') ? 'bg-slate-800 text-white' : '' }}">Sedes</a>
                    </div>
                    @endrole

                    @role('empleado')
                    <a href="{{ route('solicitar-consumo.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('solicitar-consumo.*') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Solicitar consumo</span>
                    </a>
                    @endrole

                    @role('gestionhumana')
                    <a href="{{ route('gestion-humana.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('gestion-humana.*') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Reportes consumos</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Empleados</span>
                    </a>
                    @endrole

                    @role('casino')
                    <a href="{{ route('casino.panel') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('casino.panel') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Panel Casino</span>
                    </a>
                    <a href="{{ route('casino.escaneo-qr') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('casino.escaneo-qr') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Registro Manual</span>
                    </a>
                    <a href="{{ route('casino.cuenta-cobro.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('casino.cuenta-cobro.*') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2M9 5h6M9 5v2m0 4v6m0-6h6m-6 6h6m-6 6v-6"/></svg>
                        <span>Cuenta de cobro</span>
                    </a>
                    @endrole

                    @role('operativo')
                    <a href="{{ route('operativo.pedidos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('operativo.*') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Pedidos a domicilio</span>
                    </a>
                    <a href="{{ route('casino.panel') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('casino.panel') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Panel Casino</span>
                    </a>
                    <a href="{{ route('casino.escaneo-qr') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('casino.escaneo-qr') ? 'bg-slate-800 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Registro Manual</span>
                    </a>
                    @endrole
                </nav>
                <div class="p-4 border-t border-slate-700/80">
                    <p class="text-xs text-slate-500">Sistema empresarial</p>
                </div>
            </div>
        </aside>

        <div class="flex flex-1 flex-col min-w-0 lg:pl-64">
            {{-- Navbar --}}
            <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6">
                <button type="button" class="lg:hidden min-h-[44px] min-w-[44px] flex items-center justify-center p-2.5 rounded-lg text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition-colors touch-manipulation" aria-label="Abrir menú" onclick="var s=document.getElementById('sidebar'); var o=document.getElementById('sidebar-overlay'); s.classList.toggle('-translate-x-full'); if(s.classList.contains('-translate-x-full')){ o.classList.add('opacity-0','pointer-events-none'); o.classList.remove('opacity-100','pointer-events-auto'); } else { o.classList.remove('opacity-0','pointer-events-none'); o.classList.add('opacity-100','pointer-events-auto'); }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg font-semibold text-slate-800 truncate">@yield('page-title', 'Panel')</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="hidden sm:inline text-sm text-slate-600 truncate max-w-[120px]">{{ auth()->user()->name ?? '' }}</span>
                    @php $roleLabel = config('roles.labels.'.(auth()->user()->role ?? 'empleado'), ucfirst(auth()->user()->role ?? 'empleado')); @endphp
                    <span class="hidden sm:inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">{{ $roleLabel }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 min-h-[44px] px-3 py-2.5 sm:py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors touch-manipulation">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="hidden sm:inline">Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 min-w-0 overflow-x-hidden">
                @if (session('success'))
                    <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
                @endif
                @if (session('error'))
                    <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    {{-- Tablas como tarjetas en móvil: añadir clase table-cards-mobile al <table> y data-label a cada <td> --}}
    <style>
        @media (max-width: 767px) {
            .table-cards-mobile thead { display: none; }
            .table-cards-mobile tbody tr { display: block; border-bottom: 1px solid #e2e8f0; padding: 1rem; }
            .table-cards-mobile tbody tr:hover { background: #f8fafc; }
            .table-cards-mobile tbody td { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.5rem 0; border: none; }
            .table-cards-mobile tbody td::before { content: attr(data-label); font-weight: 600; font-size: 0.75rem; color: #64748b; min-width: 7rem; }
            .table-cards-mobile tbody td:last-child { flex-wrap: wrap; justify-content: flex-end; }
        }
    </style>
</body>
</html>
