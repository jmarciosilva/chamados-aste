@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto">

        <!-- ============================================================
                | CABEÇALHO
                |============================================================ -->
        <div class="flex flex-col gap-4 mb-6">

            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold">Usuários</h1>
                    <p class="text-sm text-slate-500">
                        Gerencie usuários, perfis e status de acesso.
                    </p>
                </div>

                <!-- Novo Usuário -->
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    ➕ Novo Usuário
                </a>
            </div>

            <!-- ========================================================
                    | AÇÕES: PLANILHA
                    |======================================================== -->
            <div class="flex flex-wrap items-center gap-3">

                <!-- Download modelo -->
                <a href="{{ route('admin.users.import.template') }}"
                    class="inline-flex items-center gap-2 bg-slate-100 border border-slate-300
                          text-slate-800 hover:bg-slate-200 px-4 py-2 rounded-md text-sm transition">
                    📥 Baixar Planilha Modelo
                </a>

                <!-- Importação -->
                <form action="{{ route('admin.users.import.preview') }}" method="POST" enctype="multipart/form-data"
                    class="flex items-center gap-2 border border-slate-300 px-3 py-2 rounded-md bg-slate-50">

                    @csrf

                    <input type="file" name="file" required
                        class="text-sm text-slate-600 file:mr-3 file:px-3 file:py-1 file:rounded file:border-0
                                  file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">

                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md
                                   text-sm font-medium transition">
                        Importar
                    </button>
                </form>

            </div>

            <!-- ========================================================
                    | LEGENDA RÁPIDA DA PLANILHA
                    |======================================================== -->
            <div class="bg-slate-50 border border-slate-200 rounded-md p-4 text-sm text-slate-700">
                <p class="font-medium mb-2">📘 Como preencher a planilha de importação:</p>

                <ul class="list-disc ml-5 space-y-1">
                    <li><strong>Nome</strong>: Nome completo do usuário</li>
                    <li><strong>E-mail</strong>: Deve ser único no sistema</li>
                    <li><strong>Perfil</strong>: <code>Usuário</code>, <code>Operador</code> ou <code>Administrador</code>
                    </li>
                    <li><strong>Departamento</strong>: Nome do departamento cadastrado</li>
                    <li><strong>Ativo</strong>: <code>1</code> = ativo / <code>0</code> = inativo</li>
                    <li><strong>Senha</strong>: Opcional (padrão: <code>123456</code>)</li>
                </ul>
            </div>

        </div>

        <!-- Mensagem de sucesso -->
        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- ============================================================
                | TABELA
                |============================================================ -->
        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Nome</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Perfil</th>
                        <th class="px-4 py-2 text-left">Departamento</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-right">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                     
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2 capitalize">{{ $user->role_label }}</td>
                            <td class="px-4 py-2">
                                {{ $user->department->name ?? '—' }}
                            </td>
                            <td class="px-4 py-2">
                                @if ($user->is_active)
                                    <span class="text-green-600 font-medium">Ativo</span>
                                @else
                                    <span class="text-red-600 font-medium">Inativo</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">
                                    Editar
                                </a>

                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="text-sm text-slate-600 hover:underline">
                                        {{ $user->is_active ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginação -->
            <div class="p-4">
                {{ $users->links() }}
            </div>
        </div>

    </div>
@endsection
