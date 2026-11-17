# Design system inicial

## Identidade aprovada

Comercial contemporânea: visual minimalista e clean, base clara e verde para ações importantes. Evitar aparência de ERP antigo e excesso de elementos decorativos.

## Tokens baseline aprovados

Baseline aprovada pelo responsável do produto em 16/11/2026 e revisada para que os pares de texto e ação atinjam contraste adequado.

| Papel | Valor |
| --- | --- |
| Fundo principal | `#FFFFFF` |
| Fundo secundário | `#F8FAF9` |
| Verde suave | `#D1FAE5` |
| Cor de ação principal e foco | `#047857` |
| Texto principal | `#1F2937` |
| Bordas e controles neutros | `#6B7280` |

- Fonte: Inter, com fallback sans-serif.
- Ícones: Lucide, preferencialmente outline.
- Raios de borda: 6–8 px.
- Usar verde para ações e destaques; erro, alerta e informação têm estados semânticos próprios.
- Pares de contraste documentados: texto principal `#1F2937` sobre `#FFFFFF` = 14,68:1 e sobre `#F8FAF9` = 14,00:1; texto branco sobre ação `#047857` = 5,48:1; texto principal sobre verde suave `#D1FAE5` = 12,94:1.
- Estados semânticos para texto/ícone: erro `#B91C1C`, alerta `#92400E`, informação `#1D4ED8`; usar fundo claro ou branco e texto/ícone com contraste suficiente. Sucesso usa a cor principal `#047857`.
- A cor de ação `#047857` também é a cor do anel de foco. Exibir foco de teclado com contorno visível de pelo menos 2 px e afastamento do componente; o anel deve permanecer distinguível nos fundos usados.
- Bordas e controles neutros usam `#6B7280` quando necessários para identificar limites; contraste com branco = 4,83:1. Divisórias decorativas podem ser mais suaves, desde que não sejam o único indicador de estado ou limite funcional.
- Não depender somente da cor para comunicar estado; combinar cor com texto, ícone ou outro sinal visual.

## Componentes iniciais

Cabeçalho global fixo com marca, navegação do catálogo, busca e categoria, menu compacto da conta e acesso ao carrinho com badge circular; tabela/lista de produtos, controle de quantidade, botão primário, notificação de sucesso sem overlay, resumo financeiro, seleção de condição comercial, mensagens de erro/sucesso, listagem e detalhes de pedidos.

## Responsividade

- Mobile-first, preservando o desktop como experiência prioritária para o uso B2B: catálogo em linhas compactas com colunas legíveis e ação de compra na mesma linha.
- Mobile: cada produto vira bloco compacto vertical, sem imagem e sem depender de rolagem horizontal para comprar.
- Carrinho desktop: itens e resumo financeiro em áreas separadas; mobile: conteúdo empilhado.
- Manter áreas de toque confortáveis, navegação por teclado, rótulos acessíveis, foco visível e estados não dependentes apenas de cor.
- Cabeçalho fixo superior deve ter altura legível e não cobrir conteúdo; reorganizar controles em smartphone sem rolagem horizontal.

## Implementação frontend

Seguir [frontend-guidelines.md](./frontend-guidelines.md) para composição React, tipagem TypeScript, CSS Modules, CSS Custom Properties, estados interativos e responsividade.

## Estados obrigatórios

Carregando, vazio, erro, indisponível, ação em andamento, confirmação e feedback de atualização. Confirmação de inclusão no carrinho usa notificação discreta sem escurecer o fundo. Não usar somente notificações temporárias para erros bloqueantes.
