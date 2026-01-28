@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <h1 class="text-xl font-semibold">Pré-visualização da Importação</h1>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th>Linha</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Departamento</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach ($preview as $row)
                    <tr class="{{ $row['valid'] ? '' : 'bg-red-50' }}">
                        <td class="px-2 py-1">{{ $row['line'] }}</td>
                        <td class="px-2 py-1">{{ $row['data']['name'] }}</td>
                        <td class="px-2 py-1">{{ $row['data']['email'] }}</td>
                        <td class="px-2 py-1">{{ $row['data']['role'] }}</td>
                        <td class="px-2 py-1">{{ $row['data']['department'] }}</td>
                        <td class="px-2 py-1">
                            @if ($row['valid'])
                                <span class="text-green-600 font-medium">OK</span>
                            @else
                                <ul class="text-red-600 text-xs">
                                    @foreach ($row['errors'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-2 border rounded">
            Cancelar
        </a>

        <form method="POST" action="{{ route('admin.users.import.confirm') }}">
            @csrf
            <button class="px-6 py-2 bg-blue-600 text-white rounded">
                Confirmar Importação
            </button>
        </form>
    </div>

</div>
@endsection
