const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell, 
        HeadingLevel, AlignmentType, BorderStyle, WidthType, ShadingType,
        LevelFormat, PageBreak } = require('docx');
const fs = require('fs');

const doc = new Document({
  styles: {
    default: { 
      document: { 
        run: { font: "Arial", size: 24 } 
      } 
    },
    paragraphStyles: [
      { 
        id: "Heading1", 
        name: "Heading 1", 
        basedOn: "Normal", 
        next: "Normal", 
        quickFormat: true,
        run: { size: 32, bold: true, font: "Arial", color: "2E5C8A" },
        paragraph: { spacing: { before: 480, after: 240 }, outlineLevel: 0 } 
      },
      { 
        id: "Heading2", 
        name: "Heading 2", 
        basedOn: "Normal", 
        next: "Normal", 
        quickFormat: true,
        run: { size: 28, bold: true, font: "Arial", color: "2E5C8A" },
        paragraph: { spacing: { before: 360, after: 180 }, outlineLevel: 1 } 
      },
      { 
        id: "Heading3", 
        name: "Heading 3", 
        basedOn: "Normal", 
        next: "Normal", 
        quickFormat: true,
        run: { size: 26, bold: true, font: "Arial", color: "4A7BA7" },
        paragraph: { spacing: { before: 240, after: 120 }, outlineLevel: 2 } 
      },
    ]
  },
  numbering: {
    config: [
      {
        reference: "bullets",
        levels: [
          { 
            level: 0, 
            format: LevelFormat.BULLET, 
            text: "•", 
            alignment: AlignmentType.LEFT,
            style: { paragraph: { indent: { left: 720, hanging: 360 } } } 
          },
          { 
            level: 1, 
            format: LevelFormat.BULLET, 
            text: "◦", 
            alignment: AlignmentType.LEFT,
            style: { paragraph: { indent: { left: 1440, hanging: 360 } } } 
          }
        ]
      },
      {
        reference: "numbers",
        levels: [
          { 
            level: 0, 
            format: LevelFormat.DECIMAL, 
            text: "%1.", 
            alignment: AlignmentType.LEFT,
            style: { paragraph: { indent: { left: 720, hanging: 360 } } } 
          }
        ]
      }
    ]
  },
  sections: [{
    properties: {
      page: {
        size: {
          width: 12240,
          height: 15840
        },
        margin: { 
          top: 1440, 
          right: 1440, 
          bottom: 1440, 
          left: 1440 
        }
      }
    },
    children: [
      // CAPA
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 2880 },
        children: [
          new TextRun({
            text: "ANÁLISE TÉCNICA",
            size: 48,
            bold: true,
            color: "2E5C8A"
          })
        ]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Sistema de Help Desk",
            size: 40,
            bold: true
          })
        ]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "Grupo Aste",
            size: 32,
            color: "666666"
          })
        ]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 1440 },
        children: [
          new TextRun({
            text: "Laravel 12 • PHP • MySQL",
            size: 24,
            italics: true,
            color: "888888"
          })
        ]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 2880 },
        children: [
          new TextRun({
            text: `Data: ${new Date().toLocaleDateString('pt-BR')}`,
            size: 22,
            color: "999999"
          })
        ]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // SUMÁRIO EXECUTIVO
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("1. SUMÁRIO EXECUTIVO")]
      }),
      
      new Paragraph({
        spacing: { before: 240, after: 120 },
        children: [
          new TextRun({
            text: "Este documento apresenta uma análise técnica completa do sistema de Help Desk desenvolvido para o Grupo Aste. O sistema foi construído utilizando Laravel 12, seguindo princípios ITIL para gestão de serviços de TI.",
            size: 24
          })
        ]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("1.1 Avaliação Geral")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "Pontos Fortes:",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Arquitetura bem estruturada seguindo padrões MVC do Laravel")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Uso adequado de Enums (PHP 8.1+) para tipos e status")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Sistema de SLA robusto com controle de pausas e tempo acumulado")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Implementação de múltiplos contextos (admin/agent/user) bem pensada")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Histórico ITIL completo de transferências entre grupos")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Seeders bem documentados facilitando deploy inicial")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Pontos de Atenção:",
            bold: true,
            size: 24,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Falta implementação completa do AgentTicketController")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Upload de arquivos em public/ ao invés de storage/ (segurança)")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Ausência de testes automatizados")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Falta de middleware personalizado para validação de grupos de suporte")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Campo problem_category_id tornado nullable recentemente (possível mudança de escopo)")]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // ARQUITETURA DO SISTEMA
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("2. ARQUITETURA DO SISTEMA")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("2.1 Stack Tecnológica")]
      }),

      // Tabela de Stack
      ...createStackTable(),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 480 },
        children: [new TextRun("2.2 Estrutura do Banco de Dados")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "O sistema possui 12 tabelas principais organizadas em 4 grupos funcionais:",
            size: 24
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Grupo 1: Gestão de Usuários e Departamentos",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("users - Usuários do sistema com roles (user, agent, admin)")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("departments - Departamentos organizacionais")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Grupo 2: Catálogo de Produtos e Serviços",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("products - Sistemas/plataformas atendidas (SIGE, PDV, etc.)")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("problem_categories - Categorias de problemas vinculadas a produtos")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("slas - Regras de SLA (Produto + Tipo + Prioridade)")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Grupo 3: Grupos de Suporte",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("support_groups - Grupos de atendimento (Service Desk, Especialistas)")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("support_group_user - Pivot: usuários membros de grupos")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Grupo 4: Tickets e Relacionados",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("tickets - Chamados (entidade central do sistema)")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("ticket_messages - Mensagens e notas internas")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("ticket_attachments - Anexos de arquivos")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("ticket_group_histories - Histórico de transferências ITIL")]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // MODELO DE DADOS
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("3. MODELO DE DADOS DETALHADO")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("3.1 Tabela: tickets")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "Esta é a tabela central do sistema. Contém snapshot completo do SLA para garantir rastreabilidade histórica.",
            size: 24,
            italics: true
          })
        ]
      }),

      ...createTicketsTable(),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 480 },
        children: [new TextRun("3.2 Sistema de SLA")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "Implementação Destacável:",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Snapshot de SLA: ",
            bold: true
          }),
          new TextRun("Ao criar o ticket, os valores de response_time e resolution_time são copiados para o ticket, garantindo que alterações futuras no SLA não afetem tickets existentes")
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Controle de pausas: ",
            bold: true
          }),
          new TextRun("Sistema acumula tempo pausado em sla_paused_seconds, permitindo cálculo preciso do deadline real")
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Estados do SLA: ",
            bold: true
          }),
          new TextRun("running (ativo) | paused (aguardando usuário) | breached (estourado) | completed (concluído)")
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Cálculo de deadline: ",
            bold: true
          }),
          new TextRun("sla_started_at + sla_resolution_hours + sla_paused_seconds")
        ]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // FLUXO DE TRABALHO
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("4. FLUXO DE TRABALHO")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("4.1 Abertura de Chamado (Usuário)")]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Usuário seleciona: ",
            bold: true
          }),
          new TextRun("Produto → Tipo de Serviço → Criticidade (impacto no trabalho)")
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Sistema converte: ",
            bold: true
          }),
          new TextRun("Criticality → Priority automaticamente")
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Sistema busca SLA: ",
            bold: true
          }),
          new TextRun("Produto + ServiceType + Priority")
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Se não encontrar: ",
            bold: true
          }),
          new TextRun("Usa SLA padrão do produto (is_default = true)")
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Gera código: ",
            bold: true
          }),
          new TextRun("CHMMYY-NNNNNN (ex: CH0126-000001)")
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Atribui ao grupo de entrada: ",
            bold: true
          }),
          new TextRun("Service Desk (is_entry_point = true)")
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [
          new TextRun({
            text: "Registra histórico: ",
            bold: true
          }),
          new TextRun("Cria entrada em ticket_group_histories")
        ]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("4.2 Status do Ticket")]
      }),

      ...createStatusFlowTable(),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("4.3 Mudança Recente: problem_category_id")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "⚠️ ATENÇÃO: ",
            bold: true,
            color: "D9534F",
            size: 26
          }),
          new TextRun({
            text: "Em 29/01/2026 foi criada uma migration tornando problem_category_id NULLABLE.",
            size: 24
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "Evidências no código:",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("TicketController.store() comenta: // 🔥 categoria removida do fluxo")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Campo não é mais exigido na validação")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Migration 2026_01_29_125451_alter_problem_category_nullable_on_tickets.php")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Possível razão:",
            bold: true,
            size: 24
          }),
          new TextRun({
            text: " Simplificação do processo de abertura de chamados. Agora o usuário só precisa selecionar Produto + Tipo + Criticidade, tornando o processo mais rápido.",
            size: 24
          })
        ]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // ENUMS E REGRAS DE NEGÓCIO
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("5. ENUMS E REGRAS DE NEGÓCIO")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("5.1 Criticality (Visão do Usuário)")]
      }),

      ...createCriticalityTable(),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("5.2 Priority (Sistema)")]
      }),

      ...createPriorityTable(),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("5.3 ServiceType")]
      }),

      ...createServiceTypeTable(),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("5.4 TicketStatus")]
      }),

      ...createTicketStatusTable(),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // SISTEMA DE ROLES E PERMISSÕES
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("6. SISTEMA DE ROLES E CONTEXTOS")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("6.1 Conceito de Mode (Context Switching)")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "O sistema implementa um conceito interessante de 'modo de operação', permitindo que usuários com múltiplos perfis alternem entre contextos sem fazer logout.",
            size: 24
          })
        ]
      }),

      ...createRolesModeTable(),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Implementação:",
            bold: true,
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Modo armazenado em session('mode')")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Middleware mode:X valida o contexto atual")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("SwitchModeController gerencia as transições")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Cada modo redireciona para dashboard específico")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("6.2 Estrutura de Grupos de Suporte")]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "O sistema possui 12 grupos de suporte pré-configurados:",
            size: 24
          })
        ]
      }),

      ...createSupportGroupsTable(),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // PONTOS FORTES
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("7. ANÁLISE DETALHADA")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("7.1 Pontos Fortes da Implementação")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "1. Arquitetura e Organização",
            bold: true,
            size: 26,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Separação clara de responsabilidades: ",
            bold: true
          }),
          new TextRun("Models, Controllers, Services bem organizados")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Uso de Enums modernos (PHP 8.1+): ",
            bold: true
          }),
          new TextRun("Tipo-seguro, com métodos auxiliares (label(), color(), toPriority())")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Convenções do Laravel: ",
            bold: true
          }),
          new TextRun("Seguem as melhores práticas do framework")
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "2. Sistema de SLA",
            bold: true,
            size: 26,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Snapshot de valores: ",
            bold: true
          }),
          new TextRun("Garante que mudanças de SLA não afetam tickets antigos")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Controle de pausas: ",
            bold: true
          }),
          new TextRun("Acumula tempo pausado em segundos, permitindo cálculos precisos")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Métodos auxiliares úteis: ",
            bold: true
          }),
          new TextRun("slaDeadline(), slaIndicator(), pauseSla(), resumeSla()")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Regras customizáveis: ",
            bold: true
          }),
          new TextRun("SLA por Produto + Tipo + Prioridade, com fallback para SLA padrão")
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "3. UX e Simplicidade",
            bold: true,
            size: 26,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Conceito de Criticality: ",
            bold: true
          }),
          new TextRun("Usuário não precisa entender 'prioridade técnica', usa linguagem de impacto no trabalho")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Geração automática de código: ",
            bold: true
          }),
          new TextRun("CHMMYY-NNNNNN único por mês/ano")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Roteamento por grupo de entrada: ",
            bold: true
          }),
          new TextRun("Tickets sempre chegam ao Service Desk primeiro")
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "4. Rastreabilidade ITIL",
            bold: true,
            size: 26,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Histórico de transferências: ",
            bold: true
          }),
          new TextRun("ticket_group_histories registra toda movimentação entre grupos")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Mensagens do sistema: ",
            bold: true
          }),
          new TextRun("addSystemMessage() registra eventos automáticos visíveis ao usuário")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Notas internas: ",
            bold: true
          }),
          new TextRun("is_internal_note permite comunicação entre agentes sem visibilidade do usuário")
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "5. Seeders e Deploy",
            bold: true,
            size: 26,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Bem documentados: ",
            bold: true
          }),
          new TextRun("Comentários explicam a razão de cada registro")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Dados reais: ",
            bold: true
          }),
          new TextRun("Produtos, grupos e SLAs refletem a realidade do Grupo Aste")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Ordem correta: ",
            bold: true
          }),
          new TextRun("DatabaseSeeder executa na sequência correta de dependências")
        ]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // PONTOS DE MELHORIA
      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        children: [new TextRun("7.2 Pontos de Melhoria e Recomendações")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "1. Segurança - Upload de Arquivos",
            bold: true,
            size: 26,
            color: "F0AD4E"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "❌ Problema Atual:",
            bold: true,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("AttachmentService salva arquivos em public/uploads/tickets/")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Arquivos acessíveis diretamente via URL")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Sem controle de acesso por autenticação")]
      }),

      new Paragraph({
        spacing: { before: 180 },
        children: [
          new TextRun({
            text: "✅ Recomendação:",
            bold: true,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Mover uploads para storage/app/tickets/")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Criar controller para download autenticado")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Validar que apenas requester ou agentes atribuídos podem baixar")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "2. Arquitetura - Controller Incompleto",
            bold: true,
            size: 26,
            color: "F0AD4E"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "❌ Problema Atual:",
            bold: true,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("AgentTicketController não foi fornecido")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Impossível analisar lógica completa de atendimento")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Métodos take(), update(), forward() não implementados")]
      }),

      new Paragraph({
        spacing: { before: 180 },
        children: [
          new TextRun({
            text: "✅ Recomendação:",
            bold: true,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Implementar método take() com validação de grupo do agente")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Método forward() deve pausar SLA se encaminhar para especialista")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Adicionar validação: agente só pode pegar tickets do seu grupo")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "3. Testes Automatizados",
            bold: true,
            size: 26,
            color: "F0AD4E"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "❌ Problema Atual:",
            bold: true,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Ausência de testes unitários")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Ausência de testes de integração")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Dificulta refatorações futuras")]
      }),

      new Paragraph({
        spacing: { before: 180 },
        children: [
          new TextRun({
            text: "✅ Recomendação:",
            bold: true,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Criar testes de Feature para fluxo completo de tickets")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Testar cálculo de SLA com pausas")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Testar conversão Criticality → Priority")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Usar factories para criar dados de teste")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "4. Validações e Middlewares",
            bold: true,
            size: 26,
            color: "F0AD4E"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "❌ Problema Atual:",
            bold: true,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Falta validação: agente pertence ao grupo do ticket?")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Falta Form Requests customizados")]
      }),

      new Paragraph({
        spacing: { before: 180 },
        children: [
          new TextRun({
            text: "✅ Recomendação:",
            bold: true,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Criar middleware EnsureAgentBelongsToTicketGroup")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Criar StoreTicketRequest e UpdateTicketRequest")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Validar que tickets não podem ser editados após fechamento")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "5. Observabilidade",
            bold: true,
            size: 26,
            color: "F0AD4E"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        children: [
          new TextRun({
            text: "❌ Problema Atual:",
            bold: true,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Sem logs de auditoria (quem fez o quê e quando)")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Dificulta troubleshooting de problemas")]
      }),

      new Paragraph({
        spacing: { before: 180 },
        children: [
          new TextRun({
            text: "✅ Recomendação:",
            bold: true,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Implementar package spatie/laravel-activitylog")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Registrar mudanças de status, atribuições, transferências")]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [new TextRun("Dashboard de métricas (tickets/hora, SLA compliance)")]
      }),

      // QUEBRA DE PÁGINA
      new Paragraph({ children: [new PageBreak()] }),

      // CONCLUSÃO
      new Paragraph({
        heading: HeadingLevel.HEADING_1,
        children: [new TextRun("8. CONCLUSÃO E PRÓXIMOS PASSOS")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Avaliação Geral: ⭐⭐⭐⭐ (4/5)",
            bold: true,
            size: 28,
            color: "2E5C8A"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "O sistema apresenta uma arquitetura sólida e bem pensada, com implementação correta dos conceitos ITIL e boas práticas do Laravel. O sistema de SLA é particularmente bem executado, demonstrando atenção aos detalhes e entendimento profundo dos requisitos de negócio.",
            size: 24
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Os principais pontos de atenção são:",
            size: 24
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Segurança de uploads",
            bold: true
          }),
          new TextRun(" (mover para storage/)")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Implementação do AgentTicketController",
            bold: true
          }),
          new TextRun(" (pendente)")
        ]
      }),
      new Paragraph({
        numbering: { reference: "bullets", level: 0 },
        children: [
          new TextRun({
            text: "Testes automatizados",
            bold: true
          }),
          new TextRun(" (ausentes)")
        ]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360 },
        children: [new TextRun("8.1 Roadmap Sugerido")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Prioridade ALTA (1-2 semanas)",
            bold: true,
            size: 24,
            color: "D9534F"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Implementar AgentTicketController completo")]
      }),
      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Mover uploads para storage/ com controle de acesso")]
      }),
      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Criar Form Requests para validações")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Prioridade MÉDIA (2-4 semanas)",
            bold: true,
            size: 24,
            color: "F0AD4E"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Implementar testes de Feature")]
      }),
      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Adicionar middleware de validação de grupos")]
      }),
      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Criar dashboard de métricas")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Prioridade BAIXA (backlog)",
            bold: true,
            size: 24,
            color: "5CB85C"
          })
        ]
      }),

      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Implementar activity log (auditoria)")]
      }),
      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Criar API REST para integrações")]
      }),
      new Paragraph({
        numbering: { reference: "numbers", level: 0 },
        children: [new TextRun("Adicionar notificações por e-mail/SMS")]
      }),

      new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 480 },
        children: [new TextRun("8.2 Considerações Finais")]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "Este é um projeto bem estruturado que demonstra conhecimento técnico e atenção aos requisitos de negócio. Com as melhorias sugeridas, estará pronto para produção em ambiente corporativo crítico.",
            size: 24
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 240 },
        children: [
          new TextRun({
            text: "A decisão recente de tornar problem_category_id opcional sugere evolução do escopo baseada em feedback de usuários, o que é um sinal positivo de desenvolvimento iterativo e centrado no usuário.",
            size: 24,
            italics: true
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 360 },
        children: [
          new TextRun({
            text: "_______________________________________________",
            size: 20,
            color: "CCCCCC"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 120 },
        alignment: AlignmentType.CENTER,
        children: [
          new TextRun({
            text: "Documento gerado automaticamente",
            size: 20,
            italics: true,
            color: "999999"
          })
        ]
      }),

      new Paragraph({
        spacing: { before: 60 },
        alignment: AlignmentType.CENTER,
        children: [
          new TextRun({
            text: `Data: ${new Date().toLocaleString('pt-BR')}`,
            size: 20,
            italics: true,
            color: "999999"
          })
        ]
      }),
    ]
  }]
});

// Funções auxiliares para criar tabelas
function createStackTable() {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [3120, 6240],
      rows: [
        // Header
        new TableRow({
          children: [
            new TableCell({
              borders,
              width: { size: 3120, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Componente", bold: true, color: "FFFFFF" })] 
              })]
            }),
            new TableCell({
              borders,
              width: { size: 6240, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Versão/Detalhes", bold: true, color: "FFFFFF" })] 
              })]
            })
          ]
        }),
        // Rows
        createTableRow("Framework", "Laravel 12"),
        createTableRow("Linguagem", "PHP 8.1+"),
        createTableRow("Banco de Dados", "MySQL"),
        createTableRow("Frontend", "Blade Templates + Tailwind CSS"),
        createTableRow("Autenticação", "Laravel Breeze"),
        createTableRow("Build Tools", "Vite + NPM"),
      ]
    })
  ];
}

function createTicketsTable() {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [2800, 2800, 3760],
      rows: [
        new TableRow({
          children: [
            new TableCell({
              borders,
              width: { size: 2800, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Campo", bold: true, color: "FFFFFF" })] 
              })]
            }),
            new TableCell({
              borders,
              width: { size: 2800, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Tipo", bold: true, color: "FFFFFF" })] 
              })]
            }),
            new TableCell({
              borders,
              width: { size: 3760, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Descrição", bold: true, color: "FFFFFF" })] 
              })]
            })
          ]
        }),
        createTableRow3Cols("code", "string(20)", "Código único CH0126-000001"),
        createTableRow3Cols("subject", "string", "Assunto do chamado"),
        createTableRow3Cols("description", "longtext", "Descrição HTML com imagens"),
        createTableRow3Cols("product_id", "FK", "Sistema afetado"),
        createTableRow3Cols("problem_category_id", "FK (nullable)", "Categoria - TORNADO NULLABLE em 29/01"),
        createTableRow3Cols("service_type", "enum", "incident | service_request | etc"),
        createTableRow3Cols("priority", "enum", "low | medium | high | critical"),
        createTableRow3Cols("status", "enum", "open | in_progress | waiting_user | etc"),
        createTableRow3Cols("sla_started_at", "timestamp", "Início do contador SLA"),
        createTableRow3Cols("sla_paused_at", "timestamp", "Momento da pausa (se pausado)"),
        createTableRow3Cols("sla_paused_seconds", "int", "Tempo acumulado em pausa"),
        createTableRow3Cols("requester_id", "FK", "Usuário solicitante"),
        createTableRow3Cols("current_group_id", "FK", "Grupo atual responsável"),
        createTableRow3Cols("assigned_to", "FK", "Agente atribuído"),
      ]
    })
  ];
}

function createStatusFlowTable() {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [2340, 7020],
      rows: [
        new TableRow({
          children: [
            new TableCell({
              borders,
              width: { size: 2340, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Status", bold: true, color: "FFFFFF" })] 
              })]
            }),
            new TableCell({
              borders,
              width: { size: 7020, type: WidthType.DXA },
              shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
              margins: { top: 100, bottom: 100, left: 120, right: 120 },
              children: [new Paragraph({ 
                children: [new TextRun({ text: "Descrição", bold: true, color: "FFFFFF" })] 
              })]
            })
          ]
        }),
        createTableRow("OPEN", "Chamado criado, aguardando ser assumido por um agente"),
        createTableRow("IN_PROGRESS", "Agente assumiu e está trabalhando na resolução"),
        createTableRow("WAITING_USER", "Agente aguarda resposta/ação do usuário (SLA PAUSADO)"),
        createTableRow("RESOLVED", "Problema resolvido, aguardando confirmação do usuário"),
        createTableRow("CLOSED", "Chamado finalizado e fechado (status final)"),
      ]
    })
  ];
}

function createCriticalityTable() {
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [2340, 7020],
      rows: [
        createHeaderRow2Cols("Nível", "Descrição para o Usuário"),
        createTableRow("LOW", "Posso trabalhar normalmente"),
        createTableRow("MEDIUM", "Trabalho prejudicado, mas consigo continuar"),
        createTableRow("HIGH", "Trabalho quase parado"),
        createTableRow("CRITICAL", "Trabalho totalmente parado"),
      ]
    })
  ];
}

function createPriorityTable() {
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [1872, 1872, 1872, 3744],
      rows: [
        createHeaderRow4Cols("Nível", "Label", "Peso", "Cor (Badge)"),
        createTableRow4Cols("LOW", "Baixa", "1", "Verde"),
        createTableRow4Cols("MEDIUM", "Média", "2", "Amarelo"),
        createTableRow4Cols("HIGH", "Alta", "3", "Laranja"),
        createTableRow4Cols("CRITICAL", "Crítica", "4", "Vermelho"),
      ]
    })
  ];
}

function createServiceTypeTable() {
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [3120, 6240],
      rows: [
        createHeaderRow2Cols("Tipo", "Descrição ITIL"),
        createTableRow("INCIDENT", "Incidente - Interrupção não planejada"),
        createTableRow("SERVICE_REQUEST", "Solicitação de Serviço - Mudança padrão"),
        createTableRow("PURCHASE_REQUEST", "Solicitação de Compra"),
        createTableRow("IMPROVEMENT", "Melhoria - Sugestão de aprimoramento"),
      ]
    })
  ];
}

function createTicketStatusTable() {
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [2340, 7020],
      rows: [
        createHeaderRow2Cols("Status", "Significado"),
        createTableRow("OPEN", "Aberto - Aguardando atribuição"),
        createTableRow("IN_PROGRESS", "Em Atendimento - Agente trabalhando"),
        createTableRow("WAITING_USER", "Aguardando Usuário - SLA pausado"),
        createTableRow("RESOLVED", "Resolvido - Aguardando validação"),
        createTableRow("CLOSED", "Fechado - Finalizado (status final)"),
      ]
    })
  ];
}

function createRolesModeTable() {
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [1872, 3744, 3744],
      rows: [
        createHeaderRow3Cols("Role", "Modos Permitidos", "Dashboard Padrão"),
        createTableRow3Cols("admin", "admin, agent, user", "admin.dashboard"),
        createTableRow3Cols("agent", "agent", "agent.queue"),
        createTableRow3Cols("user", "user", "user.home"),
      ]
    })
  ];
}

function createSupportGroupsTable() {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return [
    new Table({
      width: { size: 100, type: WidthType.PERCENTAGE },
      columnWidths: [3120, 6240],
      rows: [
        createHeaderRow2Cols("Grupo", "Responsabilidade"),
        createTableRow("Service Desk ⭐", "Nível 1 - Ponto de entrada (is_entry_point)"),
        createTableRow("Suporte ERP SIGE", "Nível 2 - SIGE, fiscal, pedidos"),
        createTableRow("Suporte PDV", "Sistema de vendas e operações de loja"),
        createTableRow("Suporte TI Acesso", "Login, rede, e-mails, acessos"),
        createTableRow("Suporte TI Email", "Configuração de caixas de e-mail"),
        createTableRow("Suporte TI", "Redes, hardware, impressoras, telefonia"),
        createTableRow("Suporte OmniChannel", "Integrador de canais de venda"),
        createTableRow("Suporte E-commerce", "Plataforma online, pedidos web"),
        createTableRow("Suporte TI Equipamentos", "Manutenção e substituição"),
        createTableRow("Suporte Vejo Varejo", "Plataforma Vejo Varejo"),
        createTableRow("Suporte Webb Loja", "Social Seller, notas fiscais"),
        createTableRow("Suporte Centelha", "Plataforma B2B atacado"),
      ]
    })
  ];
}

// Helper functions
function createHeaderRow2Cols(col1, col2) {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: 3120, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col1, bold: true, color: "FFFFFF" })] 
        })]
      }),
      new TableCell({
        borders,
        width: { size: 6240, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col2, bold: true, color: "FFFFFF" })] 
        })]
      })
    ]
  });
}

function createHeaderRow3Cols(col1, col2, col3) {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col1, bold: true, color: "FFFFFF" })] 
        })]
      }),
      new TableCell({
        borders,
        width: { size: 3744, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col2, bold: true, color: "FFFFFF" })] 
        })]
      }),
      new TableCell({
        borders,
        width: { size: 3744, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col3, bold: true, color: "FFFFFF" })] 
        })]
      })
    ]
  });
}

function createHeaderRow4Cols(col1, col2, col3, col4) {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col1, bold: true, color: "FFFFFF" })] 
        })]
      }),
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col2, bold: true, color: "FFFFFF" })] 
        })]
      }),
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col3, bold: true, color: "FFFFFF" })] 
        })]
      }),
      new TableCell({
        borders,
        width: { size: 3744, type: WidthType.DXA },
        shading: { fill: "2E5C8A", type: ShadingType.CLEAR },
        margins: { top: 100, bottom: 100, left: 120, right: 120 },
        children: [new Paragraph({ 
          children: [new TextRun({ text: col4, bold: true, color: "FFFFFF" })] 
        })]
      })
    ]
  });
}

function createTableRow(col1, col2) {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: 3120, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col1)] })]
      }),
      new TableCell({
        borders,
        width: { size: 6240, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col2)] })]
      })
    ]
  });
}

function createTableRow3Cols(col1, col2, col3) {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: 2800, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col1)] })]
      }),
      new TableCell({
        borders,
        width: { size: 2800, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col2)] })]
      }),
      new TableCell({
        borders,
        width: { size: 3760, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col3)] })]
      })
    ]
  });
}

function createTableRow4Cols(col1, col2, col3, col4) {
  const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
  const borders = { top: border, bottom: border, left: border, right: border };
  
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col1)] })]
      }),
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col2)] })]
      }),
      new TableCell({
        borders,
        width: { size: 1872, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col3)] })]
      }),
      new TableCell({
        borders,
        width: { size: 3744, type: WidthType.DXA },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({ children: [new TextRun(col4)] })]
      })
    ]
  });
}

Packer.toBuffer(doc).then(buffer => {
  fs.writeFileSync("/mnt/user-data/outputs/Analise_Tecnica_HelpDesk_GrupoAste.docx", buffer);
  console.log("Documento criado com sucesso!");
});
