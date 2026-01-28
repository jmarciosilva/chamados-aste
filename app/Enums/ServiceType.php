<?php

namespace App\Enums;

enum ServiceType: string
{
    case INCIDENT = 'incident';
    case SERVICE_REQUEST = 'service_request';
    case PURCHASE_REQUEST = 'purchase_request';
    case IMPROVEMENT = 'improvement';

    /**
     * --------------------------------------------------------------
     * LABEL AMIGÁVEL (PT-BR)
     * --------------------------------------------------------------
     */
    public function label(): string
    {
        return match ($this) {
            self::INCIDENT => 'Incidente',
            self::SERVICE_REQUEST => 'Solicitação de Serviço',
            self::PURCHASE_REQUEST => 'Solicitação de Compra',
            self::IMPROVEMENT => 'Melhoria',
        };
    }

    /**
     * --------------------------------------------------------------
     * LISTA PARA SELECT (FORMULÁRIOS)
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
}
