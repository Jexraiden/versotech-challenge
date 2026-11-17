# Catálogo — tela prioritária

## Objetivo

Permitir localizar produtos e comprar diretamente da lista, sem abrir detalhes.

## Desktop

- Cabeçalho global fixo com marca `Ecommerce B2B`, link `Catálogo`, seletor de categoria, busca, botão `Buscar`, menu `Olá, {nome}` e botão `Ver Carrinho` com badge da quantidade de unidades.
- Lista compacta **sem imagens**, com código, nome, preço aplicável, disponibilidade, quantidade e ação de adicionar.
- Busca por nome parcial, SKU ou EAN, filtro por categoria e paginação; mensagens para zero resultados e falhas.
- Preço, estoque e outras informações devem refletir a API e permissões da empresa.

## Mobile

- Transformar linhas em blocos compactos sem imagens.
- Código, nome, preço, disponibilidade, controle de quantidade e botão acessíveis sem rolagem horizontal.

## Compra e barra fixa

- Adicionar mantém usuário no catálogo; sucesso mostra notificação discreta com indicador, título, mensagem e opção para dispensar; falhas continuam visíveis e acessíveis.
- Abrir o detalhe permite consultar informações do produto; adicionar ao carrinho ocorre somente na lista do catálogo.
- Se item já existe, incrementar quantidade.
- Não exibir barra fixa inferior de resumo do carrinho.
- Em smartphone, reorganizar o cabeçalho sem rolagem horizontal e manter as ações acessíveis.
- O link `Catálogo` no cabeçalho substitui links de retorno ao catálogo em outras telas.

## Estados

Carregando, lista vazia, busca sem resultados, produto indisponível, falha de atualização, adição em andamento.
