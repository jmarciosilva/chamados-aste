<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-lg font-bold">
                    Sistema Chamados
                </a>
            </div>

            <!-- Links -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-black">
                    Dashboard
                </a>

                <a href="{{ route('user.tickets.index') }}" class="text-gray-700 hover:text-black">
                    Meus Chamados
                </a>
            </div>

            <!-- User -->
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600 hover:text-red-800">
                        Sair
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>
