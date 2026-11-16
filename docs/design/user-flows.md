# Jornadas e regras de interação

## Fluxo principal

1. Autenticar usuário e identificar empresa.
2. Acessar catálogo; pesquisar por nome ou código.
3. Definir quantidade e adicionar diretamente pela lista.
4. Permanecer no catálogo e visualizar barra fixa de resumo do carrinho.
5. Abrir página dedicada do carrinho; editar/remover itens e conferir valores.
6. Prosseguir para checkout de página única.
7. Selecionar condição comercial de pagamento disponível e confirmar.
8. Receber confirmação com identificador do pedido.
9. Consultar histórico e detalhes em `Meus pedidos`.

## Catálogo e barra fixa

- Adicionar não redireciona.
- Barra fixa inferior aparece somente com carrinho não vazio; mostra total de unidades e subtotal calculado conforme regras disponíveis, com ação `Ver carrinho`.
- Adicionar produto já existente incrementa a quantidade em vez de criar linha duplicada.
- No mobile, a barra não deve encobrir conteúdo nem controles; reservar espaço inferior e respeitar safe areas.
- Comunicar adição e erros com feedback discreto e acessível.

## Carrinho

- Página dedicada; alterar quantidade, remover itens e voltar ao catálogo preservando o carrinho.
- Recalcular valores com fonte confiável; informar mudanças e impedimentos.
- Carrinho vazio deve orientar o retorno ao catálogo.

## Checkout

- Sem gateway de pagamento, Pix ou campos de cartão.
- Revisar pedido, selecionar condição comercial permitida e confirmar.
- Exibir carregamento durante envio e evitar confirmações duplicadas.
- Revalidar estoque, preços, promoções, crédito e bloqueios no backend.
- Em erro, preservar informações e indicar correção; sucesso apenas após confirmação do servidor.

## Pós-compra

- Histórico: número, data, total e situação, conforme dados disponíveis.
- Detalhes: itens, quantidades, valores, descontos, condição e status disponíveis.
- Não representar etapas logísticas fictícias.
