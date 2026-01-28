<div class="bg-white rounded shadow p-6 space-y-4">

    <div>
        <label class="text-sm">Nome</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
            class="w-full rounded border-slate-300">
    </div>

    <!-- PRODUTO -->
    <div>
        <label class="text-sm">Produto</label>
        <select name="product_id" required class="w-full rounded border-slate-300">
            <option value="">Selecione o produto</option>

            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $category->product_id ?? '') == $product->id)>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- NOME -->
    <div>
        <label class="text-sm">Nome</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
            class="w-full rounded border-slate-300">
    </div>

    <!-- DESCRIÇÃO -->
    <div>
        <label class="text-sm">Descrição</label>
        <textarea name="description" rows="3" class="w-full rounded border-slate-300">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <!-- TIPO DE SERVIÇO -->
    <div>
        <label class="text-sm">Tipo de Serviço</label>
        <select name="service_type" class="w-full rounded border-slate-300">
            @foreach ([
        'incident' => 'Incidente',
        'service_request' => 'Solicitação de Serviço',
        'purchase' => 'Solicitação de Compra',
        'improvement' => 'Melhoria',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('service_type', $category->service_type ?? '') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- PRIORIDADE -->
    <div>
        <label class="text-sm">Prioridade Padrão</label>
        <select name="default_priority" class="w-full rounded border-slate-300">
            @foreach (['low', 'medium', 'high', 'critical'] as $priority)
                <option value="{{ $priority }}" @selected(old('default_priority', $category->default_priority ?? '') === $priority)>
                    {{ ucfirst($priority) }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- AÇÕES -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.problem-categories.index') }}" class="px-4 py-2 border rounded">
            Cancelar
        </a>

        <button class="px-6 py-2 bg-blue-600 text-white rounded">
            Salvar
        </button>
    </div>

</div>
