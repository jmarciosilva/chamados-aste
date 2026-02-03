<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Chamado</th>
                <th class="px-4 py-3 text-left">Título</th>
                <th class="px-4 py-3 text-left">Solicitante</th>
                <th class="px-4 py-3 text-left">Departamento</th>
                <th class="px-4 py-3 text-left">Prioridade</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Abertura</th>

                @if(!isset($readOnly))
                    <th class="px-4 py-3 text-right">Ação</th>
                @endif
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($tickets as $ticket)

                @php
                    $priority = $ticket->priorityBadge();
                    $status   = $ticket->statusBadge();
                @endphp

                <tr class="hover:bg-slate-50">

                    <td class="px-4 py-3 font-semibold">{{ $ticket->code }}</td>
                    <td class="px-4 py-3">{{ $ticket->subject }}</td>
                    <td class="px-4 py-3">{{ $ticket->requester->name }}</td>
                    <td class="px-4 py-3">{{ $ticket->department->name ?? '—' }}</td>

                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs {{ $priority['color'] }}">
                            {{ $priority['label'] }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs {{ $status['color'] }}">
                            {{ $status['label'] }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-slate-500">
                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </td>

                    @if(!isset($readOnly))
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('agent.tickets.show', $ticket) }}"
                               class="px-2 py-1 text-xs bg-blue-600 text-white rounded">
                                Abrir
                            </a>
                        </td>
                    @endif

                </tr>

            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-500">
                        Nenhum chamado na fila
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
