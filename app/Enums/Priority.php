<?php

namespace App\Enums;

enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    /**
     * --------------------------------------------------------------
     * LABEL AMIGÁVEL (PT-BR)
     * --------------------------------------------------------------
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baixa',
            self::MEDIUM => 'Média',
            self::HIGH => 'Alta',
            self::CRITICAL => 'Crítica',
        };
    }

    /**
     * --------------------------------------------------------------
     * ORDEM DE IMPORTÂNCIA
     * --------------------------------------------------------------
     */
    public function weight(): int
    {
        return match ($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::CRITICAL => 4,
        };
    }

    /**
     * --------------------------------------------------------------
     * LISTA PARA SELECT
     * --------------------------------------------------------------
     */
    public static function options(): array
    {
        return array_map(
            fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'bg-green-100 text-green-700',
            self::MEDIUM => 'bg-yellow-100 text-yellow-700',
            self::HIGH => 'bg-orange-100 text-orange-700',
            self::CRITICAL => 'bg-red-100 text-red-700',
        };
    }
}
