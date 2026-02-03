@extends('layouts.admin')

@section('title', 'Fila de Chamados')
@section('subtitle', 'Visão operacional em tempo real (somente leitura)')

@section('content')

    @include('components.ticket-queue-table', [
        'tickets' => $tickets
    ])

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>

@endsection
