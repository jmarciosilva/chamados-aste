{{-- ============================================================
| HEADER DO OPERADOR
|------------------------------------------------------------
| - Botão hambúrguer
| - Busca global
| - Status do sistema
============================================================ --}}

<header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">

    {{-- ESQUERDA --}}
    <div class="flex items-center gap-4">

        {{-- HAMBÚRGUER --}}
        <button @click="sidebarOpen = !sidebarOpen" class="text-blue-600 hover:text-blue-800 focus:outline-none"
            aria-label="Alternar menu">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- BUSCA --}}
        <input type="text" placeholder="Buscar ticket, usuário ou artigo..."
            class="w-80 border border-slate-300 rounded-md px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-600">
                   
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="text-xs px-3 py-1 border rounded hover:bg-slate-100">
                Voltar ao Admin
            </a>
        @endif

    </div>

    {{-- DIREITA --}}
    <div class="flex items-center gap-4 text-sm">

        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">
            Sistema Online
        </span>

        <div class="text-right">
            <div class="font-medium text-slate-700">
                {{ auth()->user()->name }}
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="text-xs text-blue-600 font-semibold">
                    Admin • Modo Operador
                </div>
            @else
                <div class="text-xs text-slate-500">
                    Operador de Suporte
                </div>
            @endif
        </div>
    </div>

</header>
