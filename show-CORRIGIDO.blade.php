@extends('layouts.agent')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- DEBUG INFO --}}
        <div class="bg-blue-50 border-2 border-blue-500 rounded-lg p-4">
            <h3 class="font-bold text-blue-900 mb-2">🔍 DEBUG INFO</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li><strong>Ticket ID:</strong> {{ $ticket->id }}</li>
                <li><strong>Total Mensagens (Model):</strong> {{ $ticket->messages->count() }}</li>
                <li><strong>Total Timeline:</strong> {{ $timeline->count() }}</li>
                <li><strong>Mensagens na Timeline:</strong> {{ $timeline->where('type', 'message')->count() }}</li>
            </ul>
        </div>

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

            <a href="{{ route('agent.queue') }}" class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100">
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

        {{-- TIMELINE --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-6">

            <h2 class="font-semibold text-slate-800 text-sm">
                Conversa e histórico do chamado
            </h2>

            {{-- VERIFICAÇÃO ADICIONAL --}}
            @if($timeline->isEmpty())
                <div class="bg-red-50 border border-red-200 rounded p-4">
                    <p class="text-sm text-red-800">
                        ⚠️ <strong>Timeline vazia!</strong> Verifique:
                    </p>
                    <ul class="text-xs text-red-700 mt-2 ml-4 list-disc">
                        <li>Se existem mensagens no banco de dados</li>
                        <li>Se o relacionamento está correto no Model</li>
                        <li>Se o eager loading está funcionando</li>
                    </ul>
                </div>

                {{-- TESTE DIRETO COM MESSAGES --}}
                @if($ticket->messages->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mt-4">
                        <p class="text-sm text-yellow-900 font-bold">
                            ✅ Mensagens existem no banco! ({{ $ticket->messages->count() }})
                        </p>
                        <p class="text-xs text-yellow-800 mt-1">
                            O problema está na montagem da timeline no controller.
                        </p>

                        <div class="mt-3 space-y-3">
                            @foreach($ticket->messages as $msg)
                                <div class="bg-white rounded p-3 border">
                                    <div class="text-xs font-bold text-slate-700">
                                        {{ $msg->user->name ?? 'Sistema' }}
                                        @if($msg->is_internal_note)
                                            <span class="ml-2 px-2 py-0.5 bg-yellow-200 rounded text-[10px]">
                                                NOTA INTERNA
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm mt-1">{{ $msg->message }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ $msg->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded p-4 mt-4">
                        <p class="text-sm text-gray-700">
                            ℹ️ Nenhuma mensagem no banco de dados ainda.
                        </p>
                    </div>
                @endif
            @else
                {{-- RENDERIZAÇÃO NORMAL DA TIMELINE --}}
                <div class="space-y-4">
                    @foreach ($timeline as $event)
                        
                        {{-- MENSAGEM --}}
                        @if ($event['type'] === 'message')
                            @php
                                $isSystem = empty($event['user_id']);
                                $isMine = !empty($event['user_id']) && $event['user_id'] === auth()->id();
                                $isInternal = $event['is_internal'] ?? false;
                            @endphp

                            {{-- MENSAGEM DO SISTEMA --}}
                            @if ($isSystem)
                                <div class="text-center text-xs text-slate-500">
                                    {{ $event['content'] }}
                                    <div class="text-[10px] mt-1">
                                        {{ $event['created_at']->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                            {{-- MENSAGEM NORMAL --}}
                            @else
                                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[70%] rounded-xl px-4 py-3 text-sm shadow
                                        {{ $isInternal ? 'bg-yellow-100 text-yellow-900 border border-yellow-300' : '' }}
                                        {{ !$isInternal && $isMine ? 'bg-blue-100 text-blue-900' : '' }}
                                        {{ !$isInternal && !$isMine ? 'bg-green-100 text-green-900' : '' }}
                                    ">
                                        {{-- Nome --}}
                                        <div class="text-xs font-semibold mb-1">
                                            {{ $event['user'] }}
                                            @if ($isInternal)
                                                <span class="ml-1 text-[10px] px-2 py-0.5 rounded bg-yellow-300 text-yellow-900">
                                                    Nota interna
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Conteúdo --}}
                                        <div class="whitespace-pre-wrap">
                                            {{ $event['content'] }}
                                        </div>

                                        {{-- Anexos --}}
                                        @if (isset($event['attachments']) && $event['attachments']->isNotEmpty())
                                            <div class="mt-2 space-y-1">
                                                @foreach ($event['attachments'] as $attachment)
                                                    <a href="{{ Storage::url($attachment->path) }}" target="_blank"
                                                        class="text-xs underline opacity-80 hover:opacity-100">
                                                        📎 {{ $attachment->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Hora --}}
                                        <div class="text-[10px] mt-2 opacity-70">
                                            {{ $event['created_at']->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- TRANSFERÊNCIA DE GRUPO --}}
                        @if ($event['type'] === 'group_transfer')
                            <div class="text-center">
                                <div class="inline-block bg-purple-50 border border-purple-200 rounded-lg px-4 py-2">
                                    <div class="text-xs text-purple-900">
                                        <strong>{{ $event['user'] }}</strong> transferiu o chamado
                                    </div>
                                    <div class="text-xs text-purple-700 mt-1">
                                        De: <strong>{{ $event['from_group'] }}</strong>
                                        → Para: <strong>{{ $event['to_group'] }}</strong>
                                    </div>
                                    @if ($event['note'])
                                        <div class="text-xs text-purple-600 mt-1 italic">
                                            "{{ $event['note'] }}"
                                        </div>
                                    @endif
                                    <div class="text-[10px] text-purple-400 mt-1">
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
            <div class="bg-white rounded-xl shadow border border-slate-200">

                <div class="px-6 py-4 border-b bg-slate-50">
                    <h2 class="font-semibold text-slate-800 text-sm">
                        Atendimento do chamado
                    </h2>
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
                                Perguntar ao usuário
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
                        <textarea name="message" rows="4"
                            class="w-full mt-1 border rounded-lg px-3 py-2 text-sm"
                            placeholder="Digite sua mensagem aqui..."
                            required></textarea>
                    </div>

                    {{-- Anexos --}}
                    <div>
                        <label class="text-xs text-slate-500">Anexos (opcional)</label>
                        <input type="file" name="attachments[]" multiple
                            class="w-full mt-1 text-sm border rounded-lg px-3 py-2">
                    </div>

                    {{-- Botão enviar --}}
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Enviar resposta
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
@endsection
