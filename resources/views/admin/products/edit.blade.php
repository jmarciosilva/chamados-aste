@extends('layouts.admin')

@section('title', 'Editar Produto')
@section('subtitle', $product->name)

@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}"
      class="bg-white rounded-xl shadow p-6 max-w-xl">
    @csrf
    @method('PUT')
    @include('admin.products.form', ['product' => $product])

    <div class="mt-6 flex justify-between">
        <button formmethod="POST"
                formaction="{{ route('admin.products.destroy', $product) }}"
                class="text-red-600 text-sm">
            {{ $product->is_active ? 'Inativar' : 'Ativar' }}
        </button>

        <button class="px-6 py-2 bg-blue-600 text-white rounded text-sm">
            Atualizar
        </button>
    </div>
</form>
@endsection
