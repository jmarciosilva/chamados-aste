<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Grupo Aste Help Desk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 flex items-center justify-center">

    <div class="w-full max-w-md bg-slate-950/80 backdrop-blur rounded-2xl shadow-2xl p-8 text-white">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">

            <!-- Logo Grupo Aste -->
            <div class="mb-4 bg-white/10 p-3 rounded-2xl backdrop-blur">
                <img src="{{ asset('images/logo-aste.png') }}" alt="Grupo Aste"
                    class="h-16 md:h-18 object-contain rounded-xl">
            </div>

            <h1 class="text-xl font-semibold">Grupo Aste Help Desk</h1>
            <p class="text-sm text-slate-400 text-center">
                Acesse o portal para gerenciar seus chamados
            </p>

        </div>


        <!--
        <div class="flex bg-slate-800 rounded-lg p-1 mb-5 text-sm">
            <button type="button" class="flex-1 py-1.5 rounded-md bg-white text-slate-900 font-medium"
                onclick="setRole('user')" id="btn-user">
                Colaborador
            </button>
            <button type="button" class="flex-1 py-1.5 rounded-md text-slate-300" onclick="setRole('agent')"
                id="btn-agent">
                Operador
            </button>
            <button type="button" class="flex-1 py-1.5 rounded-md text-slate-300" onclick="setRole('admin')"
                id="btn-admin">
                Administrador
            </button>
        </div>
        Seletor de Perfil -->

        <!-- Mensagem de erro -->
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-400">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Formulário -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Campo escondido com o perfil -->
            <input type="hidden" name="login_role" id="login_role" value="user">

            <!-- Email -->
            <div>
                <label class="block text-sm text-slate-300 mb-1">
                    E-mail corporativo
                </label>
                <input type="email" name="email" required autofocus
                    class="w-full rounded-lg bg-slate-800 border-slate-700 text-white focus:border-blue-500 focus:ring-blue-500"
                    placeholder="nome@grupoaste.com.br">
            </div>

            <!-- Senha -->
            <div>
                <label class="block text-sm text-slate-300 mb-1">
                    Senha
                </label>
                <input type="password" name="password" required
                    class="w-full rounded-lg bg-slate-800 border-slate-700 text-white focus:border-blue-500 focus:ring-blue-500"
                    placeholder="••••••••">
            </div>

            <!-- Botão -->
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition rounded-lg py-2.5 font-medium flex items-center justify-center gap-2">
                Entrar
                <span>→</span>
            </button>
        </form>

        <!-- Rodapé -->
        <div class="mt-6 text-center text-xs text-slate-400">
            Esqueceu sua senha? Entre em contato com o ramal 1234.
        </div>
    </div>

    <!-- Script do seletor -->
    <script>
        function setRole(role) {
            document.getElementById('login_role').value = role;

            // reset visual
            ['user', 'agent', 'admin'].forEach(r => {
                document.getElementById('btn-' + r).className =
                    'flex-1 py-1.5 rounded-md text-slate-300';
            });

            // ativa o botão escolhido
            document.getElementById('btn-' + role).className =
                'flex-1 py-1.5 rounded-md bg-white text-slate-900 font-medium';
        }
    </script>

</body>

</html>
