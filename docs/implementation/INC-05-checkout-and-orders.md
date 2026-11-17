# INC-05 — Checkout e pedidos transacionais

**Status:** Concluído; commit manual realizado
**Fase atual:** Concluída
**Dependências:** INC-04 aprovado (itens do carrinho); INC-02 aprovado (autorização); INC-03 aprovado (preços comerciais); usa as garantias transacionais definidas em requirements e system design.

**Especificações de UI/UX e frontend:** [checkout](../design/screens/checkout.md), [fluxos de usuário](../design/user-flows.md) (checkout e confirmação), [design system](../design/design-system.md) e [diretrizes frontend](../design/frontend-guidelines.md). A listagem de [pedidos](../design/screens/orders.md) estava fora do escopo deste incremento e foi implementada no INC-07.

## Objetivo

Fluxo completo cria pedido e itens com snapshots, baixa estoque e consome crédito a prazo de forma atômica e idempotente.

## Implementação esperada

Resumo e condições comerciais PIX, Boleto 28 dias (BOL28) e Boleto 28/56 dias (BOL2856), conforme `data/condicoes_pagamento.json`; mínimos sobre subtotal; acréscimo de 2,5% para BOL2856; sem gateway; revalidação integral; locks determinísticos; chave de idempotência única; confirmação e limpeza do carrinho somente após commit.

## Critérios de aceitação

Bloqueada não compra; PIX não consome crédito; boletos consomem total final; estoque/crédito insuficientes rejeitam; rollback integral; reenvio não duplica; concorrência não excede recursos.

### Critérios de aceite de UI/UX

- [x] Checkout em página única revisa itens/totais e apresenta somente condições permitidas pelo backend, incluindo restrições comerciais sem inventar opções de pagamento.
- [x] Confirmação mostra estado de envio e impede cliques repetidos; falha preserva dados e explica o próximo passo; sucesso só é indicado após resposta confirmada e apresenta identificador/resumo.
- [x] Revisão inclui empresa, endereço de entrega estruturado obrigatório informado pelo usuário (CEP, UF, Cidade, Bairro e Logradouro; Complemento opcional), itens, condição e total; erros de validação são associados a uma ação compreensível.
- [x] Checkout oferece somente PIX, Boleto 28 dias e Boleto 28/56 dias quando elegíveis; não solicita dados de pagamento nem sugere processamento por gateway.
- [x] Checkout é utilizável em desktop e smartphone, com rótulos, foco visível e feedback que não depende apenas de cor.

## Fora do escopo

Não integrar gateway, emitir boleto, calcular frete ou implementar cancelamento.

## Foco da revisão adversarial

Priorizar race conditions, deadlocks, colisão de checkout_key, reenvio com payload divergente, snapshots, rounding, cross-company access e rollback.

## Evidências para aprovação

- [x] Implementação concluída e explicada
- [x] Revisão adversarial realizada; revisão adicional após o ajuste visual dispensada pelo desenvolvedor
- [x] Achados críticos/altos corrigidos ou explicitamente aceitos pelo desenvolvedor (nenhum achado crítico/alto; achado visual baixo corrigido)
- [x] Revisão manual aprovada pelo desenvolvedor
- [x] Testes formais executados com comandos e resultados registrados
- [x] Critérios de aceitação validados pelo desenvolvedor e pela suíte automatizada, com a limitação de concorrência abaixo reconhecida
- [x] Aprovação explícita do desenvolvedor
- [x] Commit manual após aprovação (`e4a64df` — `feat(checkout): complete transactional checkout and order flow`)

### Limitação conhecida de validação

A suíte PHPUnit padrão executada no INC-05 usou SQLite em memória e não comprovou a contenção dos locks (`lockForUpdate`) no PostgreSQL. Naquele momento, a disputa simultânea por estoque, crédito ou `checkout_key` permanecia **não verificada**. No INC-06, o desenvolvedor informou que executou validação manual adicional da concorrência no PostgreSQL; os detalhes disponíveis estão no registro do INC-06. Essa informação posterior não altera o resultado histórico da suíte do INC-05.

## Registro da execução

**Arquivos alterados:** implementação de checkout/pedidos, migrations, telas e testes conforme árvore de trabalho; ajuste de altura dos campos em `resources/js/Pages/Checkout/Checkout.module.css`.  
**Achados da revisão:** um achado Low sobre campos de formulário excessivamente altos foi corrigido. O desenvolvedor dispensou nova revisão adversarial após a correção.  
**Testes e resultados:** `docker compose run --rm --no-deps laravel.test php artisan test` — 57 testes passaram (578 assertions), em 37,82 s. Uma tentativa anterior com `--no-build` não foi aceita pela versão instalada do Docker Compose (`unknown flag: --no-build`).  
**Verificações manuais:** o desenvolvedor confirmou o funcionamento da tela e a realização das verificações manuais.  
**Pendências / decisões:** na conclusão do INC-05, a concorrência real em PostgreSQL permanecia não verificada. O desenvolvedor informou validação manual posterior no INC-06; ver registro desse incremento. Commit manual realizado posteriormente.
**Aprovação:** aprovada explicitamente pelo desenvolvedor em 16/11/2026.
