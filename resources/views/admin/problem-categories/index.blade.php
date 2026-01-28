@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex justify-between items-center">
            <h1 class="text-xl font-semibold">Categorias de Problemas</h1>

            <a href="{{ route('admin.problem-categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">
                Nova Categoria
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Nome</th>
                        <th class="px-4 py-2">Produto</th>
                        <th class="px-4 py-2">Prioridade</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($categories as $category)
                        <tr>
                            <td class="px-4 py-2">{{ $category->name }}</td>
                            <td class="px-4 py-2">{{ $category->product?->name ?? '—' }}</td>
                            <td class="px-4 py-2 capitalize">{{ $category->default_priority }}</td>
                            <td class="px-4 py-2">
                                {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('admin.problem-categories.edit', $category) }}"
                                    class="text-blue-600 text-sm">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $categories->links() }}
            </div>
        </div>

    </div>
@endsection
