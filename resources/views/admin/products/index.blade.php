@extends('layouts.admin')

@section('title', 'Produtos')
@section('subtitle', 'Sistemas e plataformas atendidas com SLAs configurados')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div class="text-sm text-slate-600">
        Total: <strong>{{ $products->total() }}</strong> produtos
    </div>
    
    <a href="{{ route('admin.products.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Novo Produto
    </a>
</div>

{{-- ============================================================
| TABELA DE PRODUTOS
============================================================ --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
            <tr>
                <th class="text-left px-6 py-3">Produto</th>
                <th class="text-center px-4 py-3">SLA Crítica</th>
                <th class="text-center px-4 py-3">SLA Alta</th>
                <th class="text-center px-4 py-3">SLA Média</th>
                <th class="text-center px-4 py-3">SLA Baixa</th>
                <th class="text-center px-4 py-3">Status</th>
                <th class="text-right px-6 py-3">Ações</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-200">
            @forelse($products as $product)
                @php
                    $slaConfig = $product->getSlaConfigFormatted();
                @endphp
                
                <tr class="hover:bg-slate-50 transition">
                    {{-- Nome do Produto --}}
                    <td class="px-6 py-4">
                        <div>
                            <strong class="text-slate-800">{{ $product->name }}</strong>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $product->slug }}</div>
                        </div>
                    </td>

                    {{-- SLA Crítica --}}
                    <td class="px-4 py-4 text-center">
                        <div class="text-xs">
                            <div class="font-medium text-slate-700">
                                {{ $slaConfig['critical']['resolution_hours'] }}h
                            </div>
                            <div class="text-slate-400">
                                Resp: {{ $slaConfig['critical']['response_hours'] }}h
                            </div>
                        </div>
                    </td>

                    {{-- SLA Alta --}}
                    <td class="px-4 py-4 text-center">
                        <div class="text-xs">
                            <div class="font-medium text-slate-700">
                                {{ $slaConfig['high']['resolution_hours'] }}h
                            </div>
                            <div class="text-slate-400">
                                Resp: {{ $slaConfig['high']['response_hours'] }}h
                            </div>
                        </div>
                    </td>

                    {{-- SLA Média --}}
                    <td class="px-4 py-4 text-center">
                        <div class="text-xs">
                            <div class="font-medium text-slate-700">
                                {{ $slaConfig['medium']['resolution_hours'] }}h
                            </div>
                            <div class="text-slate-400">
                                Resp: {{ $slaConfig['medium']['response_hours'] }}h
                            </div>
                        </div>
                    </td>

                    {{-- SLA Baixa --}}
                    <td class="px-4 py-4 text-center">
                        <div class="text-xs">
                            <div class="font-medium text-slate-700">
                                {{ $slaConfig['low']['resolution_hours'] }}h
                            </div>
                            <div class="text-slate-400">
                                Resp: {{ $slaConfig['low']['response_hours'] }}h
                            </div>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                            {{ $product->is_active 
                                ? 'bg-green-100 text-green-700' 
                                : 'bg-red-100 text-red-700' }}">
                            {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>

                    {{-- Ações --}}
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="inline-flex items-center gap-1 text-blue-600 text-xs font-medium hover:text-blue-800 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-sm">Nenhum produto cadastrado</p>
                        <a href="{{ route('admin.products.create') }}" class="text-blue-600 text-xs hover:underline mt-2 inline-block">
                            Criar primeiro produto
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginação --}}
    @if($products->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $products->links() }}
        </div>
    @endif
</div>

{{-- ============================================================
| LEGENDA
============================================================ --}}
<div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex gap-2">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-xs text-blue-800">
            <strong>Sobre os SLAs:</strong> 
            Os valores exibidos representam o <strong>tempo de resolução</strong> (em horas) para cada prioridade.
            O tempo de resposta é mostrado abaixo em cinza.
            Clique em "Editar" para ajustar os SLAs de cada produto.
        </div>
    </div>
</div>

@endsection