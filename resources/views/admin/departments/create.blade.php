@extends('layouts.admin')

@section('title', 'Novo Departamento')
@section('subtitle', 'Cadastro de um novo departamento')

@section('content')

    <div class="max-w-xl">

        <form method="POST" action="{{ route('admin.departments.store') }}" class="bg-white p-6 rounded-lg shadow space-y-4">

            @csrf


            <!-- Nome -->
            <div>
                <label class="text-sm text-slate-600">Nome do Departamento</label>

                <input type="text" name="name" value="{{ old('name') }}" required
                    class="mt-1 w-full rounded border-slate-300 text-sm">

                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <!-- Ações -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.departments.index') }}"
                    class="px-4 py-2 border rounded text-sm hover:bg-slate-100">
                    Cancelar
                </a>

                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                    Salvar
                </button>
            </div>

        </form>

    </div>

@endsection
