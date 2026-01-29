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
    | BADGES (STATUS / PRIORIDADE / SLA)
    ============================================================ --}}
        @php
            $status = $ticket->statusBadge();
            $priority = $ticket->priorityBadge();
        @endphp

        <div class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-3 items-center">
            <span class="px-3 py-1 rounded-full text-xs {{ $status['color'] }}">
                {{ $status['label'] }}
            </span>

            <span class="px-3 py-1 rounded-full text-xs {{ $priority['color'] }}">
                {{ $priority['label'] }}
            </span>

            <span class="px-3 py-1 rounded-full text-xs {{ $sla['color'] }}">
                {{ $sla['label'] }}
            </span>

            <span class="text-xs text-slate-500 ml-auto">
                Responsável:
                <strong>{{ $ticket->assignedAgent->name ?? 'Não atribuído' }}</strong>
            </span>
        </div>

        {{-- ============================================================
| TIMELINE — CHAT (OPERADOR)
============================================================ --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-6">

            <h2 class="font-semibold text-slate-800 text-sm">
                Conversa e histórico do chamado
            </h2>

            <div class="space-y-4">

                @forelse ($timeline as $event)
                    {{-- ====================================================
            | MENSAGEM
            ==================================================== --}}
                    @if ($event['type'] === 'message')
                        @php
                            $isSystem = empty($event['user_id']);
                            $isMine = !empty($event['user_id']) && $event['user_id'] === auth()->id();

                            $isInternal = $event['is_internal'] ?? false;
                        @endphp

                        {{-- ================= SISTEMA ================= --}}
                        @if ($isSystem)
                            <div class="text-center text-xs text-slate-500">
                                {{ $event['content'] }}
                                <div class="text-[10px] mt-1">
                                    {{ $event['created_at']->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            {{-- ================= USUÁRIO / OPERADOR ================= --}}
                        @else
                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                                <div
                                    class="
                            max-w-[70%] rounded-xl px-4 py-3 text-sm shadow
                            {{ $isInternal ? 'bg-yellow-100 text-yellow-900 border border-yellow-300' : '' }}
                            {{ !$isInternal && $isMine ? 'bg-blue-100 text-blue-900' : '' }}
                            {{ !$isInternal && !$isMine ? 'bg-green-100 text-green-900' : '' }}
                        ">

                                    {{-- Nome --}}
                                    <div class="text-xs font-semibold mb-1">
                                        {{ $event['user'] }}
                                        @if ($isInternal)
                                            <span
                                                class="ml-1 text-[10px] px-2 py-0.5 rounded bg-yellow-300 text-yellow-900">
                                                Nota interna
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Conteúdo --}}
                                    <div class="prose max-w-none text-sm">
                                        {!! $event['content'] !!}
                                    </div>

                                    {{-- Anexos --}}
                                    @if (!empty($event['attachments']) && $event['attachments']->count())
                                        <div class="mt-2 space-y-1">
                                            @foreach ($event['attachments'] as $attachment)
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                    class="block text-xs text-blue-700 underline">
                                                    📎 {{ $attachment->original_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Data --}}
                                    <div class="text-[10px] text-slate-500 text-right mt-2">
                                        {{ $event['created_at']->format('d/m/Y H:i') }}
                                    </div>

                                </div>
                            </div>
                        @endif

                        {{-- ====================================================
            | TRANSFERÊNCIA / EVENTO ITIL
            ==================================================== --}}
                    @elseif ($event['type'] === 'group_transfer')
                        <div
                            class="text-center bg-purple-50 border border-purple-200
                            text-purple-700 text-xs rounded px-4 py-2">
                            <strong>{{ $event['user'] }}</strong>
                            encaminhou de
                            <strong>{{ $event['from_group'] }}</strong>
                            para
                            <strong>{{ $event['to_group'] }}</strong>
                            <div class="text-[10px] mt-1">
                                {{ $event['created_at']->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @endif

                @empty
                    <p class="text-sm text-slate-500 text-center">
                        Nenhuma interação registrada até o momento.
                    </p>
                @endforelse

            </div>
        </div>



        {{-- ============================================================
    | AÇÕES DO OPERADOR
    ============================================================ --}}
        @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
            {{-- ============================================================
    | ATENDIMENTO / COMUNICAÇÃO
    ============================================================ --}}
            <div class="bg-white rounded-xl shadow border border-slate-200">

                <div class="px-6 py-4 border-b bg-slate-50">
                    <h2 class="font-semibold text-slate-800 text-sm">
                        Atendimento do chamado
                    </h2>
                    <p class="text-xs text-slate-500">
                        Pergunte ao usuário, registre notas internas ou finalize o chamado.
                    </p>
                </div>

                <form method="POST" action="{{ route('agent.tickets.update', $ticket) }}" enctype="multipart/form-data"
                    class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    {{-- Tipo de ação --}}
                    <div>
                        <label class="text-xs text-slate-500">Tipo de ação</label>

                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                            <label class="border rounded-lg p-3 cursor-pointer hover:bg-blue-50">
                                <input type="radio" name="message_type" value="user" checked>
                                Perguntar ao usuário (pausa SLA)
                            </label>

                            <label class="border rounded-lg p-3 cursor-pointer hover:bg-yellow-50">
                                <input type="radio" name="message_type" value="internal">
                                Nota interna
                            </label>

                            <label class="border rounded-lg p-3 cursor-pointer hover:bg-green-50">
                                <input type="radio" name="message_type" value="closing">
                                Encerrar chamado
                            </label>
                        </div>
                    </div>

                    {{-- Mensagem --}}
                    <div>
                        <label class="text-xs text-slate-500">Mensagem</label>

                        <div id="message-editor" contenteditable="true"
                            class="mt-1 w-full rounded-xl border p-4 min-h-[120px]
                            text-sm bg-slate-50">
                        </div>

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

                    {{-- Status (controlado pelo backend) --}}
                    <input type="hidden" name="status" value="in_progress">

                    {{-- Anexos --}}
                    <div>
                        <label class="text-xs text-slate-500">Anexos</label>
                        <input type="file" name="attachments[]" multiple>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-6 py-2 bg-blue-600 text-white rounded-full text-sm">
                            Registrar ação
                        </button>
                    </div>
                </form>
            </div>

            {{-- ============================================================
    | ENCAMINHAMENTO (ESCALONAMENTO)
    ============================================================ --}}
            <div class="bg-white rounded-xl shadow border border-slate-200 p-6 space-y-4">

                <h2 class="font-semibold text-slate-800 text-sm">
                    Encaminhar chamado
                </h2>

                <form method="POST" action="{{ route('agent.tickets.forward', $ticket) }}" class="space-y-4">
                    @csrf

                    {{-- Grupo --}}
                    <div>
                        <label class="text-xs text-slate-500">Grupo destino</label>
                        <select name="to_group_id" class="mt-1 w-full rounded border text-sm">
                            <option value="">Manter grupo atual</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Especialista --}}
                    <div>
                        <label class="text-xs text-slate-500">Especialista</label>
                        <select name="assigned_to" class="mt-1 w-full rounded border text-sm">
                            <option value="">Não atribuir</option>
                            @foreach ($specialists as $specialist)
                                <option value="{{ $specialist->id }}">
                                    {{ $specialist->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nota --}}
                    <div>
                        <label class="text-xs text-slate-500">Nota</label>
                        <textarea name="note" rows="2" class="mt-1 w-full rounded border text-sm"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-6 py-2 bg-purple-600 text-white rounded-full text-sm">
                            Encaminhar chamado
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection
