@extends('layouts.admin')

@section('title', 'Criar Usuário')
@section('subtitle', 'Cadastro de novos usuários no sistema')

@section('content')

<div class="max-w-5xl mx-auto">

    <!-- Card -->
    <div class="bg-white rounded-xl shadow-sm border">

        <!-- Cabeçalho do card -->
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-slate-800">
                Dados do Usuário
            </h2>
            <p class="text-sm text-slate-500">
                Preencha as informações abaixo para criar um novo usuário.
            </p>
        </div>

        <!-- Formulário -->
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-6">
            @csrf

            @include('admin.users.partials.form')

            <!-- Ações -->
            <div class="flex justify-end gap-3 pt-6 border-t">

                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 text-sm rounded-lg border text-slate-600 hover:bg-slate-100">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-5 py-2 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    Salvar Usuário
                </button>

            </div>
        </form>

    </div>

</div>

@endsection
