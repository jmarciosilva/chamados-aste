@extends('layouts.admin')

@section('title', 'Novo Produto')
@section('subtitle', 'Cadastro de sistema / plataforma')

@section('content')
<form method="POST" action="{{ route('admin.products.store') }}"
      class="bg-white rounded-xl shadow p-6 max-w-xl">
    @csrf
    @include('admin.products.form')

    <div class="mt-6 flex justify-end">
        <button class="px-6 py-2 bg-blue-600 text-white rounded text-sm">
            Salvar
        </button>
    </div>
</form>
@endsection
