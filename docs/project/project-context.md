# Versotech — Contexto do projeto

**Versão:** 1.0 · **Data:** 15/11/2026 · **Prazo do desafio:** 17/11/2026

## Objetivo

Implementar um MVP funcional de e-commerce B2B a partir dos dados fornecidos pela Versotech, demonstrando decisões técnicas e de produto, qualidade de código, usabilidade e capacidade de explicar e evoluir a solução.

O projeto tem prazo curto e concorre com outros compromissos. Priorizar a jornada de compra completa e as regras comerciais; evitar complexidade sem benefício imediato.

## Stack e ambiente

- Backend: PHP + Laravel
- Frontend: React + TypeScript + Vite + Inertia.js
- Banco de dados: PostgreSQL
- Autenticação: sessão web nativa do Laravel, sem Sanctum
- Desenvolvimento: Windows/WSL2, VS Code, Codex e Laravel Sail
- Arquitetura: monólito Laravel com páginas React via Inertia
- Sem API REST pública no MVP
- Sem Redis no MVP
- Sem imagens de produtos no MVP

O Sail já utiliza Docker Compose. Uma configuração Compose independente, adequada à implantação, é possibilidade pós-MVP.

## Princípios

1. Backend como fonte de verdade para preços, estoque, crédito e autorização.
2. Isolamento de dados por empresa autenticada.
3. Transação única na confirmação do pedido.
4. Testes prioritários para regras críticas e concorrência.
5. Incrementos pequenos, revisáveis e com commits manuais.
6. IA é ferramenta de desenvolvimento; decisões e validação são responsabilidade do desenvolvedor.
7. Documentação curta e atualizada quando decisões mudarem.

## Processo SDD leve

Requisito → critério de aceitação → implementação → testes → revisão → commit.

Documentos principais:

- `docs/project/requirements.md`
- `docs/architecture/system-design.md`
- `docs/adr/`
- `AGENTS.md`
- `README.md` (a ser preenchido com instalação, execução, testes e demonstração)

## Fora do escopo

Cadastro público, painel administrativo, pagamentos reais, emissão de boletos/PIX, frete, ERP, cancelamentos e reembolsos, Redis, filas, API REST pública, autenticação federada, MFA e imagens de produtos.

## Entregáveis

- Repositório Git com histórico de commits.
- Projeto executável localmente.
- Apresentação técnica de aproximadamente 40 minutos, com demonstração e justificativa de trade-offs.
