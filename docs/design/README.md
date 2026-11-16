# Design — Plataforma de compras B2B

Esta pasta é a fonte de referência para decisões de produto, UX e UI do MVP. Consulte estes arquivos antes de implementar ou alterar interfaces.

## Ordem de leitura

1. `product.md` — visão, público, escopo e limites.
2. `user-flows.md` — jornadas e regras de interação.
3. `design-system.md` — linguagem visual e responsividade.
4. `frontend-guidelines.md` — convenções de React, TypeScript, CSS e responsividade.
5. `screens/*.md` — comportamento esperado por tela, incluindo [detalhe do produto](./screens/product-detail.md).
6. `roadmap.md` — ideias futuras, **não implementar no MVP**.

## Regras para implementação

- Tratar os documentos como especificações de experiência, não como substitutos do contrato de API ou das regras de negócio existentes.
- Não inventar campos, estados, endpoints, modalidades de pagamento ou rastreamento que não existam no backend.
- Em caso de conflito com o enunciado do desafio ou requisitos funcionais, registrar a divergência e solicitar decisão antes de alterar o escopo.
- Não implementar itens de `roadmap.md` sem aprovação explícita.
- Usar dados reais da API; valores fictícios são permitidos apenas em protótipos e testes.
- Garantir estados de carregamento, vazio, erro e sucesso, acessibilidade básica e funcionamento em desktop e smartphone.
- Estas especificações descrevem o alvo do produto, não afirmam que as funcionalidades já foram implementadas no INC1.
