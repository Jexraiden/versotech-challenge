# Jornadas e regras de interação

## Fluxo principal

1. Autenticar usuário e identificar empresa.
2. Acessar catálogo; pesquisar por nome, SKU ou EAN, filtrar por categoria e navegar pelas páginas.
3. Abrir detalhes de um produto para consultar suas informações; usar `Catálogo` no cabeçalho para retornar à lista e comprar.
4. Definir quantidade e adicionar diretamente pela lista.
5. Usar a navegação superior global para acessar catálogo, busca, conta ou carrinho; ao adicionar um item, receber confirmação discreta.
6. Abrir página dedicada do carrinho; editar/remover itens e conferir valores.
7. Prosseguir para checkout de página única.
8. Selecionar condição comercial disponível e confirmar.
9. Receber confirmação com identificador do pedido.

## Navegação global e catálogo

- A barra superior fica fixa no topo durante a rolagem em todas as telas autenticadas e apresenta marca `Ecommerce B2B`, acesso ao catálogo, filtros de categoria e busca, menu do usuário e carrinho com badge de unidades. A tela de login não a apresenta.
- Ao mudar de tela, campo de busca e seletor de categoria reiniciam. Enviar a busca abre o catálogo com os filtros submetidos aplicados; os campos do cabeçalho reiniciam após a navegação.
- O menu do usuário mostra `Ver pedidos` e `Sair da conta`, nessa ordem, sem escurecer o fundo.
- `Ver pedidos` abre a listagem paginada dos pedidos do usuário autenticado, sem filtros adicionais, limitada à identidade e empresa autenticadas.
- A navegação e os controles reorganizam-se em telas pequenas sem rolagem horizontal.
- A busca envia os termos e a categoria selecionada para a tela do catálogo.

- Adicionar não redireciona.
- A tela de detalhes do produto é informativa; a ação de adicionar ao carrinho permanece na lista do catálogo.
- O acesso ao carrinho na barra superior mostra o total de unidades em badge e abre a página dedicada.
- O link `Catálogo` no cabeçalho é o caminho de volta às compras; não repetir links de retorno/continuação ao catálogo em outras telas.
- Adicionar produto já existente incrementa a quantidade em vez de criar linha duplicada.
- Comunicar adição bem-sucedida e erros com feedback discreto e acessível; sucesso não deve escurecer a página.

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

- A listagem dos pedidos do usuário autenticado está disponível pelo menu da conta, sem filtros e com paginação.

- Histórico: número, data, total e situação, conforme dados disponíveis.
- Detalhes: itens, quantidades, valores, descontos, condição e status disponíveis.
- Não representar etapas logísticas fictícias.
