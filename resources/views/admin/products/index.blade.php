@extends('layouts.admin')

@section('title', 'Produtos')
@section('subtitle', 'Sistemas e plataformas atendidas')

@section('content')

<div class="flex justify-between items-center mb-6">
    <a href="{{ route('admin.products.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded text-sm">
        + Novo Produto
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
            <tr>
                <th class="text-left px-4 py-3">Nome</th>
                <th>Status</th>
                <th class="text-right px-4">Ações</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @foreach($products as $product)
                <tr>
                    <td class="px-4 py-3">
                        <strong>{{ $product->name }}</strong>
                        <div class="text-xs text-slate-400">{{ $product->slug }}</div>
                    </td>

                    <td>
                        <span class="px-2 py-1 text-xs rounded
                            {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>

                    <td class="text-right px-4">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="text-blue-600 text-xs hover:underline">
                            Editar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4">
        {{ $products->links() }}
    </div>
</div>

@endsection
