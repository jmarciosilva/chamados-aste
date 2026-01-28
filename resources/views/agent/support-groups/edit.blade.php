@extends('layouts.agent')

@section('content')
<div class="max-w-xl space-y-6">

    <h1 class="text-xl font-semibold">Editar Grupo</h1>

    <form method="POST"
          action="{{ route('agent.support-groups.update', $supportGroup) }}"
          class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-xs text-slate-500">Nome</label>
            <input name="name" value="{{ $supportGroup->name }}"
                   class="w-full rounded border-slate-300" required>
        </div>

        <div>
            <label class="text-xs text-slate-500">Descrição</label>
            <textarea name="description"
                      class="w-full rounded border-slate-300">{{ $supportGroup->description }}</textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                   @checked($supportGroup->is_active)>
            <span class="text-sm">Grupo ativo</span>
        </div>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Atualizar
        </button>
    </form>

</div>
@endsection
