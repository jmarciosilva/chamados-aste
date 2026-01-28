<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Help Desk · Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 min-h-screen">

    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 h-14 flex justify-between items-center">

            <div class="flex items-center gap-2 font-semibold">
                <span class="bg-blue-600 text-white w-8 h-8 rounded flex items-center justify-center">GA</span>
                Grupo Aste
            </div>

            <!-- Menu -->
            <nav class="hidden md:flex items-center gap-6 text-sm text-slate-600">
                <a href="{{ route('user.home') }}" class="font-medium text-blue-600">Dashboard</a>
                <a href="{{ route('user.tickets.create') }}" class="hover:text-blue-600">Abrir Chamado</a>
                <a href="{{ route('user.tickets.index') }}" class="hover:text-blue-600">Meus Chamados</a>
                <a href="{{ route('user.knowledge-base') }}" class="hover:text-blue-600">Base de Conhecimento</a>
            </nav>

            <!-- CONTROLE DE MODO -->
            <div class="flex items-center gap-2 text-xs">

                <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                    Modo: Usuário
                </span>

                @if (auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('switch.mode', 'admin') }}">
                        @csrf
                        <button class="px-2 py-1 rounded bg-slate-200 hover:bg-slate-300">
                            Admin
                        </button>
                    </form>
                @endif

                @if (in_array(auth()->user()->role, ['admin', 'agent']))
                    <form method="POST" action="{{ route('switch.mode', 'agent') }}">
                        @csrf
                        <button class="px-2 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">
                            Operador
                        </button>
                    </form>
                @endif

                {{-- <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-500 hover:text-red-700">Sair</button>
                </form> --}}

                <!-- Usuário -->
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-slate-600">{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-500 hover:text-red-700 text-xs">
                            Sair
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </header>

    <!-- ============================================================
    | CONTEÚDO
    |============================================================ -->

    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
    </main>


    <!-- ============================================================
    | footer
    |============================================================ -->
    <footer class="text-center text-xs text-slate-400 mt-12">
        © 2026 Grupo Aste
    </footer>

    <!-- ============================================================
    | SCRIPTS ESPECÍFICOS DAS VIEWS
    |============================================================ -->
    @yield('scripts')
</body>

</html>
