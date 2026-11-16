# INC-05 — Checkout e pedidos transacionais

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** Incrementos anteriores aprovados

## Objetivo

Fluxo completo cria pedido e itens com snapshots, baixa estoque e consome crédito a prazo de forma atômica e idempotente.

## Implementação esperada

Resumo e condição PIX/BOL28/BOL2856; mínimos sobre subtotal; acréscimo de 2,5% para BOL2856; revalidação integral; locks determinísticos; chave de idempotência única; confirmação e limpeza do carrinho somente após commit.

## Critérios de aceitação

Bloqueada não compra; PIX não consome crédito; boletos consomem total final; estoque/crédito insuficientes rejeitam; rollback integral; reenvio não duplica; concorrência não excede recursos.

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
