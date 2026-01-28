@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-xl font-semibold mb-6">Editar Categoria</h1>

    <form method="POST" action="{{ route('admin.problem-categories.update', $problemCategory) }}">
        @csrf
        @method('PUT')
        @include('admin.problem-categories.form', ['category' => $problemCategory])
    </form>
</div>
@endsection
