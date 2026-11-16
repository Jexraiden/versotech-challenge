# Visão do produto e escopo

## Visão

Plataforma de compras B2B para pessoas que trabalham em lojas compradoras. O usuário autenticado identifica produtos, informa quantidades, monta um pedido, seleciona uma condição comercial de pagamento e confirma o pedido.

## Direção

- Experiência de e-commerce simples, minimalista, clean e comercial contemporânea.
- Desktop-first, totalmente responsiva para smartphones.
- Eficiência e rapidez para compras recorrentes, sem sacrificar clareza.

## MVP aprovado

- Autenticação e identificação do contexto da empresa compradora.
- Catálogo em **lista compacta sem imagens**, com busca por nome, SKU ou EAN, filtro por categoria, paginação, informações comerciais e compra direta da lista.
- Página de detalhes do produto para consultar informações comerciais; adicionar ao carrinho continua disponível somente na lista do catálogo.
- Adição ao carrinho sem sair do catálogo; barra fixa de resumo quando há itens.
- Página dedicada de carrinho, com edição de quantidades, remoção e atualização de totais.
- Checkout em página única com revisão, seleção de condição de pagamento permitida e confirmação do pedido.
- Confirmação do pedido. Histórico de pedidos com detalhes consultáveis é uma melhoria desejável se houver tempo, mas não é requisito do MVP.
- Responsividade desktop/mobile.

## Regras de negócio a respeitar

- Preços conforme tabela da empresa; estoque e promoções conforme dados e regras efetivamente disponíveis.
- Considerar bloqueios, crédito e condições de pagamento conforme validação do backend.
- Validar novamente dados comerciais ao confirmar pedido; nunca presumir sucesso antes da resposta do servidor.

## Fora do MVP

- Gateway de pagamento online (Pix/cartão).
- Rastreamento logístico detalhado.
- Imagens, grade de produtos e adicionar ao carrinho a partir da página de detalhes.

## Pendências de alinhamento técnico

- Confirmar campos exatos retornados pela API, sem inventar dados.
- Confirmar semântica de subtotal, descontos, disponibilidade e crédito.
- Confirmar status de pedido existentes e persistência do carrinho.
