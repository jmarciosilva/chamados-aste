@extends('layouts.user')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- CABEÇALHO --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Chamado {{ $ticket->code }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Aberto em {{ $ticket->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <a href="{{ route('user.tickets.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Meus Chamados
            </a>
        </div>

        {{-- BADGES (STATUS / PRIORIDADE / SLA) --}}
        @php
            $status = $ticket->statusBadge();
            $priority = $ticket->priorityBadge();
            $sla = $ticket->slaBadge();
        @endphp

        <div class="bg-white rounded-xl shadow-sm border p-5 flex flex-wrap gap-3 items-center">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium {{ $status['color'] }}">
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                {{ $status['label'] }}
            </span>

            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $priority['color'] }}">
                {{ $priority['label'] }}
            </span>

            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $sla['color'] }}">
                {{ $sla['label'] }}
            </span>

            @if ($ticket->assignedAgent)
                <span class="text-xs text-slate-500 ml-auto">
                    Responsável:
                    <strong class="text-slate-900">{{ $ticket->assignedAgent->name }}</strong>
                </span>
            @endif
        </div>

        {{-- DETALHES DO CHAMADO --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50">
                <h2 class="font-semibold text-slate-800">{{ $ticket->subject }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-slate-600">Produto:</span>
                        <span class="font-medium text-slate-900">{{ $ticket->product->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600">Departamento:</span>
                        <span class="font-medium text-slate-900">{{ $ticket->department->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600">Tipo de Serviço:</span>
                        <span class="font-medium text-slate-900">
                            {{ $ticket->service_type->label() }}
                        </span>

                    </div>
                    <div>
                        <span class="text-slate-600">Grupo Atual:</span>
                        <span class="font-medium text-slate-900">{{ $ticket->currentGroup->name }}</span>
                    </div>
                </div>

                @if ($ticket->description)
                    <div class="pt-4 border-t">
                        <h3 class="text-sm font-medium text-slate-700 mb-2">Descrição Inicial:</h3>
                        <div class="prose prose-sm max-w-none text-slate-600">
                            {!! $ticket->description !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- TIMELINE --}}
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-6 py-4 border-b bg-slate-50">
                <h2 class="font-semibold text-slate-800">Histórico do Atendimento</h2>
            </div>

            <div class="px-6 py-6 space-y-6">
                @forelse($timeline as $item)
                    @if ($item['type'] === 'message')
                        {{-- FILTRAR NOTAS INTERNAS (USUÁRIO NÃO VÊ) --}}
                        @if (!$item['is_internal'])
                            @php
                                $isRequester = $item['user_id'] === $ticket->requester_id;

                                // Cores do avatar
                                if ($isRequester) {
                                    $avatarBg = 'bg-green-100';
                                    $avatarText = 'text-green-600';
                                } else {
                                    $avatarBg = 'bg-blue-100';
                                    $avatarText = 'text-blue-600';
                                }
                            @endphp

                            <div class="flex gap-4">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $avatarBg }} flex items-center justify-center">
                                        <span class="text-sm font-medium {{ $avatarText }}">
                                            {{ substr($item['user'], 0, 2) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Conteúdo --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="font-medium text-slate-900">{{ $item['user'] }}</span>
                                        <span class="text-xs text-slate-500">
                                            {{ $item['created_at']->format('d/m/Y H:i') }}
                                        </span>

                                        {{-- BADGE: VOCÊ (Solicitante) --}}
                                        @if ($isRequester)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Você
                                            </span>

                                            {{-- BADGE: OPERADOR --}}
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                                Atendente
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Mensagem --}}
                                    <div
                                        class="prose prose-sm max-w-none text-slate-700 bg-slate-50 rounded-lg p-4 [&_img]:max-w-full [&_img]:h-auto [&_img]:rounded-lg [&_img]:mt-2 [&_img]:shadow-sm">
                                        {!! $item['content'] !!}
                                    </div>

                                    {{-- Anexos --}}
                                    @if (isset($item['attachments']) && $item['attachments'] && $item['attachments']->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($item['attachments'] as $attachment)
                                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 
                                                          rounded-lg text-xs hover:bg-blue-100 transition-colors border border-blue-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    {{ $attachment->original_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @elseif($item['type'] === 'group_transfer')
                        {{-- TRANSFERÊNCIA --}}
                        <div class="flex gap-4 items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-purple-900">Transferência de Grupo</span>
                                        <span class="text-xs text-purple-600">
                                            {{ $item['created_at']->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-purple-800">
                                        De <strong>{{ $item['from_group'] }}</strong>
                                        para <strong>{{ $item['to_group'] }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-center text-slate-500 py-8">Nenhum histórico disponível</p>
                @endforelse
            </div>
        </div>

        {{-- FORMULÁRIO DE RESPOSTA --}}
        @if ($ticket->status->value !== 'closed' && $ticket->status->value !== 'resolved')
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-6 py-4 border-b bg-slate-50">
                    <h2 class="font-semibold text-slate-800">Adicionar Resposta</h2>
                </div>

                <form id="reply-form" action="{{ route('user.tickets.reply', $ticket) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="px-6 py-6 space-y-4">
                        {{-- Editor de Mensagem --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Sua Mensagem
                            </label>
                            <div id="editor" class="min-h-[200px] border rounded-lg"></div>
                            <textarea name="message" id="message-content" class="hidden"></textarea>
                        </div>

                        {{-- Anexos --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Anexos (opcional)</label>
                            <input type="file" name="attachments[]" multiple
                                class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 
                                          file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t bg-slate-50 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg 
                                       hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Enviar Resposta
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    {{-- EDITOR QUILL --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        // Inicializar Quill
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Digite sua mensagem aqui...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['link', 'image'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['clean']
                ]
            }
        });

        // Sincronizar com textarea hidden
        const form = document.getElementById('reply-form');

        if (form) {
            form.addEventListener('submit', function(e) {
                const content = quill.root.innerHTML.trim();

                if (content === '<p><br></p>' || content === '') {
                    e.preventDefault();
                    alert('Por favor, escreva uma mensagem antes de enviar.');
                    return;
                }

                document.getElementById('message-content').value = content;
            });
        }

        // Upload de imagem
        quill.getModule('toolbar').addHandler('image', () => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                const file = input.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('upload', file);

                try {
                    const response = await fetch('{{ route('tickets.upload-image') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    const range = quill.getSelection();
                    quill.insertEmbed(range.index, 'image', data.url);
                } catch (error) {
                    console.error('Erro ao fazer upload:', error);
                    alert('Erro ao enviar imagem');
                }
            };
        });
    </script>
@endsection
