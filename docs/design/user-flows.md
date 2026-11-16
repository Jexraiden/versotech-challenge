# Jornadas e regras de interação

## Fluxo principal

1. Autenticar usuário e identificar empresa.
2. Acessar catálogo; pesquisar por nome, SKU ou EAN, filtrar por categoria e navegar pelas páginas.
3. Abrir detalhes de um produto para consultar suas informações; para comprar, retornar à lista do catálogo.
4. Definir quantidade e adicionar diretamente pela lista.
5. Permanecer no catálogo e visualizar barra fixa de resumo do carrinho.
6. Abrir página dedicada do carrinho; editar/remover itens e conferir valores.
7. Prosseguir para checkout de página única.
8. Selecionar condição comercial disponível e confirmar.
9. Receber confirmação com identificador do pedido.

## Catálogo e barra fixa

- Adicionar não redireciona.
- A tela de detalhes do produto é informativa; a ação de adicionar ao carrinho permanece na lista do catálogo.
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

- Histórico de pedidos e detalhes é melhoria futura se houver tempo, fora dos critérios obrigatórios do MVP.

- Histórico: número, data, total e situação, conforme dados disponíveis.
- Detalhes: itens, quantidades, valores, descontos, condição e status disponíveis.
- Não representar etapas logísticas fictícias.
