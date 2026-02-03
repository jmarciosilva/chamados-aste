<div class="space-y-6">

    {{-- ============================================================
    | INFORMAÇÕES BÁSICAS DO PRODUTO
    ============================================================ --}}
    <div class="border-b pb-4">
        <h3 class="text-sm font-semibold text-slate-700 mb-3">
            Informações Básicas
        </h3>

        <div class="space-y-4">

            {{-- Nome --}}
            <div>
                <label class="text-xs text-slate-500 font-medium">
                    Nome do Serviço / Solução *
                </label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                    class="w-full rounded border-slate-300 text-sm mt-1
                              focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="text-xs text-slate-500 font-medium">
                    Descrição
                </label>
                <textarea name="description" rows="3"
                    class="w-full rounded border-slate-300 text-sm mt-1
                                 focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ativo --}}
            <div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-slate-700">
                        Serviço Ativo
                    </span>
                </label>
            </div>

        </div>
    </div>

    {{-- ============================================================
    | PERGUNTA DE IMPACTO (CLASSIFICAÇÃO DE CRITICIDADE)
    ============================================================ --}}
    <div class="border-b pb-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-2">
            Classificação de Impacto do Chamado
        </h3>

        <p class="text-xs text-slate-500 mb-4">
            Esta pergunta será exibida ao usuário no momento da abertura do chamado
            e define a criticidade inicial.
        </p>

        {{-- Pergunta --}}
        <div class="mb-4">
            <label class="text-xs font-medium text-slate-600">
                Pergunta *
            </label>
            <input type="text" name="impact[question]"
                value="{{ old('impact.question', $product->impactQuestion->question ?? '') }}" required
                placeholder="Ex: Qual o impacto deste problema no seu trabalho?"
                class="w-full mt-1 rounded border-slate-300 text-sm
                          focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Respostas --}}
        @php
            $impactPriorities = [
                'low' => 'Baixo',
                'medium' => 'Médio',
                'high' => 'Alto',
                'critical' => 'Crítico',
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($impactPriorities as $key => $label)
                @php
                    $existingAnswer = null;

                    if (isset($product) && $product->relationLoaded('impactQuestion')) {
                        $existingAnswer = $product->impactQuestion?->answers?->firstWhere('priority', $key)?->label;
                    }
                @endphp

                <div class="bg-slate-50 border rounded-lg p-3">
                    <label class="text-xs font-medium text-slate-700">
                        Resposta – {{ $label }}
                    </label>

                    <input type="text" name="impact[answers][{{ $key }}]"
                        value="{{ old("impact.answers.$key", $existingAnswer) }}" required
                        placeholder="Descrição do impacto {{ strtolower($label) }}"
                        class="w-full mt-1 rounded border-slate-300 text-sm">
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================
    | CONFIGURAÇÃO DE SLAs POR PRIORIDADE
    ============================================================ --}}
    <div>
        <h3 class="text-sm font-semibold text-slate-700 mb-1">
            Configuração de SLAs
        </h3>

        <p class="text-xs text-slate-500 mb-4">
            Defina os tempos de resposta e resolução em horas para cada prioridade
        </p>

        @php
            $slaConfig = old(
                'sla',
                isset($product) && method_exists($product, 'getSlaConfigFormatted')
                    ? $product->getSlaConfigFormatted()
                    : [
                        'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                        'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                        'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                        'critical' => ['response_hours' => 2, 'resolution_hours' => 4],
                    ],
            );

            $slaPriorities = [
                'critical' => ['label' => 'Crítica', 'color' => 'red', 'icon' => '🔴'],
                'high' => ['label' => 'Alta', 'color' => 'orange', 'icon' => '🟠'],
                'medium' => ['label' => 'Média', 'color' => 'yellow', 'icon' => '🟡'],
                'low' => ['label' => 'Baixa', 'color' => 'green', 'icon' => '🟢'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach ($slaPriorities as $key => $priority)
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">

                    {{-- Cabeçalho --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">{{ $priority['icon'] }}</span>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                                     bg-{{ $priority['color'] }}-100 text-{{ $priority['color'] }}-700">
                            {{ $priority['label'] }}
                        </span>
                    </div>

                    {{-- Campos --}}
                    <div class="grid grid-cols-2 gap-4">

                        {{-- Resposta --}}
                        <div>
                            <label class="text-xs text-slate-600 font-medium">
                                Tempo de Resposta *
                            </label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="number" name="sla[{{ $key }}][response_hours]"
                                    value="{{ $slaConfig[$key]['response_hours'] }}" min="1" required
                                    class="w-full rounded border-slate-300 text-sm
                                              focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <span class="text-xs text-slate-500">horas</span>
                            </div>
                        </div>

                        {{-- Resolução --}}
                        <div>
                            <label class="text-xs text-slate-600 font-medium">
                                Tempo de Resolução *
                            </label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="number" name="sla[{{ $key }}][resolution_hours]"
                                    value="{{ $slaConfig[$key]['resolution_hours'] }}" min="1" required
                                    class="w-full rounded border-slate-300 text-sm
                                              focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <span class="text-xs text-slate-500">horas</span>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Observação --}}
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex gap-2">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs text-blue-800">
                    <strong>Importante:</strong> Os tempos são contados em horas corridas.
                    O SLA pode ser pausado quando aguardando resposta do usuário.
                </div>
            </div>
        </div>
    </div>

</div>
