@extends('layouts.app')

@section('title', 'Editar usuario del sistema')
@section('page-title', 'Editar usuario del sistema')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Nueva contraseña (dejar vacío para no cambiar)</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm" placeholder="Confirmar">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700">Rol</label>
            <select name="role" id="role" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="empleado" {{ old('role', $user->role) == 'empleado' ? 'selected' : '' }}>Empleado</option>
                <option value="casino" {{ old('role', $user->role) == 'casino' ? 'selected' : '' }}>Casino</option>
                <option value="operativo" {{ old('role', $user->role) == 'operativo' ? 'selected' : '' }}>Operativo</option>
                <option value="gestionhumana" {{ old('role', $user->role) == 'gestionhumana' ? 'selected' : '' }}>Gestión Humana</option>
                <option value="administrador" {{ old('role', $user->role) == 'administrador' ? 'selected' : '' }}>Administrador</option>
            </select>
            @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @php $userEmpresaIds = old('empresas', $user->empresas->pluck('id_empresa')->toArray()); @endphp
        <div>
            <p class="block text-sm font-medium text-slate-700 mb-2">Empresas (acceso a informes e ingresar empleados)</p>
            <p class="text-xs text-slate-500 mb-2">Seleccione las empresas a las que este usuario puede acceder. Puede elegir varias.</p>
            <div class="space-y-2 max-h-48 overflow-y-auto rounded-lg border border-slate-200 p-3 bg-slate-50">
                @foreach($empresas as $e)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="empresas[]" value="{{ $e->id_empresa }}" {{ in_array($e->id_empresa, $userEmpresaIds) ? 'checked' : '' }} class="rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                    <span class="text-sm text-slate-800">{{ $e->nombre }}</span>
                </label>
                @endforeach
            </div>
            @error('empresas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Actualizar</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
