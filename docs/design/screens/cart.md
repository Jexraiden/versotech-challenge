# Carrinho — página dedicada

## Objetivo

Revisar e ajustar o pedido antes do checkout.

## Conteúdo

- Itens com código, nome, preço unitário, quantidade editável, total por linha e ação remover, conforme dados disponíveis.
- Resumo com subtotal, descontos aplicáveis e total calculado pelo backend.
- Ação `Continuar para checkout`; o link `Catálogo` do cabeçalho global permite voltar às compras.

## Layout

Desktop: itens à esquerda e resumo à direita. Mobile: itens empilhados e resumo abaixo, com ação principal bem visível.

## Comportamento

- Recalcular após alterações, respeitando validações de estoque e regras comerciais.
- Preservar carrinho ao voltar ao catálogo.
- Carrinho vazio: mensagem orientando a adicionar produtos, com retorno pelo link `Catálogo` do cabeçalho global.
- Comunicar erros de atualização e evitar totais inconsistentes.
