@extends('layouts.admin')

@section('title', 'Departamentos')
@section('subtitle', 'Gestão dos departamentos da organização')

@section('content')

<!-- Ações -->
<div class="flex justify-end mb-4">
    <a href="{{ route('admin.departments.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        Novo Departamento
    </a>
</div>

<!-- Tabela -->
<div class="bg-white rounded-lg shadow overflow-hidden">

    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">Nome</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Ações</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @forelse ($departments as $department)
                <tr class="hover:bg-slate-50">

                    <td class="px-4 py-3 font-medium">
                        {{ $department->name }}
                    </td>

                   

                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded
                            {{ $department->is_active
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700' }}">
                            {{ $department->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-right space-x-2">

                        <a href="{{ route('admin.departments.edit', $department) }}"
                           class="text-blue-600 hover:underline text-sm">
                            Editar
                        </a>

                        <form action="{{ route('admin.departments.toggle-status', $department) }}"
                              method="POST"
                              class="inline">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="text-xs text-slate-500 hover:text-slate-700">
                                {{ $department->is_active ? 'Inativar' : 'Ativar' }}
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="text-center py-6 text-slate-500">
                        Nenhum departamento cadastrado.
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>

    <!-- Paginação -->
    <div class="p-4">
        {{ $departments->links() }}
    </div>

</div>

@endsection
