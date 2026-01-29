@extends('layouts.user')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- ============================================================
    | CABEÇALHO
    ============================================================ --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-slate-800">
                    Chamado {{ $ticket->code }}
                </h1>
                <p class="text-sm text-slate-500">
                    Acompanhe todas as atualizações do seu chamado.
                </p>
            </div>

            <a href="{{ route('user.tickets.index') }}" class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100">
                Voltar
            </a>
        </div>

        {{-- ============================================================
    | BADGES (STATUS / PRIORIDADE / SLA)
    ============================================================ --}}
        @php
            $status = $ticket->statusBadge();
            $priority = $ticket->priorityBadge();
            $sla = $ticket->slaBadge();
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
                <strong class="text-slate-700">
                    {{ $ticket->assignedAgent->name ?? 'Aguardando atendimento' }}
                </strong>
            </span>
        </div>

        {{-- ============================================================
    | DADOS DO CHAMADO
    ============================================================ --}}
        <div class="bg-white rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <p class="text-xs text-slate-500">Produto</p>
                <p class="font-medium">{{ $ticket->product->name }}</p>
            </div>

            {{-- <div>
            <p class="text-xs text-slate-500">Categoria</p>
            <p>{{ $ticket->problemCategory->name }}</p>
        </div> --}}

            <div class="md:col-span-2">
                <p class="text-xs text-slate-500">Assunto</p>
                <p class="font-medium">{{ $ticket->subject }}</p>
            </div>
        </div>

        {{-- ============================================================
| TIMELINE — CHAT (USUÁRIO)
============================================================ --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-6">

            <h2 class="font-semibold text-slate-800 text-sm">
                Conversa e histórico do chamado
            </h2>

            <div class="space-y-4">

                @forelse ($timeline as $event)
                    @if ($event['type'] === 'message')
                        @php
                            $isSystem = empty($event['user_id']);
                            $isMine = !empty($event['user_id']) && $event['user_id'] === auth()->id();

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
                            {{ $isMine ? 'bg-blue-100 text-blue-900' : 'bg-green-100 text-green-900' }}
                        ">

                                    {{-- Nome --}}
                                    <div class="text-xs font-semibold mb-1">
                                        {{ $event['user'] }}
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
| RESPOSTA DO USUÁRIO — CHAT COM SUPORTE
============================================================ --}}
        @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
            <div class="bg-white rounded-xl shadow border border-slate-200 overflow-hidden">

                {{-- ========================================================
        | CABEÇALHO DO CHAT
        ======================================================== --}}
                <div class="px-6 py-4 border-b bg-slate-50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center">
                        💬
                    </div>

                    <div>
                        <h2 class="font-semibold text-slate-800 text-sm">
                            Conversa com o Suporte
                        </h2>
                        <p class="text-xs text-slate-500">
                            Envie mensagens e acompanhe as respostas do técnico responsável.
                        </p>
                    </div>
                </div>

                {{-- ========================================================
        | ÁREA DE ENVIO (ESTILO CHAT)
        ======================================================== --}}
                <form method="POST" action="{{ route('user.tickets.reply', $ticket) }}" id="user-reply-form"
                    class="p-5 space-y-3">
                    @csrf

                    {{-- EDITOR DE MENSAGEM --}}
                    <div>
                        <label class="sr-only">
                            Sua mensagem
                        </label>

                        <div id="message-editor" contenteditable="true" placeholder="Digite sua mensagem..."
                            class="w-full rounded-xl border border-slate-300 text-sm p-4 min-h-[120px]
                            bg-slate-50
                            focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <input type="hidden" name="message" id="message">

                        <p class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                            📎 Você pode colar prints da tela (Ctrl + V) diretamente na mensagem.
                        </p>
                    </div>

                    {{-- ====================================================
            | AÇÕES DO CHAT
            ==================================================== --}}
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2
                               px-6 py-2 rounded-full
                               bg-blue-600 text-white text-sm
                               hover:bg-blue-700 transition">
                            ➤ Enviar mensagem
                        </button>
                    </div>

                </form>
            </div>
        @endif


    </div>
@endsection

{{-- ============================================================
| SCRIPT: CTRL+V (UPLOAD DE IMAGEM)
============================================================ --}}
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const editor = document.getElementById('message-editor');
            const hiddenInput = document.getElementById('message');
            const form = document.getElementById('user-reply-form');

            if (!editor || !hiddenInput || !form) return;

            editor.addEventListener('paste', async function(event) {
                const items = event.clipboardData?.items || [];

                for (const item of items) {
                    if (item.type.startsWith('image/')) {
                        event.preventDefault();

                        const file = item.getAsFile();
                        const formData = new FormData();
                        formData.append('upload', file);

                        const response = await fetch(
                            '{{ route('tickets.upload-image') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            }
                        );

                        const data = await response.json();

                        if (data.url) {
                            editor.insertAdjacentHTML(
                                'beforeend',
                                `<img src="${data.url}"
                              class="my-2 max-w-full rounded border" />`
                            );
                        }
                    }
                }
            });

            form.addEventListener('submit', function() {
                hiddenInput.value = editor.innerHTML.trim();
            });

        });
    </script>
@endsection
