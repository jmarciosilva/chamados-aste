@extends('layouts.agent')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- CABEÇALHO --}}
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">
                Chamado {{ $ticket->code }}
            </h1>
            <p class="text-sm text-slate-500">
                Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <a href="{{ route('agent.queue') }}"
           class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100">
            Voltar para fila
        </a>
    </div>

    {{-- BADGES --}}
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

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===================== --}}
        {{-- COLUNA ESQUERDA --}}
        {{-- ===================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TIMELINE --}}
            <div class="bg-white rounded-xl shadow p-6 space-y-6">
                <h2 class="font-semibold text-slate-800 text-sm">
                    Conversa e histórico do chamado
                </h2>

                @if($timeline->isEmpty())
                    <p class="text-sm text-slate-500">
                        Nenhum histórico disponível.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach ($timeline as $event)

                            {{-- MENSAGEM --}}
                            @if ($event['type'] === 'message')
                                @php
                                    $isSystem = empty($event['user_id']);
                                    $isMine = !empty($event['user_id']) && $event['user_id'] === auth()->id();
                                    $isInternal = $event['is_internal'] ?? false;
                                @endphp

                                {{-- SISTEMA --}}
                                @if ($isSystem)
                                    <div class="text-center text-xs text-slate-500">
                                        {{ $event['content'] }}
                                        <div class="text-[10px] mt-1">
                                            {{ $event['created_at']->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @else
                                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[70%] rounded-xl px-4 py-3 text-sm shadow
                                            {{ $isInternal ? 'bg-yellow-100 text-yellow-900 border border-yellow-300' : '' }}
                                            {{ !$isInternal && $isMine ? 'bg-blue-100 text-blue-900' : '' }}
                                            {{ !$isInternal && !$isMine ? 'bg-green-100 text-green-900' : '' }}
                                        ">
                                            <div class="text-xs font-semibold mb-1">
                                                {{ $event['user'] }}
                                                @if ($isInternal)
                                                    <span class="ml-1 text-[10px] px-2 py-0.5 rounded bg-yellow-300">
                                                        Nota interna
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="prose prose-sm max-w-none [&_img]:max-w-full">
                                                {!! $event['content'] !!}
                                            </div>

                                            @if(isset($event['attachments']) && $event['attachments']->isNotEmpty())
                                                <div class="mt-2 space-y-1">
                                                    @foreach ($event['attachments'] as $attachment)
                                                        <a href="{{ Storage::url($attachment->file_path) }}"
                                                           target="_blank"
                                                           class="text-xs underline">
                                                            📎 {{ $attachment->original_name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="text-[10px] mt-2 opacity-70">
                                                {{ $event['created_at']->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- TRANSFERÊNCIA --}}
                            @if ($event['type'] === 'group_transfer')
                                <div class="text-center">
                                    <div class="inline-block bg-purple-50 border border-purple-200 rounded-lg px-4 py-2">
                                        <div class="text-xs text-purple-900">
                                            <strong>{{ $event['user'] }}</strong> transferiu o chamado
                                        </div>
                                        <div class="text-xs text-purple-700 mt-1">
                                            {{ $event['from_group'] }} → {{ $event['to_group'] }}
                                        </div>
                                        <div class="text-[10px] mt-1 text-purple-400">
                                            {{ $event['created_at']->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endforeach
                    </div>
                @endif
            </div>

            {{-- FORMULÁRIO DE RESPOSTA --}}
            @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
                <div class="bg-white rounded-xl shadow border">
                    <div class="px-6 py-4 border-b bg-slate-50">
                        <h2 class="font-semibold text-slate-800 text-sm">
                            Responder chamado
                        </h2>
                    </div>

                    <form method="POST"
                          action="{{ route('agent.tickets.update', $ticket) }}"
                          enctype="multipart/form-data"
                          class="p-6 space-y-5">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="message_type" value="user">

                        <x-message-editor
                            name="message"
                            placeholder="Digite sua mensagem..."
                            :upload-route="route('tickets.upload-image')"
                        />

                        <input type="file" name="attachments[]" multiple
                               class="w-full text-sm border rounded-lg px-3 py-2">

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Enviar resposta
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        {{-- ===================== --}}
        {{-- COLUNA DIREITA (AÇÕES) --}}
        {{-- ===================== --}}
        <div class="space-y-6">

            {{-- ENCERRAR CHAMADO --}}
            @if($ticket->status !== \App\Enums\TicketStatus::CLOSED)
                <div class="bg-white rounded-xl shadow border p-5">
                    <h3 class="text-sm font-semibold mb-4">Encerrar chamado</h3>

                    <form method="POST"
                          action="{{ route('agent.tickets.update', $ticket) }}">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="message_type" value="closing">
                        <input type="hidden" name="message"
                               value="Chamado encerrado pelo operador.">

                        <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            ✔ Encerrar chamado
                        </button>
                    </form>
                </div>
            @endif

            {{-- ENCAMINHAR CHAMADO --}}
            @if($groups->isNotEmpty())
                <div class="bg-white rounded-xl shadow border p-5">
                    <h3 class="text-sm font-semibold mb-4">Encaminhar chamado</h3>

                    <form method="POST"
                          action="{{ route('agent.tickets.forward', $ticket) }}"
                          class="space-y-4">
                        @csrf

                        <select name="to_group_id"
                                class="w-full border rounded-lg px-3 py-2 text-sm"
                                required>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>

                        @if($specialists->isNotEmpty())
                            <select name="assigned_to"
                                    class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="">Não atribuir</option>
                                @foreach($specialists as $specialist)
                                    <option value="{{ $specialist->id }}">{{ $specialist->name }}</option>
                                @endforeach
                            </select>
                        @endif

                        <textarea name="note"
                                  rows="2"
                                  class="w-full border rounded-lg px-3 py-2 text-sm"
                                  placeholder="Observação (opcional)"></textarea>

                        <button type="submit"
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            🔁 Encaminhar chamado
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
