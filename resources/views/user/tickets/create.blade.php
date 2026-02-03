@extends('layouts.user')

@section('content')

    <div class="max-w-7xl mx-auto px-4">

        <!-- ============================================================
                            | CABEÇALHO
                            ============================================================ -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Abrir Novo Chamado</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Selecione o produto e descreva o problema para receber atendimento rápido
                </p>
            </div>

            <a href="{{ route('user.home') }}"
                class="text-sm px-4 py-2 border-2 border-slate-300 rounded-lg hover:bg-slate-100 transition font-medium">
                ← Voltar
            </a>
        </div>

        <!-- ============================================================
                            | ERROS DE VALIDAÇÃO
                            ============================================================ -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-800 mb-2">Corrija os seguintes erros:</h3>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('user.tickets.store') }}" enctype="multipart/form-data">

            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- ========================================================
                                    | FORMULÁRIO PRINCIPAL
                                    ======================================================== -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- ====================================================
                                        | INFORMAÇÕES DO SOLICITANTE
                                        ==================================================== -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-100">
                        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            👤 Informações do Solicitante
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Solicitante</label>
                                <input type="text" value="{{ auth()->user()->name }}" disabled
                                    class="mt-1 w-full rounded-lg border-slate-300 bg-white text-sm font-medium">
                            </div>

                            <div>
                                <label class="text-xs font-medium text-slate-600">Departamento</label>
                                <input type="text" value="{{ auth()->user()->department->name ?? 'Não informado' }}"
                                    disabled class="mt-1 w-full rounded-lg border-slate-300 bg-white text-sm font-medium">
                            </div>
                        </div>
                    </div>

                    <!-- ====================================================
                                        | SELEÇÃO DE PRODUTO
                                        ==================================================== -->
                    <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                        <h3 class="font-bold text-slate-800 mb-2">📦 Qual produto está com problema?</h3>

                        <div class="flex gap-3 overflow-x-auto pb-2">
                            @foreach ($products as $product)
                                <button type="button" data-product-id="{{ $product->id }}"
                                    data-product-sla='@json($product->getSlaConfigFormatted())'
                                    data-impact='@json(optional($product->impactQuestion)->load('answers'))'
                                    class="product-card min-w-[320px] p-4 border-2 border-slate-200 rounded-xl
                                           hover:border-yellow-400 hover:bg-yellow-100 transition flex gap-4">

                                    <div
                                        class="w-14 h-14 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($product->name, 0, 2)) }}
                                    </div>

                                    <div>
                                        <p class="font-bold text-slate-800">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $product->description ?? 'Sistema corporativo' }}
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="product_id" id="product_id">
                    </div>

                    <!-- ====================================================
                                        | TIPO DE OCORRÊNCIA (CARDS COLORIDOS)
                                        ==================================================== -->
                    <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                        <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="text-2xl">🎯</span>
                            Tipo de Ocorrência
                        </h3>
                        <p class="text-xs text-slate-500 mb-4">O que você precisa?</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @php
                                $serviceTypeColors = [
                                    'incident' => ['bg' => 'red', 'icon' => '🚨'],
                                    'service_request' => ['bg' => 'blue', 'icon' => '🎫'],
                                    'purchase_request' => ['bg' => 'green', 'icon' => '🛒'],
                                    'improvement' => ['bg' => 'purple', 'icon' => '💡'],
                                ];
                            @endphp

                            @foreach (\App\Enums\ServiceType::cases() as $type)
                                @php
                                    $color = $serviceTypeColors[$type->value] ?? ['bg' => 'gray', 'icon' => '📋'];
                                @endphp

                                <button type="button" data-service-type="{{ $type->value }}"
                                    data-color="{{ $color['bg'] }}"
                                    class="service-type-card group p-4 border-2 border-slate-200 rounded-xl text-left
                                           transition-all duration-200 hover:shadow-lg hover:scale-105
                                           hover:border-{{ $color['bg'] }}-400 hover:bg-{{ $color['bg'] }}-50">

                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">{{ $color['icon'] }}</span>
                                        <span
                                            class="font-semibold text-slate-700 group-hover:text-{{ $color['bg'] }}-700">
                                            {{ $type->label() }}
                                        </span>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="service_type" id="service_type">
                    </div>


                    <!-- ====================================================
                                        | ASSUNTO
                                        ==================================================== -->
                    <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                        <label class="font-semibold text-slate-700 mb-2 block">
                            📝 Assunto do Chamado *
                        </label>
                        <input type="text" name="subject" required
                            class="w-full rounded-lg border-2 border-slate-200 p-3">
                    </div>

                    <!-- ====================================================
                                        | DESCRIÇÃO
                                        ==================================================== -->
                    <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                        <label class="font-semibold text-slate-700 mb-2 block">
                            📄 Descrição Detalhada
                        </label>

                        <div id="description-editor" contenteditable="true"
                            class="border-2 border-slate-200 rounded-lg p-4 min-h-[160px] bg-slate-50"></div>

                        <input type="hidden" name="description" id="description">
                    </div>

                    <!-- ====================================================
                                    | ANEXOS
                                    ==================================================== -->
                    <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                        <label class="font-semibold text-slate-700 mb-2 block">
                            📎 Anexos Adicionais
                        </label>

                        <input type="file" name="attachments[]" multiple
                            class="block w-full text-sm text-slate-600
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 transition">
                    </div>





                    <!-- ====================================================
                                        | PERGUNTA DE IMPACTO (DINÂMICA)
                                        ==================================================== -->
                    <div id="impact-container"
                        class="hidden bg-gradient-to-br from-orange-50 to-yellow-50 rounded-xl shadow-lg border-2 border-orange-200 p-6">

                        <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            ⚠️ <span id="impact-question-text"></span>
                        </h3>

                        <div id="impact-answers" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>

                        <input type="hidden" name="impact_answer_id" id="impact_answer_id">

                        <p id="impact-error" class="hidden text-sm text-red-700 mt-3">
                            Selecione uma opção para continuar
                        </p>
                    </div>

                    <!-- ====================================================
                                        | BOTÕES
                                        ==================================================== -->
                    <div class="flex justify-end gap-4">
                        <button type="reset" class="px-6 py-3 border rounded-lg">
                            Limpar
                        </button>

                        <button type="submit" id="submit-ticket" disabled
                            class="px-10 py-4 rounded-lg bg-slate-300 text-slate-500 cursor-not-allowed">
                            Criar Chamado
                        </button>
                    </div>

                </div>

                <!-- ========================================================
                            | SIDEBAR – SLA DINÂMICO
                            ======================================================== -->
                <aside class="lg:sticky lg:top-6 self-start">
                    <div class="bg-white rounded-xl shadow-xl border-2 border-blue-100 overflow-hidden">
                        <div class="px-5 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                            <h3 class="font-bold text-white text-lg flex items-center gap-2">
                                ⏱️ Prazos de Atendimento (SLA)
                            </h3>
                            <p class="text-xs text-blue-100 mt-1">
                                Tempos estimados de resposta e resolução
                            </p>
                        </div>

                        <div id="sla-table" class="p-5">
                            <div class="text-center py-8 text-slate-400">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm">Selecione um produto<br>para ver os prazos</p>
                            </div>
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

            /* ============================================================
               ELEMENTOS
            ============================================================ */
            const editor = document.getElementById('description-editor');
            const hiddenDescription = document.getElementById('description');
            const submitBtn = document.getElementById('submit-ticket');

            const impactContainer = document.getElementById('impact-container');
            const impactQuestionText = document.getElementById('impact-question-text');
            const impactAnswers = document.getElementById('impact-answers');
            const impactAnswerInput = document.getElementById('impact_answer_id');

            const productInput = document.getElementById('product_id');
            const serviceTypeInput = document.getElementById('service_type');

            const slaTable = document.getElementById('sla-table');

            /* ============================================================
               EDITOR
            ============================================================ */
            const syncDescription = () => {
                hiddenDescription.value = editor.innerHTML.trim();
            };

            editor.addEventListener('input', syncDescription);
            editor.addEventListener('blur', syncDescription);
            editor.addEventListener('paste', () => setTimeout(syncDescription, 50));

            /* ============================================================
               UTIL
            ============================================================ */
            function clearSelection(selector) {
                document.querySelectorAll(selector).forEach(el => {
                    el.classList.remove(
                        'border-yellow-400', 'bg-yellow-100',
                        'ring-4', 'ring-yellow-300',
                        'shadow-xl', 'scale-105'
                    );
                });
            }

            function validateForm() {
                const isValid =
                    productInput.value &&
                    serviceTypeInput.value &&
                    impactAnswerInput.value;

                submitBtn.disabled = !isValid;

                if (isValid) {
                    submitBtn.classList.remove(
                        'bg-slate-300',
                        'text-slate-500',
                        'cursor-not-allowed',
                        'opacity-50'
                    );

                    submitBtn.classList.add(
                        'bg-blue-600',
                        'text-white',
                        'cursor-pointer',
                        'hover:bg-blue-700',
                        'shadow-lg'
                    );
                } else {
                    submitBtn.classList.remove(
                        'bg-blue-600',
                        'text-white',
                        'cursor-pointer',
                        'hover:bg-blue-700',
                        'shadow-lg'
                    );

                    submitBtn.classList.add(
                        'bg-slate-300',
                        'text-slate-500',
                        'cursor-not-allowed',
                        'opacity-50'
                    );
                }
            }


            /* ============================================================
               SLA
            ============================================================ */
            function updateSlaTable(sla) {
                const labels = {
                    critical: ['Crítica', 'red', '🔴'],
                    high: ['Alta', 'orange', '🟠'],
                    medium: ['Média', 'yellow', '🟡'],
                    low: ['Baixa', 'green', '🟢']
                };

                let html = '<div class="space-y-3">';
                Object.entries(sla).forEach(([key, data]) => {
                    const [label, color, icon] = labels[key];
                    html += `
                <div class="p-3 rounded border bg-${color}-50 border-${color}-200">
                    <strong>${icon} ${label}</strong>
                    <div class="grid grid-cols-2 text-xs mt-2">
                        <div>Resposta: <b>${data.response_hours}h</b></div>
                        <div>Resolução: <b>${data.resolution_hours}h</b></div>
                    </div>
                </div>
            `;
                });
                html += '</div>';

                slaTable.innerHTML = html;
            }

            /* ============================================================
               IMPACTO
            ============================================================ */
            function renderImpact(impact) {
                impactAnswers.innerHTML = '';
                impactQuestionText.textContent = impact.question;

                impact.answers.forEach(answer => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'p-4 border rounded-lg bg-white hover:bg-yellow-100';
                    btn.textContent = answer.label;

                    btn.onclick = () => {
                        clearSelection('#impact-answers button');
                        btn.classList.add('bg-yellow-100', 'border-yellow-400');
                        impactAnswerInput.value = answer.id;
                        validateForm();
                    };

                    impactAnswers.appendChild(btn);
                });

                impactContainer.classList.remove('hidden');
            }

            /* ============================================================
               PRODUTO
            ============================================================ */
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', () => {

                    productInput.value = card.dataset.productId;

                    clearSelection('.product-card');
                    card.classList.add('border-yellow-400', 'bg-yellow-100', 'ring-4',
                        'ring-yellow-300', 'shadow-xl');

                    updateSlaTable(JSON.parse(card.dataset.productSla));
                    renderImpact(JSON.parse(card.dataset.impact));

                    validateForm();
                });
            });

            /* ============================================================
               TIPO DE OCORRÊNCIA
            ============================================================ */
            document.querySelectorAll('.service-type-card').forEach(card => {
                card.addEventListener('click', () => {

                    serviceTypeInput.value = card.dataset.serviceType;

                    clearSelection('.service-type-card');
                    card.classList.add('border-yellow-400', 'bg-yellow-100', 'ring-4',
                        'ring-yellow-300', 'shadow-lg');

                    validateForm();
                });
            });

        });
    </script>
@endsection
