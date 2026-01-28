@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-xl font-semibold mb-6">Nova Categoria de Problema</h1>

    <form method="POST" action="{{ route('admin.problem-categories.store') }}">
        @csrf
        @include('admin.problem-categories.form')
    </form>
</div>
@endsection
