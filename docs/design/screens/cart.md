# Carrinho — página dedicada

## Objetivo

Revisar e ajustar o pedido antes do checkout.

## Conteúdo

- Itens com código, nome, preço unitário, quantidade editável, total por linha e ação remover, conforme dados disponíveis.
- Resumo com subtotal, descontos aplicáveis e total calculado pelo backend.
- Ações `Continuar comprando` e `Continuar para checkout`.

## Layout

Desktop: itens à esquerda e resumo à direita. Mobile: itens empilhados e resumo abaixo, com ação principal bem visível.

## Comportamento

- Recalcular após alterações, respeitando validações de estoque e regras comerciais.
- Preservar carrinho ao voltar ao catálogo.
- Carrinho vazio: mensagem e retorno ao catálogo.
- Comunicar erros de atualização e evitar totais inconsistentes.
