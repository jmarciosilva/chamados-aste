@extends('layouts.user')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- ============================================================
    | CABEÇALHO
    ============================================================ -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Meus Chamados</h1>
            <p class="text-sm text-slate-500">
                Acompanhe o andamento das suas solicitações.
            </p>
        </div>

        <a href="{{ route('user.tickets.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            Abrir Novo Chamado
        </a>
    </div>

    <!-- ============================================================
    | TABELA
    ============================================================ -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-2 text-left">Chamado</th>
                    <th class="px-4 py-2 text-left">Assunto</th>
                    <th class="px-4 py-2 text-left">Produto</th>
                    <th class="px-4 py-2 text-left">Categoria</th>
                    <th class="px-4 py-2 text-left">Prioridade</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">SLA</th>
                    <th class="px-4 py-2 text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($tickets as $ticket)

                    @php
                        $sla = $ticket->slaIndicator();

                        $slaColor = match ($sla['status']) {
                            'running'   => 'bg-blue-100 text-blue-700',
                            'paused'    => 'bg-yellow-100 text-yellow-700',
                            'breached'  => 'bg-red-100 text-red-700',
                            'completed' => 'bg-green-100 text-green-700',
                            default     => 'bg-slate-100 text-slate-600',
                        };

                        $priorityColor = $ticket->priority->color();
                    @endphp

                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $ticket->code }}</td>

                        <td class="px-4 py-3">{{ $ticket->subject }}</td>

                        <td class="px-4 py-3">{{ $ticket->product->name }}</td>

                        <td class="px-4 py-3">{{ $ticket->problemCategory->name }}</td>

                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded {{ $priorityColor }}">
                                {{ $ticket->priority->label() }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded bg-slate-100 text-slate-700">
                                {{ $ticket->status_label }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded {{ $slaColor }}">
                                {{ $sla['label'] }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('user.tickets.show', $ticket) }}"
                               class="text-blue-600 hover:underline">
                                Ver
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-slate-500">
                            Nenhum chamado encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
