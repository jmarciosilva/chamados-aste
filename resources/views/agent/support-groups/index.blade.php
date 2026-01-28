@extends('layouts.agent')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold">Grupos de Atendimento</h1>

        <a href="{{ route('agent.support-groups.create') }}"
           class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
            + Novo Grupo
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">Código</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($groups as $group)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">
                            {{ $group->name }}

                            @if($group->is_entry_point)
                                <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">
                                    Entrada
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-slate-500">
                            {{ $group->code }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded
                                {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-600' }}">
                                {{ $group->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('agent.support-groups.edit', $group) }}"
                               class="text-blue-600 text-xs hover:underline">
                                Editar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $groups->links() }}

</div>
@endsection
