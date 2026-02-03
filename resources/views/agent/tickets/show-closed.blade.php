@extends('layouts.agent')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- ============================================================
        | CABEÇALHO DO CHAMADO
        |============================================================ -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold text-slate-800">
                    Chamado {{ $ticket->code }}
                </h1>
                <p class="text-sm text-slate-500">
                    Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <a href="{{ route('agent.queue.closed') }}" class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100">
                Voltar para fechados
            </a>
        </div>

        <!-- ============================================================
        | BADGES (STATUS / PRIORIDADE / SLA)
        |============================================================ -->
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
                <strong>{{ $ticket->assignedAgent->name ?? '—' }}</strong>
            </span>
        </div>

        <!-- ============================================================
        | TIMELINE (SOMENTE LEITURA)
        |============================================================ -->
        <div class="bg-white rounded-xl shadow p-6 space-y-6">

            <h2 class="font-semibold text-slate-800 text-sm">
                Histórico completo do chamado
            </h2>

            @if ($timeline->isEmpty())
                <p class="text-sm text-slate-500 text-center py-6">
                    Nenhum histórico disponível.
                </p>
            @else
                <div class="space-y-4">

                    @foreach ($timeline as $event)
                        {{-- ================= MENSAGENS ================= --}}
                        @if ($event['type'] === 'message')
                            @php
                                $isSystem = empty($event['user_id']);
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
                            @else
                                <div class="flex justify-start">
                                    <div
                                        class="max-w-[70%] rounded-xl px-4 py-3 text-sm shadow
                                    {{ $isInternal ? 'bg-yellow-100 text-yellow-900 border border-yellow-300' : 'bg-slate-100 text-slate-900' }}">

                                        <div class="text-xs font-semibold mb-1">
                                            {{ $event['user'] }}
                                            @if ($isInternal)
                                                <span
                                                    class="ml-1 text-[10px] px-2 py-0.5 rounded bg-yellow-300 text-yellow-900">
                                                    Nota interna
                                                </span>
                                            @endif
                                        </div>

                                        <div
                                            class="whitespace-pre-wrap prose prose-sm max-w-none
                                        [&_img]:max-w-full [&_img]:h-auto [&_img]:rounded-lg [&_img]:mt-2 [&_img]:shadow-sm">
                                            {!! $event['content'] !!}
                                        </div>

                                        {{-- Anexos --}}
                                        @if (isset($event['attachments']) && $event['attachments']->isNotEmpty())
                                            <div class="mt-2 space-y-1">
                                                @foreach ($event['attachments'] as $attachment)
                                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                        class="text-xs underline opacity-80 hover:opacity-100 block">
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

                        {{-- ================= TRANSFERÊNCIA DE GRUPO ================= --}}
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

        <!-- ============================================================
        | AÇÕES DISPONÍVEIS (CHAMADO FECHADO)
        |============================================================ -->
        <div class="bg-white rounded-xl shadow p-6 flex flex-wrap gap-3 justify-end">

            <!-- Reabrir chamado -->
            <form method="POST" action="{{ route('agent.tickets.reopen', $ticket) }}">
                @csrf

                <div class="flex justify-end gap-4 pt-2">
                    <button type="reset"
                        class="px-6 py-3 border-2 border-slate-300 rounded-lg text-sm font-semibold
                                   hover:bg-slate-100 transition">
                        🔁 Reabrir chamado
                    </button>


                </div>


                {{-- <button
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg
                       hover:bg-yellow-700 text-sm font-medium">
                    🔁 Reabrir chamado
                </button>
            </form> --}}

            <!-- Imprimir histórico (placeholder) -->
            {{-- <button disabled
                class="px-4 py-2 bg-slate-300 text-slate-600 rounded-lg
                   text-sm font-medium cursor-not-allowed">
                🖨️ Imprimir histórico
            </button> --}}
        </div>

    </div>
@endsection
