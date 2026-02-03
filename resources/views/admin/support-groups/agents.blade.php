@extends('layouts.admin')

@section('title', 'Gerenciar Técnicos')

@section('content')
<h2 class="text-xl font-semibold mb-4">
    {{ $supportGroup->name }}
</h2>

<form method="POST"
      action="{{ route('admin.support-groups.agents.update', $supportGroup) }}">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($agents as $agent)
            <label class="flex items-center gap-3 p-3 border rounded-lg">
                <input type="checkbox"
                       name="users[]"
                       value="{{ $agent->id }}"
                       @checked($supportGroup->users->contains($agent->id))>

                <span>
                    {{ $agent->name }}
                    <small class="text-slate-500 block">
                        {{ $agent->email }}
                    </small>
                </span>
            </label>
        @endforeach
    </div>

    <div class="mt-6">
        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
            Salvar
        </button>
    </div>
</form>
@endsection
