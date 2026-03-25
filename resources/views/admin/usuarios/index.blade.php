@extends('layouts.app')

@section('title', 'Usuarios (empleados)')
@section('page-title', 'Usuarios (empleados)')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Empleados / usuarios empresariales</h2>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.usuarios.plantilla-importar') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Descargar plantilla (listado usuarios)</a>
        <a href="{{ route('admin.usuarios.plantilla-ejemplo-importar') }}" class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50">Ejemplo importar usuarios</a>
        <button type="button" onclick="document.getElementById('form-importar').classList.toggle('hidden')" class="inline-flex items-center px-4 py-2 border border-emerald-600 text-emerald-700 rounded-lg hover:bg-emerald-50">Importar Excel</button>
        <a href="{{ route('admin.usuarios.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nuevo usuario</a>
    </div>
</div>
<div id="form-importar" class="mb-4 p-4 rounded-xl border border-slate-200 bg-white {{ $errors->has('archivo') ? '' : 'hidden' }}">
    <form action="{{ route('admin.usuarios.importar') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
        @csrf
        <div>
            <label for="archivo" class="block text-sm font-medium text-slate-700 mb-1">Archivo Excel (.xlsx) o CSV (.csv)</label>
            <input type="file" name="archivo" id="archivo" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            @error('archivo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Importar</button>
    </form>
    <p class="text-xs text-slate-500 mt-2">Para <strong>importar usuarios nuevos</strong> use el archivo <strong>Ejemplo importar usuarios</strong> (columnas empresa, rol, tipo por nombre). La contraseña inicial será el documento de cada usuario. Si Zip no está habilitado en PHP, guarde el Excel como CSV y súbalo.</p>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Documento o nombre" class="rounded-lg border-slate-300 shadow-sm">
    <select name="empresa" class="rounded-lg border-slate-300">
        <option value="">Todas las empresas</option>
        @foreach($empresas as $e)
            <option value="{{ $e->id_empresa }}" {{ request('empresa') == $e->id_empresa ? 'selected' : '' }}>{{ $e->nombre }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-slate-200 rounded-lg hover:bg-slate-300">Filtrar</button>
</form>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Documento</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nombres</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Empresa</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Rol</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Activo</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($usuarios as $u)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $u->documento }}</td>
                <td class="px-4 py-3">{{ $u->nombres }}</td>
                <td class="px-4 py-3">{{ $u->empresa->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $u->rol->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $u->activo ? 'Si' : 'No' }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.usuarios.edit', $u) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    <form action="{{ route('admin.usuarios.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay usuarios.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $usuarios->links() }}</div>
@endsection
