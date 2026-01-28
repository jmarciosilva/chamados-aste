@extends('layouts.admin')

@section('title', 'Editar Departamento')
@section('subtitle', 'Atualização das informações do departamento')

@section('content')

<div class="max-w-xl">

    <form method="POST"
          action="{{ route('admin.departments.update', $department) }}"
          class="bg-white p-6 rounded-lg shadow space-y-4">

        @csrf
        @method('PUT')

        <!-- Nome -->
        <div>
            <label class="text-sm text-slate-600">Nome do Departamento</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $department->name) }}"
                   required
                   class="mt-1 w-full rounded border-slate-300 text-sm">
        </div>

        <!-- Status -->
        <div class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   {{ $department->is_active ? 'checked' : '' }}>
            <span class="text-sm text-slate-600">Departamento ativo</span>
        </div>

        <!-- Ações -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.departments.index') }}"
               class="px-4 py-2 border rounded text-sm hover:bg-slate-100">
                Cancelar
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                Atualizar
            </button>
        </div>

    </form>

</div>

@endsection
