<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">

    <!-- ============================================================
    | TÍTULO PADRÃO
    ============================================================ -->
    <title>Admin · Help Desk</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Assets (Tailwind + Alpine via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- ================================================================
| CONTROLE DE ESTADO (ALPINE)
| sidebarOpen → menu expandido/recolhido
================================================================ --}}

<body class="bg-slate-100" x-data="{ sidebarOpen: true }">

    <div class="flex min-h-screen">

        {{-- ============================================================
    | SIDEBAR ADMIN (SOMENTE EM mode:admin)
    ============================================================ --}}
        @if (session('mode', 'admin') === 'admin')

            <aside
                class="bg-gradient-to-b from-slate-900 to-slate-800 text-white flex flex-col
               transition-all duration-300"
                :class="sidebarOpen ? 'w-64' : 'w-20'">

                <!-- LOGO -->
                <div class="h-16 flex items-center px-4 font-bold border-b border-slate-700">
                    <span class="bg-blue-600 w-9 h-9 rounded-lg flex items-center justify-center mr-2">
                        GA
                    </span>
                    <span x-show="sidebarOpen" x-transition>
                        Admin Console
                    </span>
                </div>

                <!-- ========================================================
        | MENU PRINCIPAL
        ======================================================== -->
                <nav class="flex-1 px-3 py-6 space-y-1 text-sm">

                    <!-- DASHBOARD -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
               {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        📊 <span x-show="sidebarOpen">Visão Geral</span>
                    </a>

                    <!-- SLA -->
                    {{-- <a href="{{ route('admin.slas.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
               {{ request()->routeIs('admin.slas.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        ⏱️ <span x-show="sidebarOpen">Configuração de SLAs</span> --}}
                    </a>

                    <!-- PRODUTOS -->
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
                {{ request()->routeIs('admin.products.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        📦 <span x-show="sidebarOpen">Serviços / Soluções</span>
                    </a>


                    <!-- ====================================================
            | CATEGORIAS DE PROBLEMAS (NOVO CRUD)
            | Base para:
            | - Abertura de chamados
            | - Prioridade automática
            | - Associação com SLA / Produto
            ==================================================== -->
                    {{-- <a href="{{ route('admin.problem-categories.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
               {{ request()->routeIs('admin.problem-categories.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        🧩 <span x-show="sidebarOpen">Categorias de Problemas</span>
                    </a> --}}

                    <!-- DEPARTAMENTOS -->
                    <a href="{{ route('admin.departments.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
                {{ request()->routeIs('admin.departments.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        🏢 <span x-show="sidebarOpen">Departamentos</span>
                    </a>

                    <!-- ====================================================
                    | GRUPOS DE ATENDIMENTO
                    | ITIL: Service Desk / Support Teams
                    ==================================================== -->
                    {{-- <a href="{{ route('admin.support-groups.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
                {{ request()->routeIs('admin.support-groups.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        👥 <span x-show="sidebarOpen">Grupos de Atendimento</span>
                    </a> --}}
                    <div x-data="{ open: {{ request()->routeIs('admin.support-groups.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-md
                                {{ request()->routeIs('admin.support-groups.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">

                            <div class="flex items-center gap-3">
                                👥 <span x-show="sidebarOpen">Grupos de Atendimento</span>
                            </div>

                            <span x-show="sidebarOpen" class="text-xs">▾</span>
                        </button>

                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('admin.support-groups.index') }}"
                                class="block px-3 py-1.5 rounded text-xs hover:bg-slate-700">
                                📋 Listar Grupos
                            </a>

                            {{-- <span class="block px-3 py-1.5 text-xs text-slate-400">
                                ➕ Gerenciar Técnicos
                            </span> --}}
                        </div>
                    </div>



                    <!-- USUÁRIOS -->
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
                            {{ request()->routeIs('admin.users.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        👤 <span x-show="sidebarOpen">Usuários</span>
                    </a>

                    <a href="{{ route('admin.queue') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md
                            {{ request()->routeIs('admin.queue') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                        👁️ <span x-show="sidebarOpen">Fila de Chamados</span>
                    </a>




                    {{-- ========================================================
            | SWITCH DE MODO (ADMIN)
            ======================================================== --}}
                    <div class="pt-6 space-y-2">

                        <p class="text-xs text-slate-400 px-3 uppercase">
                            Modo de Acesso
                        </p>

                        @foreach (['admin' => 'Admin', 'agent' => 'Operador', 'user' => 'Usuário'] as $mode => $label)
                            <form method="POST" action="{{ route('switch.mode', $mode) }}">
                                @csrf
                                <button
                                    class="w-full text-left px-3 py-1 rounded text-xs
                            {{ session('mode') === $mode ? 'bg-blue-600 text-white' : 'bg-slate-700 hover:bg-slate-600' }}">
                                    {{ $label }}
                                </button>
                            </form>
                        @endforeach

                    </div>
                </nav>

                <!-- ========================================================
        | USUÁRIO LOGADO
        ======================================================== -->
                <div class="p-4 border-t border-slate-700 text-xs">
                    <div>{{ auth()->user()->name }}</div>
                    <div class="text-slate-400">{{ auth()->user()->email }}</div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button class="text-red-400 hover:text-red-300">
                            Sair
                        </button>
                    </form>
                </div>

            </aside>
        @endif

        {{-- ============================================================
    | CONTEÚDO PRINCIPAL
    ============================================================ --}}
        <div class="flex-1 flex flex-col">

            <!-- HEADER -->
            <header class="h-16 bg-white border-b flex items-center justify-between px-6">

                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded hover:bg-slate-100">
                        ☰
                    </button>

                    <div>
                        <h1 class="text-lg font-semibold">@yield('title')</h1>
                        <p class="text-xs text-slate-500">@yield('subtitle')</p>
                    </div>
                </div>

                <!-- INDICADOR DE MODO -->
                <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">
                    Modo: Admin
                </span>
            </header>

            <!-- CONTEÚDO -->
            <main class="p-6">
                @yield('content')
            </main>

        </div>
    </div>
</body>

</html>
