@extends('layouts.app')

@section('content')
    {{-- ============================================================
| DASHBOARD DO OPERADOR (AGENT)
|------------------------------------------------------------
| - Layout operacional
| - Sidebar colapsável
| - Visual alinhado ao Admin Console
| - Dados mockados (fase de UI)
============================================================ --}}

    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen bg-slate-50">

        {{-- SIDEBAR --}}
        @include('agent.partials.sidebar')

        {{-- ÁREA PRINCIPAL --}}
        <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">

            {{-- HEADER --}}
            @include('agent.partials.header')

            {{-- CONTEÚDO --}}
            <main class="p-8 space-y-8">

                {{-- TÍTULO --}}
                <header>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Dashboard Operacional
                    </h1>
                    <p class="text-sm text-slate-500">
                        Visão geral da sua fila e métricas de desempenho.
                    </p>
                </header>

                {{-- KPIs --}}
                @php
                    $kpis = [
                        ['Chamados na Fila', 12],
                        ['SLA em Risco', 3],
                        ['Resolvidos Hoje', 8],
                        ['CSAT Médio', '4.8'],
                    ];
                @endphp

                <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @foreach ($kpis as [$title, $value])
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <p class="text-sm text-slate-500">{{ $title }}</p>
                            <p class="mt-2 text-3xl font-semibold text-blue-600">{{ $value }}</p>
                        </div>
                    @endforeach
                </section>

                {{-- ÁREA PRINCIPAL --}}
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- CHAMADOS PRIORITÁRIOS --}}
                    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-slate-800">
                                Chamados Prioritários
                            </h2>
                            <a href="{{ route('agent.queue') }}"
                                class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Ver fila completa
                            </a>
                        </div>

                        @php
                            $tickets = [
                                ['PDV 03 travado - Morumbi', '15 min'],
                                ['Queda de link - CD Cajamar', '1h 30m'],
                                ['Erro no ERP - Vendas', '3h 45m'],
                                ['Novo monitor - RH', '22h'],
                            ];
                        @endphp

                        <div class="space-y-3 text-sm">
                            @foreach ($tickets as [$title, $time])
                                <div
                                    class="flex justify-between items-center border border-slate-200 rounded-lg p-4 hover:bg-blue-50 transition">
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $title }}</p>
                                        <p class="text-xs text-slate-500">Em andamento</p>
                                    </div>
                                    <span class="font-semibold text-blue-600">{{ $time }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- COLUNA DIREITA --}}
                    <aside class="space-y-6">

                        {{-- STATUS DA EQUIPE --}}
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <h2 class="font-semibold text-slate-800 mb-4">
                                Status da Equipe
                            </h2>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span>Carlos Mendes</span>
                                    <span class="flex items-center gap-1 text-green-600">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span> Online
                                    </span>
                                </div>

                                <div class="flex justify-between">
                                    <span>Ana Souza</span>
                                    <span class="flex items-center gap-1 text-blue-600">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span> Em atendimento
                                    </span>
                                </div>

                                <div class="flex justify-between">
                                    <span>Roberto Lima</span>
                                    <span class="flex items-center gap-1 text-slate-400">
                                        <span class="w-2 h-2 bg-slate-400 rounded-full"></span> Offline
                                    </span>
                                </div>

                                <div class="flex justify-between font-semibold">
                                    <span>Você</span>
                                    <span class="flex items-center gap-1 text-green-600">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span> Online
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- AVISOS --}}
                        <div class="bg-blue-900 rounded-xl p-5">
                            <h2 class="font-semibold text-white mb-4">
                                Avisos do Sistema
                            </h2>

                            <div class="space-y-4 text-sm">
                                <div class="bg-blue-800/80 p-4 rounded-lg">
                                    <strong class="text-white">Manutenção Programada</strong>
                                    <p class="text-blue-100 mt-1">
                                        Servidor ficará indisponível hoje às 22h.
                                    </p>
                                </div>

                                <div class="bg-blue-800/80 p-4 rounded-lg">
                                    <strong class="text-white">Novo Procedimento</strong>
                                    <p class="text-blue-100 mt-1">
                                        Atualizado fluxo de aprovação de licenças.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </aside>
                </section>

            </main>
        </div>
    </div>
@endsection
