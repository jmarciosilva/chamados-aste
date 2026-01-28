@extends('layouts.user')

@section('content')
    <!-- ============================================================
        | HERO
        |============================================================ -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-xl p-8 text-white mb-8">

        <h1 class="text-2xl font-semibold mb-2">
            Olá, {{ auth()->user()->name }}! Como podemos ajudar hoje?
        </h1>

        <p class="text-sm text-blue-100 mb-4">
            Pesquise na base de conhecimento ou acompanhe seus chamados.
        </p>

        <div class="flex flex-col md:flex-row gap-3">
            <input type="text" placeholder="Buscar artigos (em breve)" class="flex-1 rounded-lg px-4 py-2 text-slate-800"
                disabled>

            <a href="{{ route('user.tickets.create') }}"
                class="bg-white text-blue-700 px-5 py-2 rounded-lg font-medium hover:bg-blue-50 text-center">
                Abrir Chamado
            </a>
        </div>
    </div>

    <!-- ============================================================
        | KPIs DO USUÁRIO
        |============================================================ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        <!-- Chamados abertos -->
        <div class="bg-white rounded-lg p-4 shadow border-l-4 border-blue-500">
            <p class="text-xs text-slate-500">Chamados Abertos</p>
            <p class="text-2xl font-bold">{{ $openTickets }}</p>
        </div>

        <!-- Em atendimento -->
        <div class="bg-white rounded-lg p-4 shadow border-l-4 border-yellow-500">
            <p class="text-xs text-slate-500">Em Atendimento</p>
            <p class="text-2xl font-bold">{{ $waitingTickets }}</p>
        </div>

        <!-- Resolvidos no mês -->
        <div class="bg-white rounded-lg p-4 shadow border-l-4 border-green-500">
            <p class="text-xs text-slate-500">Resolvidos no Mês</p>
            <p class="text-2xl font-bold">{{ $resolvedThisMonth }}</p>
        </div>

    </div>

    <!-- ============================================================
        | GRID PRINCIPAL
        |============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ========================================================
            | CHAMADOS RECENTES
            |======================================================== -->
        <div class="lg:col-span-2">

            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold text-slate-800">
                    Meus Chamados Recentes
                </h2>

                <a href="{{ route('user.tickets.index') }}" class="text-sm text-blue-600 hover:underline">
                    Ver todos
                </a>
            </div>

            <div class="space-y-3">

                @forelse($recentTickets as $ticket)
                    @php
                        /**
                         * --------------------------------------------------
                         * CORES DE STATUS (UI)
                         * Tradução vem do accessor: status_label
                         * --------------------------------------------------
                         */
                        $statusColor = match ($ticket->status) {
                            'open' => 'bg-blue-100 text-blue-700',
                            'in_progress' => 'bg-yellow-100 text-yellow-700',
                            'resolved' => 'bg-green-100 text-green-700',
                            'closed' => 'bg-slate-200 text-slate-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp

                    <a href="{{ route('user.tickets.show', $ticket) }}"
                        class="block bg-white rounded-lg p-4 shadow hover:shadow-md transition">

                        <div class="flex justify-between items-center">

                            <!-- Informações principais -->
                            <div>
                                <p class="font-medium text-slate-800">
                                    <span class="text-slate-500 text-sm">
                                        {{ $ticket->code }}
                                    </span>
                                    <span class="mx-1">–</span>
                                    {{ $ticket->subject }}
                                </p>


                                {{-- <p class="text-xs text-slate-500">
                                    {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    · Prioridade: {{ $ticket->priority_label }}
                                </p> --}}
                            </div>

                            <!-- Status traduzido -->
                            <span class="text-xs px-2 py-1 rounded {{ $statusColor }}">
                                {{ $ticket->status_label }}
                            </span>

                        </div>
                    </a>

                @empty

                    <div class="bg-white rounded-lg p-6 shadow text-center text-slate-500">
                        Você ainda não possui chamados abertos.
                    </div>
                @endforelse

            </div>
        </div>

        <!-- ========================================================
            | LADO DIREITO
            |======================================================== -->
        <div class="space-y-6">

            <!-- Artigos (MVP) -->
            <div class="bg-white rounded-lg p-4 shadow">
                <h3 class="font-semibold text-slate-800 mb-3">
                    Artigos Recomendados
                </h3>

                <ul class="space-y-2 text-sm text-blue-600">
                    <li>Como reiniciar o PDV corretamente</li>
                    <li>Problemas comuns de impressora térmica</li>
                    <li>Erro de login mais frequente</li>
                </ul>
            </div>

            <!-- Ajuda urgente -->
            <div class="bg-gradient-to-r from-blue-800 to-indigo-900 rounded-lg p-5 text-white">
                <h3 class="font-semibold mb-2">
                    Problema crítico?
                </h3>

                <p class="text-xs text-blue-100 mb-3">
                    Para lojas paradas, acione o canal de emergência.
                </p>

                <a href="#" class="block text-center bg-white text-blue-700 py-2 rounded font-medium">
                    Contato Emergencial
                </a>
            </div>

        </div>

    </div>
@endsection
