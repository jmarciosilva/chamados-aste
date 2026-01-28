<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_USER = 'waiting_user';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    /**
     * --------------------------------------------------------------
     * LABEL AMIGÁVEL (PT-BR)
     * --------------------------------------------------------------
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::IN_PROGRESS => 'Em Atendimento',
            self::WAITING_USER => 'Aguardando Usuário',
            self::RESOLVED => 'Resolvido',
            self::CLOSED => 'Fechado',
        };
    }

    /**
     * --------------------------------------------------------------
     * STATUS VISÍVEIS PARA O USUÁRIO
     * --------------------------------------------------------------
     */
    public function isUserVisible(): bool
    {
        return ! in_array($this, [
            self::IN_PROGRESS,
        ]);
    }

    /**
     * --------------------------------------------------------------
     * STATUS FINAIS
     * --------------------------------------------------------------
     */
    public function isFinal(): bool
    {
        return $this === self::CLOSED;
    }
}
