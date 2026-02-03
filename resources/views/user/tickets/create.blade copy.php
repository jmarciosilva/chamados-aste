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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

    <form method="POST"
          action="{{ route('user.tickets.store') }}"
          enctype="multipart/form-data">

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
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informações do Solicitante
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Solicitante</label>
                            <input type="text" value="{{ auth()->user()->name }}" disabled
                                   class="mt-1 w-full rounded-lg border-slate-300 bg-white text-sm font-medium">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-slate-600">Departamento</label>
                            <input type="text"
                                   value="{{ auth()->user()->department->name ?? 'Não informado' }}"
                                   disabled
                                   class="mt-1 w-full rounded-lg border-slate-300 bg-white text-sm font-medium">
                        </div>
                    </div>
                </div>

                <!-- ====================================================
                | SELEÇÃO DE PRODUTO (CARROSSEL MELHORADO)
                ==================================================== -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                    <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                        <span class="text-2xl">📦</span>
                        Qual produto está com problema?
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Escolha o sistema ou plataforma</p>

                    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-slate-300">
                        @foreach ($products as $product)
                            <button type="button"
                                    data-product-id="{{ $product->id }}"
                                    data-product-sla='@json($product->getSlaConfigFormatted())'
                                    class="product-card group min-w-[320px] max-w-[360px] p-4 border-2 border-slate-200 rounded-xl
                                           transition-all duration-200 hover:shadow-xl hover:scale-[1.02]
                                           hover:border-blue-400 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50
                                           flex items-center gap-4">

                                <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600
                                            flex items-center justify-center text-white text-lg font-bold
                                            shadow-lg group-hover:scale-110 transition-transform flex-shrink-0">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 group-hover:text-blue-700 transition truncate">
                                        {{ $product->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-tight mt-1">
                                        {{ $product->description ?? 'Sistema ' . $product->name }}
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="product_id" id="product_id">
                    
                    <p id="product-error" class="hidden text-sm text-red-600 mt-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Selecione um produto para continuar
                    </p>
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

                            <button type="button"
                                    data-service-type="{{ $type->value }}"
                                    data-color="{{ $color['bg'] }}"
                                    class="service-type-card group p-4 border-2 border-slate-200 rounded-xl text-left
                                           transition-all duration-200 hover:shadow-lg hover:scale-105
                                           hover:border-{{ $color['bg'] }}-400 hover:bg-{{ $color['bg'] }}-50">
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">{{ $color['icon'] }}</span>
                                    <span class="font-semibold text-slate-700 group-hover:text-{{ $color['bg'] }}-700">
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
                    <input type="text"
                           name="subject"
                           required
                           class="w-full rounded-lg border-2 border-slate-200 text-sm p-3
                                  focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition"
                           placeholder="Ex: PDV da loja Morumbi não finaliza venda">
                    @error('subject')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ====================================================
                | DESCRIÇÃO
                ==================================================== -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                    <label class="font-semibold text-slate-700 mb-2 block">
                        📄 Descrição Detalhada
                    </label>

                    <div id="description-editor"
                         contenteditable="true"
                         class="w-full rounded-lg border-2 border-slate-200 text-sm p-4 min-h-[180px]
                                focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                                transition bg-slate-50">
                    </div>

                    <input type="hidden" name="description" id="description">

                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Você pode colar prints (Ctrl+V) diretamente aqui
                    </div>
                </div>

                <!-- ====================================================
                | ANEXOS
                ==================================================== -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-slate-100 p-6">
                    <label class="font-semibold text-slate-700 mb-2 block">
                        📎 Anexos Adicionais
                    </label>

                    <input type="file"
                           name="attachments[]"
                           multiple
                           class="block w-full text-sm text-slate-600
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 transition">
                </div>

                <!-- ====================================================
                | CRITICIDADE (CARDS GRANDES COLORIDOS)
                ==================================================== -->
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl shadow-lg border-2 border-orange-200 p-6">
                    <h3 class="font-bold text-slate-800 mb-2 text-lg flex items-center gap-2">
                        <span class="text-2xl">⚠️</span>
                        Qual o impacto no seu trabalho?
                    </h3>
                    <p class="text-sm text-slate-600 mb-4">Isso define a urgência do atendimento</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (\App\Enums\Criticality::cases() as $criticality)
                            @php
                                // Mapeia os valores do Enum (minúsculos) para configurações visuais
                                $configs = [
                                    'critical' => ['color' => 'red', 'icon' => '🔥', 'title' => 'CRÍTICO'],
                                    'high' => ['color' => 'orange', 'icon' => '⚡', 'title' => 'ALTO'],
                                    'medium' => ['color' => 'yellow', 'icon' => '⚠️', 'title' => 'MÉDIO'],
                                    'low' => ['color' => 'green', 'icon' => '✅', 'title' => 'BAIXO'],
                                ];
                                
                                // Tenta lowercase também
                                $value = strtolower($criticality->value);
                                $config = $configs[$value] ?? ['color' => 'slate', 'icon' => '📋', 'title' => strtoupper($criticality->value)];
                            @endphp

                            <button type="button"
                                    data-criticality="{{ $criticality->value }}"
                                    data-color="{{ $config['color'] }}"
                                    class="criticality-btn group p-5 border-2 border-slate-200 rounded-xl text-left
                                           transition-all duration-200 hover:shadow-xl hover:scale-105 bg-white">
                                
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-3xl">{{ $config['icon'] }}</span>
                                    <div>
                                        <span class="font-bold text-lg block
                                            {{ $config['color'] === 'red' ? 'text-red-700' : '' }}
                                            {{ $config['color'] === 'orange' ? 'text-orange-700' : '' }}
                                            {{ $config['color'] === 'yellow' ? 'text-yellow-700' : '' }}
                                            {{ $config['color'] === 'green' ? 'text-green-700' : '' }}">
                                            {{ $config['title'] }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    {{ $criticality->label() }}
                                </p>
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="criticality" id="criticality">

                    <p id="criticality-error" class="hidden text-sm text-red-700 mt-3 flex items-center gap-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Informe o impacto do problema para continuar
                    </p>
                </div>

                <!-- ====================================================
                | BOTÕES DE AÇÃO
                ==================================================== -->
                <div class="flex justify-end gap-4 pt-2">
                    <button type="reset"
                            class="px-6 py-3 border-2 border-slate-300 rounded-lg text-sm font-semibold
                                   hover:bg-slate-100 transition">
                        🔄 Limpar Formulário
                    </button>

                    <button type="submit"
                            id="submit-ticket"
                            disabled
                            class="px-10 py-4 rounded-lg text-base font-bold transition-all duration-200
                                   bg-slate-300 text-slate-500 cursor-not-allowed opacity-50">
                        🚀 Criar Chamado
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
                            <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

    const editor = document.getElementById('description-editor');
    const hiddenDescription = document.getElementById('description');
    const submitBtn = document.getElementById('submit-ticket');
    const slaTable = document.getElementById('sla-table');

    /* ============================================================
       SINCRONIZAÇÃO DO EDITOR
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
    function clearSelection(selector) {
        document.querySelectorAll(selector).forEach(el => {
            // Remove TODAS as classes de seleção
            el.classList.remove(
                'border-yellow-400', 'bg-yellow-100', 'ring-4', 'ring-yellow-300',
                'scale-105', 'shadow-2xl', 'shadow-xl', 'shadow-lg'
            );
        });
    }

    function validateForm() {
        const hasProduct = !!document.getElementById('product_id').value;
        const hasServiceType = !!document.getElementById('service_type').value;
        const hasCriticality = !!document.getElementById('criticality').value;

        if (hasProduct && hasServiceType && hasCriticality) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-slate-300', 'text-slate-500', 'cursor-not-allowed', 'opacity-50');
            submitBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'shadow-lg', 'hover:shadow-xl');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'shadow-lg', 'hover:shadow-xl');
            submitBtn.classList.add('bg-slate-300', 'text-slate-500', 'cursor-not-allowed', 'opacity-50');
        }
    }

    /* ============================================================
       PRODUTO (SELEÇÃO AMARELA)
    ============================================================ */
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('product_id').value = card.dataset.productId;
            
            clearSelection('.product-card');
            // Aplica estilo amarelo
            card.classList.add('border-yellow-400', 'bg-yellow-100', 'ring-4', 'ring-yellow-300', 'shadow-xl');

            // Atualiza tabela de SLA
            const slaData = JSON.parse(card.dataset.productSla);
            updateSlaTable(slaData);

            validateForm();
        });
    });

    /* ============================================================
       ATUALIZA TABELA DE SLA
    ============================================================ */
    function updateSlaTable(slaData) {
        const priorities = {
            critical: { label: 'Crítica', color: 'red', icon: '🔴' },
            high: { label: 'Alta', color: 'orange', icon: '🟠' },
            medium: { label: 'Média', color: 'yellow', icon: '🟡' },
            low: { label: 'Baixa', color: 'green', icon: '🟢' }
        };

        let html = '<div class="space-y-3">';
        
        for (const [key, data] of Object.entries(slaData)) {
            const priority = priorities[key];
            html += `
                <div class="p-3 rounded-lg border-2 border-${priority.color}-200 bg-${priority.color}-50">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">${priority.icon}</span>
                            <span class="font-bold text-sm text-${priority.color}-700">${priority.label}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white rounded p-2">
                            <p class="text-slate-500 font-medium">Resposta</p>
                            <p class="font-bold text-${priority.color}-600">${data.response_hours}h</p>
                        </div>
                        <div class="bg-white rounded p-2">
                            <p class="text-slate-500 font-medium">Resolução</p>
                            <p class="font-bold text-${priority.color}-600">${data.resolution_hours}h</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        slaTable.innerHTML = html;
    }

    /* ============================================================
       TIPO DE OCORRÊNCIA (SELEÇÃO AMARELA)
    ============================================================ */
    document.querySelectorAll('.service-type-card').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('service_type').value = card.dataset.serviceType;
            
            clearSelection('.service-type-card');
            // Aplica estilo amarelo
            card.classList.add('border-yellow-400', 'bg-yellow-100', 'ring-4', 'ring-yellow-300', 'shadow-lg');
            
            validateForm();
        });
    });

    /* ============================================================
       CRITICIDADE (SELEÇÃO AMARELA)
    ============================================================ */
    document.querySelectorAll('.criticality-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('criticality').value = btn.dataset.criticality;
            
            clearSelection('.criticality-btn');
            // Aplica estilo amarelo
            btn.classList.add('border-yellow-400', 'bg-yellow-100', 'ring-4', 'ring-yellow-300', 'scale-105', 'shadow-2xl');
            
            document.getElementById('criticality-error').classList.add('hidden');
            validateForm();
        });
    });

});
</script>
@endsection