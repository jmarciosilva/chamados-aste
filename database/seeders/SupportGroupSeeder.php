<?php

namespace Database\Seeders;

use App\Models\SupportGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportGroupSeeder extends Seeder
{
    /**
     * ------------------------------------------------------------------
     * GRUPOS DE SUPORTE – GRUPO ASTE
     * ------------------------------------------------------------------
     * Estrutura oficial de atendimento baseada em ITIL.
     *
     * Regras:
     * - Todos os grupos iniciais são criados por um usuário administrador
     * - Service Desk é o grupo de entrada padrão
     * - Os demais grupos podem ser ativados/desativados futuramente
     */
    public function run(): void
    {
        /**
         * --------------------------------------------------------------
         * USUÁRIO RESPONSÁVEL PELA CRIAÇÃO
         * --------------------------------------------------------------
         * Normalmente será o primeiro admin do sistema.
         * Caso não exista, falhamos propositalmente.
         */
        $creator = User::first();

        if (! $creator) {
            throw new \Exception(
                'Não foi possível executar SupportGroupSeeder: nenhum usuário encontrado.'
            );
        }

        /**
         * --------------------------------------------------------------
         * SERVICE DESK (PONTO DE ENTRADA)
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'SERVICE_DESK'],
            [
                'name' => 'Service Desk',
                'description' => 'Nível 1 - Triagem, atendimento inicial e direcionamento',
                'is_entry_point' => true,
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE ERP
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'SIGE'],
            [
                'name' => 'Suporte ERP SIGE Nível 2',
                'description' => 'SIGE, fiscal, pedidos e sistemas administrativos',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE PDV
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'PDV'],
            [
                'name' => 'Suporte PDV',
                'description' => 'Sistema de vendas, cancelamentos e operações de loja',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE TI LOGIN
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'TI_Acesso'],
            [
                'name' => 'Suporte TI Acesso',
                'description' => 'Login rede, e-mails, sistemas internos e acessos',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE TI EMAIL
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'TI_Email'],
            [
                'name' => 'Suporte TI Email',
                'description' => 'Caixa Email, configuração e problemas relacionados',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE TI
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'TI'],
            [
                'name' => 'Suporte TI',
                'description' => 'Redes, hardware, impressoras, telefonia e infraestrutura',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE OmniChannel
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'OmniChannel'],
            [
                'name' => 'Suporte OmniChannel',
                'description' => 'Integrador de canais de venda (e-commerce, marketplaces, etc.)',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE E-commerce
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'Ecommerce'],
            [
                'name' => 'Suporte E-commerce',
                'description' => 'Plataforma de vendas online, pedidos e integrações',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE OmniChannel
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'TI_Equipamentos'],
            [
                'name' => 'Suporte TI Equipamentos',
                'description' => 'Manutenção, Substituição e suporte a equipamentos de TI',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE Vejo Varejo
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'Vejo_Varejo'],
            [
                'name' => 'Suporte Vejo Varejo',
                'description' => 'Venda e Suporte Plataforma Vejo Varejo',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE WEBB LOJA
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'WEBB_LOJA'],
            [
                'name' => 'Suporte Webb Loja',
                'description' => 'Social Seller, Vendas com comissão via WhatsApp e WEB, Notas Fiscais',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * SUPORTE CENTELHA
         * --------------------------------------------------------------
         */
        SupportGroup::firstOrCreate(
            ['code' => 'CENTELHA'],
            [
                'name' => 'Suporte Centelha',
                'description' => 'Plataforma B2B Centelha (atacado)',
                'is_active' => true,
                'created_by' => $creator->id,
            ]
        );

        /**
         * --------------------------------------------------------------
         * VÍNCULO DE ESPECIALISTAS AOS GRUPOS
         * --------------------------------------------------------------
         */
        $erpSpecialist = User::where('email', 'erp@grupoaste.com.br')->first();
        $pdvSpecialist = User::where('email', 'pdv@grupoaste.com.br')->first();

        $sigeGroup = SupportGroup::where('code', 'SIGE')->first();
        $pdvGroup = SupportGroup::where('code', 'PDV')->first();

        if ($erpSpecialist && $sigeGroup) {
            $sigeGroup->users()->syncWithoutDetaching([$erpSpecialist->id]);
        }

        if ($pdvSpecialist && $pdvGroup) {
            $pdvGroup->users()->syncWithoutDetaching([$pdvSpecialist->id]);
        }

    }
}
