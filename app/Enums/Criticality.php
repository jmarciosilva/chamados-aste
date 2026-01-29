<?php

namespace App\Enums;

enum Criticality: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    /**
     * --------------------------------------------------------------
     * LABEL AMIGÁVEL (USUÁRIO FINAL)
     * --------------------------------------------------------------
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Posso trabalhar normalmente',
            self::MEDIUM => 'Trabalho prejudicado, mas consigo continuar',
            self::HIGH => 'Trabalho quase parado',
            self::CRITICAL => 'Trabalho totalmente parado',
        };
    }

    /**
     * --------------------------------------------------------------
     * CONVERSÃO PARA PRIORIDADE DO SISTEMA
     * --------------------------------------------------------------
     */
    public function toPriority(): Priority
    {
        return match ($this) {
            self::LOW => Priority::LOW,
            self::MEDIUM => Priority::MEDIUM,
            self::HIGH => Priority::HIGH,
            self::CRITICAL => Priority::CRITICAL,
        };
    }
}
