@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- ============================================================
        | CABEÇALHO
        ============================================================ -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold">Configuração de SLAs</h1>
            <p class="text-sm text-slate-500">
                Defina tempos de resposta e resolução por produto, tipo de serviço e prioridade.
            </p>
        </div>

        <a href="{{ route('admin.slas.create') }}"
           class="inline-flex items-center gap-2
                  bg-blue-600 hover:bg-blue-700
                  text-white px-4 py-2 rounded-md
                  text-sm font-medium transition">
            ➕ Nova Regra SLA
        </a>
    </div>

    <!-- ============================================================
        | CARDS DE MÉTRICAS (PLACEHOLDER)
        | Preparados para dashboard executivo futuramente
        ============================================================ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white rounded-lg border shadow-sm p-4">
            <div class="text-sm text-slate-500">SLA Global (Mês)</div>
            <div class="mt-2 text-2xl font-semibold text-slate-800">94.2%</div>
            <div class="text-xs text-green-600 mt-1">+2.1% vs mês anterior</div>
        </div>

        <div class="bg-white rounded-lg border shadow-sm p-4">
            <div class="text-sm text-slate-500">Tempo Médio de Resposta</div>
            <div class="mt-2 text-2xl font-semibold text-slate-800">1.8h</div>
            <div class="text-xs text-slate-500 mt-1">-0.5h vs mês anterior</div>
        </div>

        <div class="bg-white rounded-lg border shadow-sm p-4">
            <div class="text-sm text-slate-500">Tickets Fora do SLA</div>
            <div class="mt-2 text-2xl font-semibold text-red-600">12</div>
            <div class="text-xs text-red-500 mt-1">Atenção necessária</div>
        </div>

    </div>

    <!-- ============================================================
        | FEEDBACK
        ============================================================ -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- ============================================================
        | TABELA DE REGRAS DE SLA
        ============================================================ -->
    <div class="bg-white rounded-lg border shadow-sm">

        <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-slate-800">
                Regras de Nível de Serviço
            </h2>
            <p class="text-xs text-slate-500">
                Estas regras são aplicadas automaticamente aos novos chamados.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-6 py-3 text-left">Regra</th>
                        <th class="px-6 py-3 text-left">Produto</th>
                        <th class="px-6 py-3 text-left">Prioridade</th>
                        <th class="px-6 py-3 text-left">Resposta</th>
                        <th class="px-6 py-3 text-left">Resolução</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($slas as $sla)
                        <tr class="hover:bg-slate-50">

                            <!-- Nome -->
                            <td class="px-6 py-3 font-medium text-slate-800">
                                {{ $sla->service_type }}
                            </td>

                            <!-- Produto -->
                            <td class="px-6 py-3">
                                {{ $sla->product?->name ?? '—' }}
                            </td>

                            <!-- Prioridade (ENUM) -->
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs
                                    {{ $sla->priority->color() }}">
                                    {{ $sla->priority->label() }}
                                </span>
                            </td>

                            <!-- Tempo de Resposta -->
                            <td class="px-6 py-3">
                                {{ $sla->response_time_hours }}h
                            </td>

                            <!-- Tempo de Resolução -->
                            <td class="px-6 py-3">
                                {{ $sla->resolution_time_hours }}h
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-3">
                                @if ($sla->is_active)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs
                                                 bg-blue-100 text-blue-700">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs
                                                 bg-slate-200 text-slate-600">
                                        Inativo
                                    </span>
                                @endif
                            </td>

                            <!-- Ações -->
                            <td class="px-6 py-3 text-right space-x-2">

                                <a href="{{ route('admin.slas.edit', $sla) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Editar
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.slas.toggle-status', $sla) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                            class="text-sm text-slate-600 hover:underline">
                                        {{ $sla->is_active ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="px-6 py-6 text-center text-slate-500">
                                Nenhuma regra de SLA cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
