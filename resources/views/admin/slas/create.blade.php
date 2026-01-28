@extends('layouts.admin')

@section('title', 'Criar SLA')
@section('subtitle', 'Cadastro de regra de nível de serviço por produto')

@section('content')

<div class="max-w-5xl mx-auto">

    <!-- ============================================================
        | CARD
        ============================================================ -->
    <div class="bg-white rounded-xl shadow-sm border">

        <!-- ========================================================
            | CABEÇALHO DO CARD
            ======================================================== -->
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-slate-800">
                Dados do SLA
            </h2>
            <p class="text-sm text-slate-500">
                Defina os tempos de resposta e resolução por produto,
                tipo de serviço e prioridade.
            </p>
        </div>

        <!-- ========================================================
            | FORMULÁRIO
            ======================================================== -->
        <form method="POST"
              action="{{ route('admin.slas.store') }}"
              class="p-6 space-y-6">
            @csrf

            <!-- ====================================================
                | PRODUTO
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Produto
                </label>

                <select name="product_id"
                        required
                        class="mt-1 w-full border rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione um produto</option>

                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ====================================================
                | NOME DA REGRA
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Nome da Regra
                </label>

                <input type="text"
                       name="name"
                       required
                       placeholder="Ex: SLA SIGE - Incidente - Alta"
                       class="mt-1 w-full border rounded-lg px-3 py-2
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- ====================================================
                | TIPO DE SERVIÇO (ENUM)
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Tipo de Serviço
                </label>

                <select name="service_type"
                        required
                        class="mt-1 w-full border rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach (\App\Enums\ServiceType::cases() as $type)
                        <option value="{{ $type->value }}">
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ====================================================
                | PRIORIDADE (ENUM)
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Prioridade
                </label>

                <select name="priority"
                        required
                        class="mt-1 w-full border rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach (\App\Enums\Priority::cases() as $priority)
                        <option value="{{ $priority->value }}">
                            {{ $priority->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ====================================================
                | TEMPO DE RESPOSTA
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Tempo de Resposta (horas)
                </label>

                <input type="number"
                       name="response_time_hours"
                       min="1"
                       required
                       class="mt-1 w-full border rounded-lg px-3 py-2
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- ====================================================
                | TEMPO DE RESOLUÇÃO
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Tempo de Resolução (horas)
                </label>

                <input type="number"
                       name="resolution_time_hours"
                       min="1"
                       required
                       class="mt-1 w-full border rounded-lg px-3 py-2
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- ====================================================
                | STATUS
                ==================================================== -->
            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       checked
                       class="rounded border-slate-300 text-blue-600
                              focus:ring-blue-500">

                <span class="text-sm text-slate-700">
                    SLA ativo
                </span>
            </div>

            <!-- ====================================================
                | AÇÕES
                ==================================================== -->
            <div class="flex justify-end gap-3 pt-6 border-t">

                <a href="{{ route('admin.slas.index') }}"
                   class="px-4 py-2 text-sm rounded-lg border
                          text-slate-600 hover:bg-slate-100 transition">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-5 py-2 text-sm rounded-lg
                               bg-blue-600 text-white
                               hover:bg-blue-700 transition">
                    Salvar SLA
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
