@extends('layouts.user')

@section('content')

    <div class="max-w-6xl mx-auto">

        <!-- ============================================================
        | CABEÇALHO
        ============================================================ -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Abrir Novo Chamado</h1>
                <p class="text-sm text-slate-500">
                    Informe o problema para que o time correto possa ajudar rapidamente.
                </p>
            </div>

            <a href="{{ route('user.home') }}" class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100 transition">
                Cancelar
            </a>
        </div>

        <!-- ============================================================
        | ERROS DE VALIDAÇÃO
        ============================================================ -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-sm">
                <ul class="list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user.tickets.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- ========================================================
                | FORMULÁRIO PRINCIPAL
                ======================================================== -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow p-6 space-y-6">

                        <!-- ====================================================
                        | SOLICITANTE / DEPARTAMENTO
                        ==================================================== -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-slate-500">Solicitante</label>
                                <input type="text" value="{{ auth()->user()->name }}" disabled
                                    class="mt-1 w-full rounded border-slate-300 bg-slate-100 text-sm">
                            </div>

                            <div>
                                <label class="text-xs text-slate-500">Departamento</label>
                                <input type="text" value="{{ auth()->user()->department->name ?? '—' }}" disabled
                                    class="mt-1 w-full rounded border-slate-300 bg-slate-100 text-sm">
                            </div>
                        </div>

                        <!-- ====================================================
                        | PRODUTO
                        ==================================================== -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">
                                Produto
                            </label>

                            <select name="product_id" required class="mt-1 w-full rounded border-slate-300 text-sm">
                                <option value="">Selecione o produto</option>

                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ====================================================
                        | TIPO DE SERVIÇO
                        ==================================================== -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">
                                Tipo de Serviço
                            </label>

                            <select name="service_type" required class="mt-1 w-full rounded border-slate-300 text-sm">
                                <option value="">Selecione o tipo</option>

                                @foreach (\App\Enums\ServiceType::cases() as $type)
                                    <option value="{{ $type->value }}">
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ====================================================
                        | CATEGORIA DO PROBLEMA
                        ==================================================== -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">
                                Categoria do Problema
                            </label>

                            <select name="problem_category_id" required
                                class="mt-1 w-full rounded border-slate-300 text-sm">
                                <option value="">Selecione a categoria</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1 text-xs text-slate-500">
                                A lista será filtrada automaticamente conforme o produto no futuro.
                            </p>
                        </div>

                        <!-- ====================================================
                        | ASSUNTO
                        ==================================================== -->
                        <div>
                            <label class="text-sm text-slate-600">Assunto</label>
                            <input type="text" name="subject" required
                                class="mt-1 w-full rounded border-slate-300 text-sm"
                                placeholder="Ex: PDV da loja Morumbi não finaliza venda">
                        </div>

                        <!-- ====================================================
                        | DESCRIÇÃO (EDITOR COM COLAR IMAGEM)
                        ==================================================== -->
                        <div>
                            <label class="text-sm text-slate-600 mb-1 block">
                                Descrição detalhada
                            </label>

                            <div id="description-editor" contenteditable="true"
                                class="mt-1 w-full rounded border border-slate-300 text-sm p-3 min-h-[150px] focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <input type="hidden" name="description" id="description">

                            <p class="mt-1 text-xs text-slate-500">
                                💡 Você pode colar prints (Ctrl+V) diretamente aqui.
                            </p>
                        </div>


                        <!-- ====================================================
                        | ANEXOS
                        ==================================================== -->
                        <div>
                            <label class="text-sm text-slate-600 mb-1 block">
                                Anexos adicionais
                            </label>

                            <input type="file" name="attachments[]" multiple class="block w-full text-sm text-slate-600">
                        </div>

                        <!-- ====================================================
                        | AÇÕES
                        ==================================================== -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="reset" class="px-4 py-2 border rounded text-sm">
                                Limpar
                            </button>

                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded text-sm">
                                Criar Chamado
                            </button>
                        </div>

                    </div>
                </div>

                <!-- ========================================================
                | SIDEBAR – SLA (INFORMATIVO)
                ======================================================== -->
                <aside class="space-y-6 lg:sticky lg:top-6 self-start">
                    <div class="bg-white rounded-xl shadow border border-slate-100">
                        <div class="px-5 py-4 border-b bg-slate-50 rounded-t-xl">
                            <h3 class="font-semibold text-slate-800 text-sm">
                                ⏱️ Prazos de Atendimento (SLA)
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                O SLA será aplicado automaticamente após a abertura.
                            </p>
                        </div>

                        <div class="p-4 text-xs text-slate-500 text-center">
                            Os prazos dependem do produto, tipo de serviço e prioridade.
                        </div>
                    </div>
                </aside>

            </div>
        </form>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const editor = document.getElementById('description-editor');
    const hiddenInput = document.getElementById('description');
    const form = editor.closest('form');

    if (!editor || !hiddenInput || !form) {
        console.error('Editor ou input não encontrado');
        return;
    }

    // Colar imagem (Ctrl+V)
    editor.addEventListener('paste', async function (event) {
        const items = event.clipboardData?.items || [];

        for (const item of items) {
            if (item.type.startsWith('image/')) {
                event.preventDefault();

                const file = item.getAsFile();
                const formData = new FormData();
                formData.append('image', file);

                const response = await fetch('{{ route('user.tickets.upload-image') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.url) {
                    editor.insertAdjacentHTML(
                        'beforeend',
                        `<img src="${data.url}" class="my-2 max-w-full rounded border" />`
                    );
                }
            }
        }
    });

    // 🔥 GARANTIA: copiar conteúdo antes do submit
    form.addEventListener('submit', function () {
        hiddenInput.value = editor.innerHTML.trim();
    });

});
</script>
@endsection


