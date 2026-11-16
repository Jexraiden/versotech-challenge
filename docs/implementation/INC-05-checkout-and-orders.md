# INC-05 — Checkout e pedidos transacionais

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** INC-04 aprovado (itens do carrinho); INC-02 aprovado (autorização); INC-03 aprovado (preços comerciais); usa as garantias transacionais definidas em requirements e system design.

**Especificações de UI/UX e frontend:** [checkout](../design/screens/checkout.md), [fluxos de usuário](../design/user-flows.md) (checkout e confirmação), [design system](../design/design-system.md) e [diretrizes frontend](../design/frontend-guidelines.md). A tela de [pedidos](../design/screens/orders.md) descreve melhoria opcional fora dos critérios obrigatórios do MVP.

## Objetivo

Fluxo completo cria pedido e itens com snapshots, baixa estoque e consome crédito a prazo de forma atômica e idempotente.

## Implementação esperada

Resumo e condições comerciais PIX, Boleto 28 dias (BOL28) e Boleto 28/56 dias (BOL2856), conforme `data/condicoes_pagamento.json`; mínimos sobre subtotal; acréscimo de 2,5% para BOL2856; sem gateway; revalidação integral; locks determinísticos; chave de idempotência única; confirmação e limpeza do carrinho somente após commit.

## Critérios de aceitação

Bloqueada não compra; PIX não consome crédito; boletos consomem total final; estoque/crédito insuficientes rejeitam; rollback integral; reenvio não duplica; concorrência não excede recursos.

### Critérios de aceite de UI/UX

- [ ] Checkout em página única revisa itens/totais e apresenta somente condições permitidas pelo backend, incluindo restrições comerciais sem inventar opções de pagamento.
- [ ] Confirmação mostra estado de envio e impede cliques repetidos; falha preserva dados e explica o próximo passo; sucesso só é indicado após resposta confirmada e apresenta identificador/resumo.
- [ ] Revisão inclui empresa, endereço cadastrado quando disponível, itens, condição e total, conforme dados existentes; erros de validação são associados a uma ação compreensível.
- [ ] Checkout oferece somente PIX, Boleto 28 dias e Boleto 28/56 dias quando elegíveis; não solicita dados de pagamento nem sugere processamento por gateway.
- [ ] Checkout é utilizável em desktop e smartphone, com rótulos, foco visível e feedback que não depende apenas de cor.

## Fora do escopo

Não integrar gateway, emitir boleto, calcular frete ou implementar cancelamento.

## Foco da revisão adversarial

Priorizar race conditions, deadlocks, colisão de checkout_key, reenvio com payload divergente, snapshots, rounding, cross-company access e rollback.

## Evidências para aprovação

- [ ] Implementação concluída e explicada
- [ ] Revisão adversarial em sessão separada, com achados classificados por severidade
- [ ] Achados críticos/altos corrigidos ou explicitamente aceitos pelo desenvolvedor
- [ ] Revisão manual aprovada
- [ ] Testes formais executados com comandos e resultados registrados
- [ ] Critérios de aceitação validados
- [ ] Aprovação explícita do desenvolvedor
- [ ] Commit manual após aprovação

## Registro da execução

**Arquivos alterados:** _preencher_  
**Achados da revisão:** _preencher_  
**Testes e resultados:** _preencher_  
**Pendências / decisões:** _preencher_  
**Aprovação:** _pendente_
