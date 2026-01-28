@extends('layouts.agent')

@section('content')
<div class="space-y-6">

    {{-- ============================================================
    | KPIs DO DASHBOARD OPERACIONAL
    | Visível APENAS para OPERADORES (Service Desk)
    ============================================================ --}}
    @if (auth()->user()->agent_type === 'operator')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Chamados aguardando atendimento --}}
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-xs text-slate-500">
                    Chamados aguardando atendimento
                </p>
                <p class="text-2xl font-bold">
                    {{ $waitingCount }}
                </p>
            </div>

            {{-- Chamados em atendimento --}}
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-xs text-slate-500">
                    Chamados em atendimento
                </p>
                <p class="text-2xl font-bold text-yellow-600">
                    {{ $inProgressCount }}
                </p>
            </div>

            {{-- Chamados resolvidos hoje --}}
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-xs text-slate-500">
                    Resolvidos hoje
                </p>
                <p class="text-2xl font-bold text-green-600">
                    {{ $resolvedTodayCount }}
                </p>
            </div>

            {{-- CSAT (placeholder futuro) --}}
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-xs text-slate-500">
                    Satisfação (CSAT)
                </p>
                <p class="text-2xl font-bold text-slate-400">
                    —
                </p>
            </div>

        </div>
    @endif

    {{-- ============================================================
    | FILA DE CHAMADOS
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nº Chamado</th>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Solicitante</th>
                    <th class="px-4 py-3 text-left">Departamento</th>
                    <th class="px-4 py-3 text-left">Prioridade</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Abertura</th>
                    <th class="px-4 py-3 text-right">Ação</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($tickets as $ticket)

                    @php
                        /**
                         * -------------------------------------------------------
                         * BADGES PRONTOS VINDOS DO MODEL
                         * - Nenhuma lógica na view
                         * -------------------------------------------------------
                         */
                        $priority = $ticket->priorityBadge();
                        $status   = $ticket->statusBadge();
                    @endphp

                    <tr class="hover:bg-slate-50">

                        {{-- Código do chamado --}}
                        <td class="px-4 py-3 font-medium">
                            {{ $ticket->code }}
                        </td>

                        {{-- Assunto --}}
                        <td class="px-4 py-3">
                            {{ $ticket->subject }}
                        </td>

                        {{-- Solicitante --}}
                        <td class="px-4 py-3">
                            {{ $ticket->requester->name }}
                        </td>

                        {{-- Departamento --}}
                        <td class="px-4 py-3">
                            {{ $ticket->department->name ?? '—' }}
                        </td>

                        {{-- PRIORIDADE --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[11px] {{ $priority['color'] }}">
                                {{ $priority['label'] }}
                            </span>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[11px] {{ $status['color'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>

                        {{-- Data de abertura --}}
                        <td class="px-4 py-3 text-slate-500">
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </td>

                        {{-- ====================================================
                        | AÇÕES
                        ==================================================== --}}
                        <td class="px-4 py-2 text-right space-x-1">

                            {{-- 1️⃣ Chamado ainda não assumido --}}
                            @if (is_null($ticket->assigned_to))
                                <form method="POST"
                                      action="{{ route('agent.tickets.take', $ticket) }}"
                                      class="inline">
                                    @csrf
                                    <button
                                        class="px-2 py-0.5 text-[11px] rounded
                                               bg-blue-600 text-white hover:bg-blue-700">
                                        Atender
                                    </button>
                                </form>

                            {{-- 2️⃣ Chamado assumido pelo usuário logado --}}
                            @elseif ($ticket->assigned_to === auth()->id())
                                <a href="{{ route('agent.tickets.show', $ticket) }}"
                                   class="px-2 py-0.5 text-[11px] rounded
                                          bg-green-600 text-white hover:bg-green-700 inline-block">
                                    Continuar
                                </a>

                            {{-- 3️⃣ Chamado em atendimento por outro operador --}}
                            @else
                                <span
                                    class="px-2 py-0.5 text-[11px] rounded
                                           bg-slate-200 text-slate-600 cursor-not-allowed inline-block">
                                    Em atendimento
                                </span>
                            @endif

                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-slate-500">
                            Nenhum chamado na fila 🎉
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

    {{-- PAGINAÇÃO --}}
    <div>
        {{ $tickets->links() }}
    </div>

</div>
@endsection
