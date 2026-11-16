# Catálogo — tela prioritária

## Objetivo

Permitir localizar produtos e comprar diretamente da lista, sem abrir detalhes.

## Desktop

- Cabeçalho com navegação, busca, pedidos/conta e carrinho conforme funcionalidades existentes.
- Lista compacta **sem imagens**, com código, nome, preço aplicável, disponibilidade, quantidade e ação de adicionar.
- Busca por nome parcial, SKU ou EAN, filtro por categoria e paginação; mensagens para zero resultados e falhas.
- Preço, estoque e outras informações devem refletir a API e permissões da empresa.

## Mobile

- Transformar linhas em blocos compactos sem imagens.
- Código, nome, preço, disponibilidade, controle de quantidade e botão acessíveis sem rolagem horizontal.

## Compra e barra fixa

- Adicionar mantém usuário no catálogo; feedback de sucesso ou falha.
- Abrir o detalhe permite consultar informações do produto; adicionar ao carrinho ocorre somente na lista do catálogo.
- Se item já existe, incrementar quantidade.
- Barra fixa inferior somente com itens: total de unidades, subtotal e `Ver carrinho`.
- Reservar espaço para não encobrir o último produto; considerar safe areas.

## Estados

Carregando, lista vazia, busca sem resultados, produto indisponível, falha de atualização, adição em andamento.
