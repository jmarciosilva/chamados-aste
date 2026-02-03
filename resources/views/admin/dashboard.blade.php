@extends('layouts.admin')

@section('title', 'Painel do Gestor')
@section('subtitle', 'Visão estratégica da operação de suporte e indicadores de performance.')

@section('content')

    {{-- ============================================================
    | AÇÕES DO ADMINISTRADOR
    |============================================================ --}}
    @if (auth()->user()->role === 'admin')
        <div class="flex justify-end mb-6">
            <a href="{{ route('agent.queue') }}"
                class="inline-flex items-center gap-2 px-4 py-2
                  bg-blue-600 text-white text-sm font-medium
                  rounded-lg hover:bg-blue-700 transition
                  shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6H7v-2a4 4 0 014-4h1m4-4a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>

                Atuar como Operador
            </a>
        </div>
    @endif


    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-xs text-slate-500">Volume Total</p>
            <p class="text-2xl font-bold">1.248</p>
            <p class="text-xs text-green-600">+12%</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-xs text-slate-500">SLA Global</p>
            <p class="text-2xl font-bold">94.2%</p>
            <p class="text-xs text-red-600">-0.8%</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
            <p class="text-xs text-slate-500">Tempo Médio (TMA)</p>
            <p class="text-2xl font-bold">4h 15m</p>
            <p class="text-xs text-green-600">-15m</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
            <p class="text-xs text-slate-500">Satisfação (CSAT)</p>
            <p class="text-2xl font-bold">4.8 / 5</p>
            <p class="text-xs text-green-600">+0.2</p>
        </div>

    </div>

    


    <!-- GRÁFICOS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Evolução -->
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-2">Evolução de Chamados</h3>
            <div class="h-56 flex items-center justify-center text-slate-400">
                <canvas id="lineChart" height="200"></canvas>
            </div>
        </div>

        <!-- Chamados por Área -->
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-2">Chamados por Área</h3>
            <div class="h-56 flex items-center justify-center text-slate-400">
                <canvas id="pieChart" height="200"></canvas>
            </div>
        </div>

    </div>

    <!-- SLA + Top Motivos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- SLA Semanal -->
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-4">Cumprimento de SLA (Semanal)</h3>

            @foreach (['Seg', 'Ter', 'Qua', 'Qui', 'Sex'] as $dia)
                <div class="mb-2">
                    <div class="flex justify-between text-xs mb-1">
                        <span>{{ $dia }}</span>
                        <span>85%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width:85%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Top Motivos -->
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-4">Top Motivos de Chamados</h3>

            <ul class="space-y-2 text-sm">
                <li class="flex justify-between">
                    <span>Erro de Acesso</span>
                    <span>145</span>
                </li>
                <li class="flex justify-between">
                    <span>Impressora</span>
                    <span>82</span>
                </li>
                <li class="flex justify-between">
                    <span>Lentidão ERP</span>
                    <span>64</span>
                </li>
                <li class="flex justify-between">
                    <span>Internet / VPN</span>
                    <span>45</span>
                </li>
                <li class="flex justify-between">
                    <span>Solicitação Equipamento</span>
                    <span>32</span>
                </li>
            </ul>
        </div>

    </div>

@endsection
