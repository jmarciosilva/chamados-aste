@php
    $editing = isset($user);
@endphp

<!-- ============================================================
| FORMULÁRIO DE USUÁRIO (ADMIN)
|============================================================ -->

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Nome -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Nome
        </label>
        <input type="text" name="name"
               value="{{ old('name', $user->name ?? '') }}"
               required
               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
    </div>

    <!-- Email -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Email
        </label>
        <input type="email" name="email"
               value="{{ old('email', $user->email ?? '') }}"
               required
               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
    </div>

    <!-- Senha -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Senha
            @if($editing)
                <span class="text-xs text-slate-400">(opcional)</span>
            @endif
        </label>
        <input type="password" name="password"
               {{ $editing ? '' : 'required' }}
               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
    </div>

    <!-- Perfil -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Perfil
        </label>
        <select name="role" required
                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            @foreach(['user' => 'Usuário', 'agent' => 'Operador', 'admin' => 'Administrador'] as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('role', $user->role ?? 'user') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Departamento -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Departamento
        </label>
        <select name="department_id"
                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            <option value="">—</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}"
                    @selected(old('department_id', $user->department_id ?? '') == $dept->id)>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Cargo -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Cargo
        </label>
        <input type="text" name="job_title"
               value="{{ old('job_title', $user->job_title ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
    </div>

    <!-- Telefone -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Telefone
        </label>
        <input type="text" name="phone"
               value="{{ old('phone', $user->phone ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
    </div>

    <!-- Status -->
    <div class="flex items-center gap-3 mt-7">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
               @checked(old('is_active', $user->is_active ?? true))>
        <label class="text-sm text-slate-700">
            Usuário ativo
        </label>
    </div>

</div>
