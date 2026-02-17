@extends('layouts.app')

@section('title', 'Editar usuario (empleado)')
@section('page-title', 'Editar usuario (empleado)')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        @method('PUT')
        <div>
            <label for="documento" class="block text-sm font-medium text-slate-700">Documento</label>
            <input type="text" name="documento" id="documento" value="{{ old('documento', $usuario->documento) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('documento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="nombres" class="block text-sm font-medium text-slate-700">Nombres</label>
            <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $usuario->nombres) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nombres')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Nueva contraseña (opcional)</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm" placeholder="Confirmar">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="id_empresa" class="block text-sm font-medium text-slate-700">Empresa</label>
            <select name="id_empresa" id="id_empresa" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($empresas as $e)
                    <option value="{{ $e->id_empresa }}" {{ old('id_empresa', $usuario->id_empresa) == $e->id_empresa ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
            </select>
            @error('id_empresa')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="id_rol" class="block text-sm font-medium text-slate-700">Rol</label>
            <select name="id_rol" id="id_rol" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($roles as $r)
                    <option value="{{ $r->id_rol }}" {{ old('id_rol', $usuario->id_rol) == $r->id_rol ? 'selected' : '' }}>{{ $r->nombre }}</option>
                @endforeach
            </select>
            @error('id_rol')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="id_tipo_usuario" class="block text-sm font-medium text-slate-700">Tipo usuario</label>
            <select name="id_tipo_usuario" id="id_tipo_usuario" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($tipos as $t)
                    <option value="{{ $t->id_tipo_usuario }}" {{ old('id_tipo_usuario', $usuario->id_tipo_usuario) == $t->id_tipo_usuario ? 'selected' : '' }}>{{ $t->nombre }}</option>
                @endforeach
            </select>
            @error('id_tipo_usuario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="codigo_qr" class="block text-sm font-medium text-slate-700">Codigo QR (opcional)</label>
            <input type="text" name="codigo_qr" id="codigo_qr" value="{{ old('codigo_qr', $usuario->codigo_qr) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('codigo_qr')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="activo" class="ml-2 text-sm text-slate-700">Activo</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Actualizar</button>
            <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
