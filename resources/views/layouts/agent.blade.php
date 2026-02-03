<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Operador · Service Desk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 min-h-screen">

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r hidden md:block">
            <div class="px-6 py-4 border-b font-semibold text-blue-700">
                Grupo Aste · Operador
            </div>

            <nav class="px-4 py-4 text-sm space-y-1">
                <a href="{{ route('agent.queue') }}" class="block px-3 py-2 rounded hover:bg-slate-100">
                    📥 Chamados Ativos
                </a>
                <a href="{{ route('agent.queue.closed') }}" class="block px-3 py-2 rounded hover:bg-slate-100">
                    📁 Chamados Fechados
                </a>

                <!-- ================= ADMINISTRAÇÃO OPERACIONAL ================= -->
                @if (auth()->user()->agent_type === 'operator')
                    <p class="px-3 pt-4 pb-1 text-xs text-slate-400 uppercase tracking-wide">
                        Administração
                    </p>

                    <a href="{{ route('agent.support-groups.index') }}"
                        class="block px-3 py-2 rounded hover:bg-slate-100">
                        🧩 Grupos de Atendimento
                    </a>
                @endif
            </nav>
            <!-- ========================================================
        | USUÁRIO LOGADO
        |======================================================== -->
            <div class="border-t px-4 py-4 text-sm">
                <div class="font-medium">
                    {{ auth()->user()->name }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600 text-xs mt-2 hover:underline">
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        <!-- CONTEÚDO -->
        <div class="flex-1 flex flex-col">

            <header class="bg-white border-b px-6 py-4 flex justify-between">

                <span class="text-sm text-slate-600">
                    Modo: Operador
                </span>

                <!-- VOLTAR AO ADMIN -->
                @if (auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('switch.mode', 'admin') }}">
                        @csrf
                        <button class="text-xs px-3 py-1 rounded bg-slate-200 hover:bg-slate-300">
                            Voltar ao Admin
                        </button>
                    </form>
                @endif
            </header>

            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>
    </div>

     <!-- ============================================================
    | SCRIPTS ESPECÍFICOS DAS VIEWS
    |============================================================ -->
    @yield('scripts')
    @stack('scripts')

</body>

</html>
