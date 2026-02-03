@extends('layouts.admin')

@section('title', 'Editar Grupo de Atendimento')
@section('subtitle', 'Atualização do grupo de suporte')

@section('content')

    {{-- BREADCRUMB --}}
    <div class="flex items-center gap-2 text-sm text-slate-600 mb-6">
        <a href="{{ route('admin.support-groups.index') }}" class="hover:text-slate-900">Grupos de Atendimento</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-900 font-medium">Editar: {{ $supportGroup->name }}</span>
    </div>

    <div class="max-w-4xl">

        {{-- CARD PRINCIPAL --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">

            {{-- CABEÇALHO --}}
            <div class="px-8 py-6 border-b border-slate-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-slate-900">Editar Grupo</h2>
                        <p class="text-sm text-slate-600 mt-1">
                            Atualize as informações do grupo de atendimento
                        </p>
                    </div>
                    
                    {{-- STATUS BADGE --}}
                    @if($supportGroup->is_active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium 
                                     bg-green-100 text-green-700 border border-green-200">
                            <span class="w-2 h-2 rounded-full bg-green-600"></span>
                            Ativo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium 
                                     bg-red-100 text-red-700 border border-red-200">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            Inativo
                        </span>
                    @endif
                </div>
            </div>

            {{-- FORMULÁRIO --}}
            <form action="{{ route('admin.support-groups.update', $supportGroup) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="px-8 py-6 space-y-6">

                    {{-- GRID 2 COLUNAS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- NOME --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">
                                Nome do Grupo
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $supportGroup->name) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                          transition-all @error('name') border-red-300 focus:ring-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CÓDIGO --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">
                                Código do Grupo
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="code"
                                   value="{{ old('code', $supportGroup->code) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 uppercase font-mono
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                          transition-all @error('code') border-red-300 focus:ring-red-500 @enderror"
                                   required>
                            @error('code')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- DESCRIÇÃO --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Descrição
                        </label>
                        <textarea name="description"
                                  rows="3"
                                  class="w-full px-4 py-2.5 rounded-lg border border-slate-300 
                                         focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                         transition-all resize-none">{{ old('description', $supportGroup->description) }}</textarea>
                    </div>

                    {{-- CHECKBOXES --}}
                    <div class="space-y-3 bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center h-6">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       id="is_active"
                                       class="w-4 h-4 rounded border-slate-300 text-blue-600 
                                              focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                       @checked(old('is_active', $supportGroup->is_active))>
                            </div>
                            <div class="flex-1">
                                <label for="is_active" class="text-sm font-medium text-slate-900 cursor-pointer">
                                    Grupo ativo
                                </label>
                                <p class="text-xs text-slate-600 mt-0.5">
                                    Apenas grupos ativos podem receber chamados
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex items-center h-6">
                                <input type="checkbox"
                                       name="is_entry_point"
                                       value="1"
                                       id="is_entry_point"
                                       class="w-4 h-4 rounded border-slate-300 text-yellow-600 
                                              focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                                       @checked(old('is_entry_point', $supportGroup->is_entry_point))>
                            </div>
                            <div class="flex-1">
                                <label for="is_entry_point" class="text-sm font-medium text-slate-900 cursor-pointer flex items-center gap-1.5">
                                    Grupo de entrada padrão
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Service Desk
                                    </span>
                                </label>
                                <p class="text-xs text-slate-600 mt-0.5">
                                    Novos chamados serão direcionados automaticamente para este grupo
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- TÉCNICOS --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Técnicos do Grupo
                            <span class="text-xs font-normal text-slate-500 ml-1">
                                ({{ $supportGroup->users->count() }} selecionados)
                            </span>
                        </label>
                        <select name="users[]"
                                multiple
                                size="8"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 
                                       focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                       transition-all">
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" 
                                        class="px-3 py-2 hover:bg-blue-50"
                                        @selected($supportGroup->users->contains($tech->id) || collect(old('users', []))->contains($tech->id))>
                                    {{ $tech->name }} ({{ $tech->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="flex items-start gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Use <kbd class="px-1.5 py-0.5 bg-slate-200 rounded font-mono">CTRL</kbd> (ou <kbd class="px-1.5 py-0.5 bg-slate-200 rounded font-mono">CMD</kbd> no Mac) para selecionar múltiplos técnicos</p>
                        </div>
                    </div>

                </div>

                {{-- RODAPÉ --}}
                <div class="px-8 py-5 border-t border-slate-200 bg-slate-50 rounded-b-xl">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.support-groups.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium 
                                  text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 
                                  transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium 
                                       text-white bg-blue-600 hover:bg-blue-700 shadow-sm 
                                       transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Atualizar Grupo
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection