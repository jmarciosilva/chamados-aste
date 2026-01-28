@extends('layouts.user')

@section('content')

<div class="max-w-6xl mx-auto">

    <!-- HERO -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-xl p-8 text-white mb-8">
        <h1 class="text-2xl font-semibold mb-2">Base de Conhecimento</h1>
        <p class="text-sm text-blue-100 mb-4">
            Encontre respostas rápidas, tutoriais e guias passo-a-passo.
        </p>

        <div class="max-w-xl">
            <input
                type="text"
                placeholder="O que você precisa saber hoje?"
                class="w-full rounded-lg px-4 py-2 text-slate-800"
            >
        </div>
    </div>

    <!-- CATEGORIAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        @php
            $categories = [
                ['icon' => '🖥️', 'title' => 'Hardware & Equipamentos', 'count' => 24],
                ['icon' => '📶', 'title' => 'Rede & Conectividade', 'count' => 15],
                ['icon' => '🔐', 'title' => 'Acessos & Segurança', 'count' => 32],
                ['icon' => '🖨️', 'title' => 'Impressoras & Periféricos', 'count' => 18],
            ];
        @endphp

        @foreach($categories as $cat)
            <div class="bg-white rounded-xl shadow p-5 text-center hover:shadow-md transition cursor-pointer">
                <div class="text-3xl mb-2">{{ $cat['icon'] }}</div>
                <p class="font-medium text-slate-800">{{ $cat['title'] }}</p>
                <p class="text-xs text-slate-500">{{ $cat['count'] }} artigos</p>
            </div>
        @endforeach

    </div>

    <!-- GRID PRINCIPAL -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ARTIGOS MAIS ACESSADOS -->
        <div class="lg:col-span-2 space-y-4">

            <h2 class="font-semibold text-slate-800 mb-2 flex items-center gap-2">
                📈 Artigos Mais Acessados
            </h2>

            @php
                $articles = [
                    ['title' => 'Como configurar VPN no Windows 11', 'category' => 'Rede', 'views' => 1250, 'likes' => 45],
                    ['title' => 'Solicitando acesso ao SAP (Módulo Vendas)', 'category' => 'Acessos', 'views' => 980, 'likes' => 32],
                    ['title' => 'Resolvendo erro de papel na impressora térmica', 'category' => 'Impressoras', 'views' => 850, 'likes' => 28],
                    ['title' => 'Política de troca de senhas', 'category' => 'Segurança', 'views' => 720, 'likes' => 15],
                    ['title' => 'Configurando e-mail corporativo no celular', 'category' => 'Acessos', 'views' => 650, 'likes' => 40],
                ];
            @endphp

            @foreach($articles as $article)
                <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center hover:bg-slate-50 transition">
                    <div>
                        <p class="font-medium text-slate-800">{{ $article['title'] }}</p>
                        <div class="text-xs text-slate-500 mt-1 flex gap-3">
                            <span class="bg-slate-100 px-2 py-0.5 rounded">{{ $article['category'] }}</span>
                            <span>👁️ {{ $article['views'] }}</span>
                            <span>👍 {{ $article['likes'] }}</span>
                        </div>
                    </div>
                    <span class="text-slate-400 text-lg">›</span>
                </div>
            @endforeach

        </div>

        <!-- SIDEBAR -->
        <div class="space-y-6">

            <!-- ATUALIZAÇÕES -->
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    🔔 Atualizações Recentes
                </h3>

                <ul class="text-sm space-y-3 text-slate-700">
                    <li>
                        <p class="font-medium">Novo procedimento de backup</p>
                        <p class="text-xs text-slate-500">Atualizado ontem</p>
                    </li>
                    <li>
                        <p class="font-medium">Atualização do sistema de PDV v2.4</p>
                        <p class="text-xs text-slate-500">Atualizado ontem</p>
                    </li>
                    <li>
                        <p class="font-medium">Mudança no acesso Wi-Fi Visitantes</p>
                        <p class="text-xs text-slate-500">Atualizado ontem</p>
                    </li>
                    <li>
                        <p class="font-medium">Manutenção programada SAP</p>
                        <p class="text-xs text-slate-500">Atualizado ontem</p>
                    </li>
                </ul>
            </div>

            <!-- CTA -->
            <div class="bg-gradient-to-r from-blue-800 to-indigo-900 rounded-xl p-5 text-white">
                <h3 class="font-semibold mb-2">Não encontrou?</h3>
                <p class="text-xs text-blue-100 mb-3">
                    Nossa equipe está pronta para ajudar se você não encontrar a solução aqui.
                </p>
                <a href="{{ route('user.tickets.create') }}"
                   class="block text-center bg-white text-blue-700 py-2 rounded font-medium">
                    Abrir Chamado
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
