{{-- ============================================================
| SIDEBAR DO OPERADOR
|------------------------------------------------------------
| - Colapsável
| - Estados ativos por rota
| - Identidade visual consistente
============================================================ --}}

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white
           transform transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- LOGO --}}
    <div class="h-16 flex items-center px-6 text-lg font-bold border-b border-blue-700">
        ASTE SUPPORT
    </div>

    {{-- MENU --}}
    <nav class="flex-1 mt-6 px-4 space-y-1 text-sm">

        <a href="{{ route('agent.dashboard') }}"
            class="block px-3 py-2 rounded-md
           {{ request()->routeIs('agent.dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700/70' }}">
            Dashboard
        </a>

        <a href="{{ route('agent.queue') }}"
            class="block px-3 py-2 rounded-md
           {{ request()->routeIs('agent.queue') ? 'bg-blue-700' : 'hover:bg-blue-700/70' }}">
            Fila de Chamados
        </a>

        <a href="#" class="block px-3 py-2 rounded-md hover:bg-blue-700/70">
            Base de Conhecimento
        </a>

        <hr class="border-blue-700 my-4">

        <a href="#" class="block px-3 py-2 rounded-md hover:bg-blue-700/70">
            Relatórios & KPIs
        </a>

        <a href="#" class="block px-3 py-2 rounded-md hover:bg-blue-700/70">
            Equipes
        </a>

        <a href="#" class="block px-3 py-2 rounded-md hover:bg-blue-700/70">
            Configurações
        </a>
    </nav>

    {{-- USUÁRIO --}}
    <div class="p-4 border-t border-blue-800 text-xs">
        <div class="font-semibold">
            {{ auth()->user()->name }}
        </div>

        <div class="text-blue-200">
            {{ auth()->user()->role === 'admin' ? 'Administrador • Operador' : 'Operador' }}
        </div>
    </div>
</aside>
