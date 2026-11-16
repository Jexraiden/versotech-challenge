# Design system inicial

## Identidade aprovada

Comercial contemporânea: visual minimalista e clean, base clara e verde para ações importantes. Evitar aparência de ERP antigo e excesso de elementos decorativos.

## Tokens iniciais (proposta a validar em protótipo)

| Papel | Valor |
| --- | --- |
| Fundo principal | `#FFFFFF` |
| Fundo secundário | `#F8FAF9` |
| Verde suave | `#D1FAE5` |
| Cor de ação principal | `#059669` |
| Texto principal | `#1F2937` |
| Bordas neutras | `#D1D5DB` |

- Fonte: Inter, com fallback sans-serif.
- Ícones: Lucide, preferencialmente outline.
- Raios de borda: 6–8 px como ponto de partida.
- Usar verde para ações e destaques; erro, alerta e indisponibilidade têm estados semânticos próprios.
- Validar contraste de textos, botões, focos e estados antes de considerar o tema final.

## Componentes iniciais

Cabeçalho/navegação, busca, tabela/lista de produtos, controle de quantidade, botão primário, barra fixa de carrinho, resumo financeiro, seleção de condição comercial, mensagens de erro/sucesso, listagem e detalhes de pedidos.

## Responsividade

- Desktop-first: catálogo em linhas compactas com colunas legíveis e ação de compra na mesma linha.
- Mobile: cada produto vira bloco compacto vertical, sem imagem e sem depender de rolagem horizontal para comprar.
- Carrinho desktop: itens e resumo financeiro em áreas separadas; mobile: conteúdo empilhado.
- Manter áreas de toque confortáveis, navegação por teclado, rótulos acessíveis, foco visível e estados não dependentes apenas de cor.
- Barra inferior fixa deve respeitar áreas seguras e não cobrir conteúdo.

## Estados obrigatórios

Carregando, vazio, erro, indisponível, ação em andamento, confirmação e feedback de atualização. Não usar somente notificações temporárias para erros bloqueantes.
