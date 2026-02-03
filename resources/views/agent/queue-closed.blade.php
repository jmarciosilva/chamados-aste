@extends('layouts.agent')

@section('content')
<div class="space-y-6">

    <!-- ============================================================
    | TÍTULO DA PÁGINA
    |============================================================ -->
    <h1 class="text-xl font-semibold text-slate-800">
        📁 Chamados Fechados
    </h1>

    <!-- ============================================================
    | TABELA DE CHAMADOS FECHADOS
    |------------------------------------------------------------
    | Regras de exibição:
    | - status = RESOLVED ou CLOSED
    | - resolved_at preenchido
    | - Ordenação feita no controller
    |============================================================ -->
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nº Chamado</th>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Solicitante</th>
                    <th class="px-4 py-3 text-left">Departamento</th>
                    {{-- <th class="px-4 py-3 text-left">Prioridade</th> --}}
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Fechado em</th>
                    <th class="px-4 py-3 text-right">Ação</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse($tickets as $ticket)

                    @php
                        /**
                         * ----------------------------------------------------------
                         * PRIORIDADE — cores visuais
                         * ----------------------------------------------------------
                         * Banco: enum (critical, high, medium, low)
                         * UI: cores semânticas
                         */
                        $priorityColor = match ($ticket->priority) {
                            'critical' => 'bg-red-100 text-red-700',
                            'high'     => 'bg-orange-100 text-orange-700',
                            'medium'   => 'bg-yellow-100 text-yellow-700',
                            'low'      => 'bg-green-100 text-green-700',
                            default    => 'bg-slate-100 text-slate-600',
                        };

                        /**
                         * ----------------------------------------------------------
                         * STATUS — via accessor do Model (statusBadge)
                         * ----------------------------------------------------------
                         * Ex:
                         * - Resolvido
                         * - Fechado
                         */
                        $statusBadge = $ticket->statusBadge();
                    @endphp

                    <tr class="hover:bg-slate-50">

                        <!-- Código do chamado -->
                        <td class="px-4 py-3 font-medium">
                            {{ $ticket->code }}
                        </td>

                        <!-- Assunto -->
                        <td class="px-4 py-3">
                            {{ $ticket->subject }}
                        </td>

                        <!-- Solicitante -->
                        <td class="px-4 py-3">
                            {{ $ticket->requester->name }}
                        </td>

                        <!-- Departamento -->
                        <td class="px-4 py-3">
                            {{ $ticket->department->name ?? '—' }}
                        </td>

                        <!-- Prioridade -->
                        {{-- <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[11px] {{ $priorityColor }}">
                                {{ $ticket->priority_label }}
                            </span>
                        </td> --}}

                        <!-- Status -->
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[11px] {{ $statusBadge['color'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </td>

                        <!-- Data de fechamento / resolução -->
                        <td class="px-4 py-3 text-slate-500">
                            {{ $ticket->resolved_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>

                        <!-- Ação -->
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('agent.tickets.show', $ticket) }}"
                               class="px-2 py-0.5 text-[11px] rounded
                                      bg-slate-200 text-slate-700 hover:bg-slate-300 inline-block">
                                Visualizar
                            </a>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-slate-500">
                            Nenhum chamado fechado ainda.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

    <!-- ============================================================
    | PAGINAÇÃO
    |============================================================ -->
    <div>
        {{ $tickets->links() }}
    </div>

</div>
@endsection
