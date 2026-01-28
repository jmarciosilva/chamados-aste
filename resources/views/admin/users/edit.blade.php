@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto">

    <h1 class="text-xl font-semibold mb-6">Editar Usuário</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        @include('admin.users.partials.form', ['user' => $user])

        <div class="mt-6 text-right">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Atualizar
            </button>
        </div>
    </form>

</div>

@endsection
