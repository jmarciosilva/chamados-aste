<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Nome --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Nome do Grupo
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $supportGroup->name ?? '') }}"
               class="w-full rounded-md border border-slate-300 px-3 py-2
                      focus:outline-none focus:ring-2 focus:ring-blue-500"
               required>
    </div>

    {{-- Código --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Código do Grupo
        </label>
        <input type="text"
               name="code"
               value="{{ old('code', $supportGroup->code ?? '') }}"
               class="w-full rounded-md border border-slate-300 px-3 py-2 uppercase
                      focus:outline-none focus:ring-2 focus:ring-blue-500"
               required>
        <p class="text-xs text-slate-500 mt-1">
            Ex: SERVICE_DESK, ERP
        </p>
    </div>

    {{-- Descrição --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Descrição
        </label>
        <textarea name="description"
                  rows="3"
                  class="w-full rounded-md border border-slate-300 px-3 py-2
                         focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $supportGroup->description ?? '') }}</textarea>
    </div>

    {{-- Checkboxes --}}
    <div class="md:col-span-2 flex gap-8">
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                   @checked(old('is_active', $supportGroup->is_active ?? true))>
            Grupo ativo
        </label>

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox"
                   name="is_entry_point"
                   value="1"
                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                   @checked(old('is_entry_point', $supportGroup->is_entry_point ?? false))>
            Grupo de entrada (Service Desk)
        </label>
    </div>

    {{-- Técnicos --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Técnicos do Grupo
        </label>
        <select name="users[]"
                multiple
                class="w-full h-40 rounded-md border border-slate-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach($technicians as $tech)
                <option value="{{ $tech->id }}"
                    @selected(isset($supportGroup) && $supportGroup->users->contains($tech->id))>
                    {{ $tech->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
            Use CTRL (ou CMD no Mac) para múltipla seleção
        </p>
    </div>

</div>
