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

        <a href="{{ route('user.home') }}"
           class="text-sm px-3 py-1.5 border rounded hover:bg-slate-100 transition">
            Cancelar
        </a>
    </div>

    <!-- ============================================================
    | ERROS DE VALIDAÇÃO (BACKEND)
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

    <form method="POST"
          action="{{ route('user.tickets.store') }}"
          enctype="multipart/form-data">

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
                            <input type="text"
                                   value="{{ auth()->user()->department->name ?? '—' }}"
                                   disabled
                                   class="mt-1 w-full rounded border-slate-300 bg-slate-100 text-sm">
                        </div>
                    </div>

                    <!-- ====================================================
                    | PRODUTO (CARROSSEL)
                    ==================================================== -->
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-2 block">
                            Produto
                        </label>

                        <div class="flex gap-4 overflow-x-auto pb-3">
                            @foreach ($products as $product)
                                <button type="button"
                                        data-product-id="{{ $product->id }}"
                                        class="product-card min-w-[260px] p-4 border rounded-xl text-left
                                               transition hover:bg-blue-50 hover:border-blue-400">

                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-100
                                                    flex items-center justify-center
                                                    text-blue-700 text-sm font-semibold">
                                            {{ strtoupper(substr($product->name, 0, 1)) }}
                                        </div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $product->name }}
                                        </p>
                                    </div>

                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        {{ $product->description ?? 'Clique para abrir um chamado para este produto.' }}
                                    </p>
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="product_id" id="product_id">
                    </div>

                    <!-- ====================================================
                    | TIPO DE OCORRÊNCIA
                    ==================================================== -->
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-2 block">
                            Tipo de Ocorrência
                        </label>

                        <div class="grid grid-cols-2 gap-4">
                            @foreach (\App\Enums\ServiceType::cases() as $type)
                                <button type="button"
                                        data-service-type="{{ $type->value }}"
                                        class="service-type-card p-4 border rounded-lg
                                               transition hover:bg-blue-50">
                                    {{ $type->label() }}
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="service_type" id="service_type">
                    </div>

                    <!-- ====================================================
                    | ASSUNTO
                    ==================================================== -->
                    <div>
                        <label class="text-sm text-slate-600">Assunto</label>
                        <input type="text"
                               name="subject"
                               required
                               class="mt-1 w-full rounded border-slate-300 text-sm"
                               placeholder="Ex: PDV da loja Morumbi não finaliza venda">
                    </div>

                    <!-- ====================================================
                    | DESCRIÇÃO (EDITOR)
                    ==================================================== -->
                    <div>
                        <label class="text-sm text-slate-600 mb-1 block">
                            Descrição detalhada
                        </label>

                        <div id="description-editor"
                             contenteditable="true"
                             class="mt-1 w-full rounded border border-slate-300 text-sm
                                    p-3 min-h-[150px]
                                    focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- INPUT REAL ENVIADO AO BACKEND -->
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

                        <input type="file"
                               name="attachments[]"
                               multiple
                               class="block w-full text-sm text-slate-600">
                    </div>

                    <!-- ====================================================
                    | QUESTIONÁRIO — IMPACTO / CRITICIDADE
                    ==================================================== -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-4">
                        <h3 class="font-semibold text-slate-800">
                            Qual o impacto deste problema no seu trabalho?
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach (\App\Enums\Criticality::cases() as $criticality)
                                <button type="button"
                                        data-criticality="{{ $criticality->value }}"
                                        class="criticality-btn p-4 border rounded-lg text-left
                                               transition hover:bg-blue-50">
                                    {{ $criticality->label() }}
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="criticality" id="criticality">

                        <p id="criticality-error"
                           class="hidden text-sm text-red-600">
                            ⚠️ Informe o impacto do problema para continuar.
                        </p>
                    </div>

                    <!-- ====================================================
                    | AÇÕES
                    ==================================================== -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="reset"
                                class="px-4 py-2 border rounded text-sm">
                            Limpar
                        </button>

                        <button type="submit"
                                id="submit-ticket"
                                disabled
                                class="px-6 py-2 rounded text-sm
                                       bg-slate-300 text-slate-500 cursor-not-allowed">
                            Criar Chamado
                        </button>
                    </div>

                </div>
            </div>

            <!-- ========================================================
            | SIDEBAR – SLA
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
                        Os prazos dependem do produto, tipo de serviço e criticidade.
                    </div>
                </div>
            </aside>

        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const editor = document.getElementById('description-editor');
    const hiddenDescription = document.getElementById('description');
    const submitBtn = document.getElementById('submit-ticket');
    const criticalityInput = document.getElementById('criticality');
    const criticalityError = document.getElementById('criticality-error');

    /* ============================================================
       SINCRONIZAÇÃO REAL DO EDITOR (FIX DEFINITIVO)
    ============================================================ */
    const syncDescription = () => {
        hiddenDescription.value = editor.innerHTML.trim();
    };

    editor.addEventListener('input', syncDescription);
    editor.addEventListener('blur', syncDescription);
    editor.addEventListener('paste', () => setTimeout(syncDescription, 50));

    /* ============================================================
       FUNÇÕES AUXILIARES
    ============================================================ */
    function clearSelection(selector, classes) {
        document.querySelectorAll(selector)
            .forEach(el => el.classList.remove(...classes));
    }

    function validateForm() {
        if (
            document.getElementById('product_id').value &&
            document.getElementById('service_type').value &&
            criticalityInput.value
        ) {
            submitBtn.disabled = false;
            submitBtn.classList.remove(
                'bg-slate-300', 'text-slate-500', 'cursor-not-allowed'
            );
            submitBtn.classList.add(
                'bg-blue-600', 'text-white', 'hover:bg-blue-700'
            );
        }
    }

    /* ============================================================
       PRODUTO
    ============================================================ */
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('product_id').value = card.dataset.productId;
            clearSelection('.product-card',
                ['bg-blue-50','border-blue-500','ring-1','ring-blue-300']);
            card.classList.add(
                'bg-blue-50','border-blue-500','ring-1','ring-blue-300');
            validateForm();
        });
    });

    /* ============================================================
       TIPO DE OCORRÊNCIA
    ============================================================ */
    document.querySelectorAll('.service-type-card').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('service_type').value = card.dataset.serviceType;
            clearSelection('.service-type-card',
                ['bg-blue-50','border-blue-500','ring-1','ring-blue-300']);
            card.classList.add(
                'bg-blue-50','border-blue-500','ring-1','ring-blue-300');
            validateForm();
        });
    });

    /* ============================================================
       CRITICIDADE
    ============================================================ */
    document.querySelectorAll('.criticality-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            criticalityInput.value = btn.dataset.criticality;
            clearSelection('.criticality-btn',
                ['bg-blue-50','border-blue-500','ring-1','ring-blue-300']);
            btn.classList.add(
                'bg-blue-50','border-blue-500','ring-1','ring-blue-300');
            criticalityError.classList.add('hidden');
            validateForm();
        });
    });

});
</script>
@endsection
