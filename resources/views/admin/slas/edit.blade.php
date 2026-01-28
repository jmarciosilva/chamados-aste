@extends('layouts.admin')

@section('title', 'Editar SLA')
@section('subtitle', 'Atualização de regra de nível de serviço')

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
                Atualize os tempos de resposta e resolução conforme a prioridade.
            </p>
        </div>

        <!-- ========================================================
            | FORMULÁRIO
            ======================================================== -->
        <form method="POST"
              action="{{ route('admin.slas.update', $sla) }}"
              class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- ====================================================
                | NOME DA REGRA
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Nome da Regra
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $sla->name) }}"
                       required
                       class="mt-1 w-full border rounded-lg px-3 py-2
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- ====================================================
                | PRIORIDADE
                | value = valor interno do sistema
                | label = texto amigável
                ==================================================== -->
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Prioridade
                </label>

                <select name="priority"
                        required
                        class="mt-1 w-full border rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    @foreach ($priorities as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('priority', $sla->priority) === $value)>
                            {{ $label }}
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
                       value="{{ old('response_time_hours', $sla->response_time_hours) }}"
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
                       value="{{ old('resolution_time_hours', $sla->resolution_time_hours) }}"
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
                       @checked(old('is_active', $sla->is_active))
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
                    Atualizar SLA
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
