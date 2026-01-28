<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Sistema de Chamados</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    <!-- Topbar -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between">

            <!-- Logo -->
            <div class="font-bold text-lg text-indigo-600">
                Aste HelpDesk
            </div>

            <!-- Menu -->
            <div class="hidden md:flex items-center space-x-6 text-sm">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">
                    Dashboard
                </a>
                <a href="{{ route('user.tickets.index') }}" class="hover:text-indigo-600">
                    Chamados
                </a>
            </div>

            <!-- User -->
            <div class="flex items-center space-x-3 text-sm">
                <span class="text-gray-600">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-500 hover:text-red-700">
                        Sair
                    </button>
                </form>
            </div>

        </div>
    </nav>

    <!-- Conteúdo -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <script src="//unpkg.com/alpinejs" defer></script>


</body>
</html>
