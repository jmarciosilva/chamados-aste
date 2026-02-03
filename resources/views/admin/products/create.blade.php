@extends('layouts.admin')

@section('title', 'Novo Serviço')
@section('subtitle', 'Cadastro de serviços / soluções oferecidos pelo Grupo Aste.')

@section('content')

{{-- Container Centralizado --}}
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <form method="POST" action="{{ route('admin.products.store') }}"
              class="bg-white rounded-xl shadow p-6">
            @csrf
            @include('admin.products.form')

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.products.index') }}"
                   class="px-6 py-2 bg-slate-200 text-slate-700 rounded text-sm hover:bg-slate-300 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                    Salvar Serviço
                </button>
            </div>
        </form>
    </div>
</div>

@endsection