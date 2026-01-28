@extends('layouts.agent')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- ============================================================
    | CABEÇALHO
    ============================================================ --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold text-slate-800">
                    Chamado {{ $ticket->code }}
                </h1>
                <p class="text-sm text-slate-500">
                    Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <a href="{{ route('agent.queue') }}" class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100">
                Voltar para fila
            </a>
        </div>

        {{-- ============================================================
    | BADGES
    ============================================================ --}}
        @php
            $status = $ticket->statusBadge();
            $priority = $ticket->priorityBadge();
        @endphp

        <div class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-3 items-center">
            <span class="px-3 py-1 rounded text-xs {{ $status['color'] }}">
                {{ $status['label'] }}
            </span>

            <span class="px-3 py-1 rounded text-xs {{ $priority['color'] }}">
                {{ $priority['label'] }}
            </span>

            <span class="px-3 py-1 rounded text-xs {{ $sla['color'] }}">
                {{ $sla['label'] }}
            </span>

            <span class="text-xs text-slate-500 ml-auto">
                Responsável:
                <strong>{{ $ticket->assignedAgent->name ?? 'Não atribuído' }}</strong>
            </span>
        </div>

        {{-- ============================================================
    | TIMELINE
    ============================================================ --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-6">
            <h2 class="font-semibold text-slate-800 text-sm">
                Linha do tempo do chamado
            </h2>

            @forelse ($timeline as $event)
                @if ($event['type'] === 'message')
                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex justify-between text-xs text-slate-500">
                            <span>{{ $event['user'] }}</span>
                            <span>{{ $event['created_at']->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="prose max-w-none text-sm mt-2">
                            {!! $event['content'] !!}
                        </div>
                    </div>
                @elseif ($event['type'] === 'group_transfer')
                    <div class="border-l-4 border-purple-500 pl-4 bg-slate-50 p-3 rounded">
                        <div class="text-xs text-slate-600">
                            <strong>{{ $event['user'] }}</strong>
                            encaminhou de
                            <strong>{{ $event['from_group'] }}</strong>
                            para
                            <strong>{{ $event['to_group'] }}</strong>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-sm text-slate-500">Nenhuma movimentação registrada.</p>
            @endforelse
        </div>

        {{-- ============================================================
    | ATENDIMENTO (N1)
    ============================================================ --}}
        @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
            <div class="bg-white rounded-xl shadow p-6 space-y-4">

                <h2 class="font-semibold text-slate-800 text-sm">
                    Atendimento do chamado
                </h2>

                <form method="POST" action="{{ route('agent.tickets.update', $ticket) }}" enctype="multipart/form-data"
                    id="ticket-update-form" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    {{-- ====================================================
| TIPO DE MENSAGEM (REGRA ITIL)
==================================================== --}}
                    <div>
                        <label class="text-xs text-slate-500">
                            Tipo da mensagem
                        </label>

                        <div class="mt-1 space-y-1 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="message_type" value="user" checked>
                                Mensagem ao usuário (pausa SLA)
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="radio" name="message_type" value="internal">
                                Nota interna (somente equipe)
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="radio" name="message_type" value="closing">
                                Mensagem de encerramento
                            </label>
                        </div>
                    </div>


                    {{-- Mensagem --}}
                    <div>
                        <label class="text-xs text-slate-500">Mensagem ao usuário</label>

                        <div id="message-editor" contenteditable="true"
                            class="mt-1 w-full rounded border p-3 min-h-[120px] text-sm"></div>

                        <input type="hidden" name="message" id="message">
                    </div>

                    {{-- Prioridade --}}
                    <div>
                        <label class="text-xs text-slate-500">Prioridade</label>
                        <select name="priority" class="mt-1 w-full rounded border text-sm">
                            @foreach (\App\Enums\Priority::cases() as $p)
                                <option value="{{ $p->value }}" @selected($ticket->priority === $p)>
                                    {{ $p->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="text-xs text-slate-500">Status</label>
                        <select name="status" class="mt-1 w-full rounded border text-sm">
                            <option value="in_progress">Em atendimento</option>
                            <option value="resolved">Resolver e fechar</option>
                        </select>
                    </div>

                    {{-- Anexos --}}
                    <div>
                        <label class="text-xs text-slate-500">Anexos</label>
                        <input type="file" name="attachments[]" multiple>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-6 py-2 bg-blue-600 text-white rounded text-sm">
                            Registrar atendimento
                        </button>
                    </div>
                </form>
            </div>

            {{-- ============================================================
        | ENCAMINHAMENTO (ESCALONAMENTO)
        ============================================================ --}}
            <div class="bg-white rounded-xl shadow p-6 space-y-4">

                <h2 class="font-semibold text-slate-800 text-sm">
                    Encaminhar chamado
                </h2>

                <form method="POST" action="{{ route('agent.tickets.forward', $ticket) }}" class="space-y-4">
                    @csrf

                    {{-- GRUPO --}}
                    <div>
                        <label class="text-xs text-slate-500">Grupo destino (opcional)</label>
                        <select name="to_group_id" class="mt-1 w-full rounded border text-sm">
                            <option value="">Manter grupo atual</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ESPECIALISTA --}}
                    <div>
                        <label class="text-xs text-slate-500">Especialista (opcional)</label>
                        <select name="assigned_to" class="mt-1 w-full rounded border text-sm">
                            <option value="">Não atribuir</option>
                            @foreach ($specialists as $specialist)
                                <option value="{{ $specialist->id }}">{{ $specialist->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- NOTA --}}
                    <div>
                        <label class="text-xs text-slate-500">Nota</label>
                        <textarea name="note" rows="2" class="mt-1 w-full rounded border text-sm"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-6 py-2 bg-purple-600 text-white rounded text-sm">
                            Encaminhar chamado
                        </button>
                    </div>
                </form>

            </div>
        @endif

    </div>
@endsection
