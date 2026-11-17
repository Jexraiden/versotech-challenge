# Detalhe do produto

## Objetivo

Consultar as informações comerciais disponíveis de um produto antes de decidir adicioná-lo ao carrinho pela lista do catálogo.

## Conteúdo

- Nome, descrição quando existente, marca, categoria, SKU, preço aplicável e disponibilidade, conforme dados retornados pelo backend.
- Não apresentar imagens no MVP.

## Comportamento

- O detalhe é informativo; adicionar ao carrinho continua sendo uma ação exclusiva da lista do catálogo.
- Produto inativo, sem preço aplicável ou sem estoque não deve ser apresentado como comprável.
- Usar o link `Catálogo` do cabeçalho global para retornar à lista; não repetir link de retorno na página.
- Incluir estados de carregamento, erro e produto indisponível.

## Layout

Responsivo em desktop e smartphone, com informações legíveis, rótulos claros, foco visível e sem rolagem horizontal.
