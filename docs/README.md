# Documentação — Versotech B2B Commerce

Esta documentação segue um **SDD leve**: requisitos, arquitetura e decisões explícitas, com foco na entrega de um MVP funcional e demonstrável.

## Navegação

| Documento | Objetivo |
|---|---|
| [Contexto do projeto](project/project-context.md) | Objetivo, escopo, stack e prioridades |
| [Requisitos e critérios de aceitação](project/requirements.md) | Regras de negócio e cenários verificáveis |
| [Plano de implementação incremental](implementation/README.md) | Seis incrementos e gates de aprovação |
| [Prompts de workflow](workflows/implement.md) | Implementação, revisão adversarial e validação |
| [Arquitetura e System Design](architecture/README.md) | Componentes, modelo de dados, precificação e checkout transacional |
| [ADR-004 — Autenticação web sem Sanctum](adr/004-web-auth-without-sanctum.md) | Justificativa para sessão Laravel com Inertia |
| [ADR-005 — Segurança da autenticação](adr/005-auth-security-scope.md) | Riscos, controles mínimos e alternativas futuras |
| [ADR-006 — Imagens dos produtos](adr/006-product-images.md) | Ausência de imagens no MVP e evolução com S3/CDN |

## Fluxo de trabalho

1. Consultar os requisitos e seus critérios de aceitação.
2. Verificar a arquitetura e os ADRs relacionados.
3. Implementar um incremento pequeno e testável.
4. Revisar adversarialmente em sessão separada, realizar revisão humana, executar validação formal e obter aprovação antes do commit.
5. Atualizar a documentação apenas quando houver mudança relevante.

## Escopo

O MVP utiliza **Laravel, React, TypeScript, Inertia e PostgreSQL**. Prioriza autenticação de demonstração, catálogo B2B, precificação por empresa, carrinho em sessão e checkout transacional com controle de estoque e crédito.

A aplicação não implementa API REST pública, Sanctum, Redis, integração de pagamento ou imagens de produtos neste estágio.
