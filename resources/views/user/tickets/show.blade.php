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

        <a href="{{ route('user.tickets.index') }}"
           class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100">
            Voltar
        </a>
    </div>

    {{-- ============================================================
    | BADGES (STATUS / PRIORIDADE / SLA)
    ============================================================ --}}
    @php
        $status   = $ticket->statusBadge();
        $priority = $ticket->priorityBadge();
        $sla      = $ticket->slaBadge();
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

        <div>
            <p class="text-xs text-slate-500">Categoria</p>
            <p>{{ $ticket->problemCategory->name }}</p>
        </div>

        <div class="md:col-span-2">
            <p class="text-xs text-slate-500">Assunto</p>
            <p class="font-medium">{{ $ticket->subject }}</p>
        </div>
    </div>

    {{-- ============================================================
    | TIMELINE DO CHAMADO (USUÁRIO)
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-6">

        <h2 class="font-semibold text-slate-800 text-sm">
            Histórico do chamado
        </h2>

        @forelse ($timeline as $event)

            {{-- =======================
            | MENSAGEM / SISTEMA
            ======================= --}}
            @if ($event['type'] === 'message')

                <div class="border-l-4 border-blue-500 pl-4">
                    <div class="flex justify-between items-center text-xs text-slate-500">
                        <span>{{ $event['user'] }}</span>
                        <span>{{ $event['created_at']->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="prose max-w-none text-sm mt-2">
                        {!! $event['content'] !!}
                    </div>

                    {{-- ANEXOS --}}
                    @if ($event['attachments']->isNotEmpty())
                        <div class="flex flex-wrap gap-3 mt-3">
                            @foreach ($event['attachments'] as $attachment)
                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                   target="_blank"
                                   class="block border rounded p-2 hover:bg-slate-50">
                                    <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                         class="max-h-32 rounded">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            {{-- =======================
            | TRANSFERÊNCIA DE GRUPO
            ======================= --}}
            @elseif ($event['type'] === 'group_transfer')

                <div class="border-l-4 border-purple-500 pl-4 bg-slate-50 rounded p-3">
                    <div class="text-xs text-slate-600">
                        <strong>{{ $event['user'] }}</strong>
                        encaminhou seu chamado de
                        <strong>{{ $event['from_group'] }}</strong>
                        para
                        <strong>{{ $event['to_group'] }}</strong>
                    </div>

                    @if ($event['note'])
                        <div class="text-xs text-slate-500 mt-1">
                            Nota: {{ $event['note'] }}
                        </div>
                    @endif

                    <div class="text-xs text-slate-400 mt-1">
                        {{ $event['created_at']->format('d/m/Y H:i') }}
                    </div>
                </div>

            @endif

        @empty
            <p class="text-sm text-slate-500">
                Nenhuma atualização registrada ainda.
            </p>
        @endforelse
    </div>

    {{-- ============================================================
    | RESPOSTA DO USUÁRIO
    ============================================================ --}}
    @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)

        <div class="bg-white rounded-xl shadow p-6 space-y-4">

            <h2 class="font-semibold text-slate-800 text-sm">
                Enviar mensagem
            </h2>

            <form method="POST"
                  action="{{ route('user.tickets.reply', $ticket) }}"
                  class="space-y-4"
                  id="user-reply-form">
                @csrf

                {{-- EDITOR COM CTRL+V --}}
                <div>
                    <label class="text-xs text-slate-500">
                        Sua mensagem
                    </label>

                    <div id="message-editor"
                         contenteditable="true"
                         class="mt-1 w-full rounded border border-slate-300 text-sm p-3 min-h-[120px]
                                focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <input type="hidden" name="message" id="message">

                    <p class="mt-1 text-[11px] text-slate-500">
                        💡 Você pode colar prints (Ctrl + V) diretamente aqui.
                    </p>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded text-sm">
                        Enviar mensagem
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
document.addEventListener('DOMContentLoaded', function () {

    const editor = document.getElementById('message-editor');
    const hiddenInput = document.getElementById('message');
    const form = document.getElementById('user-reply-form');

    if (!editor || !hiddenInput || !form) return;

    editor.addEventListener('paste', async function (event) {
        const items = event.clipboardData?.items || [];

        for (const item of items) {
            if (item.type.startsWith('image/')) {
                event.preventDefault();

                const file = item.getAsFile();
                const formData = new FormData();
                formData.append('upload', file);

                const response = await fetch(
                     '{{ route('tickets.upload-image') }}',
                    {
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

    form.addEventListener('submit', function () {
        hiddenInput.value = editor.innerHTML.trim();
    });

});
</script>
@endsection
