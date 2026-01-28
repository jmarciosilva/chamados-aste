<div class="space-y-4">

    <div>
        <label class="text-xs text-slate-500">Nome do Produto</label>
        <input type="text" name="name"
               value="{{ old('name', $product->name ?? '') }}"
               required
               class="w-full rounded border-slate-300 text-sm">
    </div>

    <div>
        <label class="text-xs text-slate-500">Descrição</label>
        <textarea name="description"
                  rows="3"
                  class="w-full rounded border-slate-300 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            Produto ativo
        </label>
    </div>

</div>
