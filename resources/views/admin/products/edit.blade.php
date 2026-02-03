@extends('layouts.admin')

@section('title', 'Editar Produto')
@section('subtitle', $product->name)

@section('content')

{{-- Container Centralizado --}}
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <form method="POST" action="{{ route('admin.products.update', $product) }}"
              class="bg-white rounded-xl shadow p-6">
            @csrf
            @method('PUT')
            @include('admin.products.form', ['product' => $product])

            <div class="mt-6 flex justify-between items-center">
                {{-- Botão Inativar/Ativar --}}
                <button type="button"
                        onclick="if(confirm('Tem certeza?')) { document.getElementById('toggle-form').submit(); }"
                        class="text-sm {{ $product->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }}">
                    {{ $product->is_active ? 'Inativar Produto' : 'Ativar Produto' }}
                </button>

                {{-- Botões Cancelar e Atualizar --}}
                <div class="flex gap-3">
                    <a href="{{ route('admin.products.index') }}"
                       class="px-6 py-2 bg-slate-200 text-slate-700 rounded text-sm hover:bg-slate-300 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                        Atualizar Produto
                    </button>
                </div>
            </div>
        </form>

        {{-- Form oculto para toggle de status --}}
        <form id="toggle-form" method="POST" action="{{ route('admin.products.destroy', $product) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

@endsection