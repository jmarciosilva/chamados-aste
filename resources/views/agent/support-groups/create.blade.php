@extends('layouts.agent')

@section('content')
<div class="max-w-xl space-y-6">

    <h1 class="text-xl font-semibold">Criar Grupo de Atendimento</h1>

    <form method="POST" action="{{ route('agent.support-groups.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="text-xs text-slate-500">Nome</label>
            <input name="name" class="w-full rounded border-slate-300" required>
        </div>

        <div>
            <label class="text-xs text-slate-500">Código</label>
            <input name="code" class="w-full rounded border-slate-300 uppercase" required>
        </div>

        <div>
            <label class="text-xs text-slate-500">Descrição</label>
            <textarea name="description" class="w-full rounded border-slate-300"></textarea>
        </div>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Salvar
        </button>
    </form>

</div>
@endsection
