# INC-07 — Navegação global e feedback do carrinho

**Status:** Planejado — escopo atualizado, aguardando aprovação para implementação
**Fase atual:** Plano e critérios definidos
**Dependências:** INC-06 implementado

**Especificações de UI/UX:** [produto](../design/product.md), [fluxos](../design/user-flows.md), [design system](../design/design-system.md), [diretrizes frontend](../design/frontend-guidelines.md), [catálogo](../design/screens/catalog.md), [detalhe do produto](../design/screens/product-detail.md), [carrinho](../design/screens/cart.md) e [checkout](../design/screens/checkout.md).

## Objetivo

Melhorar a navegação entre catálogo, busca, conta e carrinho com uma barra superior global, fixa durante a rolagem e adaptada a desktop e smartphone. Substituir a barra inferior do catálogo por uma confirmação discreta ao adicionar um produto.

## Escopo esperado

- Criar cabeçalho global em todas as telas autenticadas do projeto, com altura e distribuição responsivas; a tela de login fica fora do cabeçalho autenticado.
- Da esquerda para a direita, incluir: marca textual `Ecommerce B2B` em cor de destaque; link `Catálogo`; seletor de categoria sem título separado; campo de busca com exemplos no placeholder; botão compacto `Buscar` na cor de ação; controle `Olá, {nome}`; botão/link `Ver Carrinho` com badge circular.
- Busca e categoria permanecem disponíveis no cabeçalho autenticado. Ao mudar de tela, os campos reiniciam para texto vazio e categoria padrão. Ao clicar `Buscar`, abrir o catálogo com os filtros submetidos aplicados; reiniciar os campos do cabeçalho após a navegação, sem limpar os filtros aplicados aos resultados.
- O controle do usuário abre um menu pequeno abaixo dele, sem escurecer o restante da página, contendo `Ver pedidos` e `Sair da conta`, nessa ordem. `Ver pedidos` abre a listagem paginada dos pedidos criados pelo usuário autenticado, sem filtros adicionais, limitada ao usuário e à empresa autenticados; os detalhes mantêm a autorização por empresa já existente.
- O carrinho mostra a quantidade total de unidades informada pelo estado atual do carrinho (`item_count`) no badge circular e abre a página dedicada do carrinho.
- Remover a barra fixa inferior de resumo do carrinho no catálogo.
- Remover links redundantes `Voltar ao catálogo` e `Continuar comprando` das telas autenticadas; o link `Catálogo` no cabeçalho passa a ser o retorno às compras.
- Criar listagem paginada, sem filtros, dos pedidos do usuário autenticado, acessada pelo menu da conta.
- Após inclusão bem-sucedida, apresentar notificação de sucesso semelhante à referência fornecida: indicador de sucesso, título `Sucesso`, texto `Item adicionado ao carrinho` e opção para dispensar. Sem overlay ou escurecimento; manter feedback de erro existente.
- Atualizar as especificações de design para refletir o cabeçalho global, o menu de conta, o badge, a notificação e a remoção da barra inferior.

## Critérios de aceitação

- [ ] Cabeçalho aparece em todas as telas autenticadas (catálogo, detalhe, carrinho, checkout, confirmação e pedidos) e permanece no topo durante a rolagem sem cobrir o conteúdo. Login não apresenta controles autenticados.
- [ ] Em desktop, os controles seguem a ordem especificada e mantêm legibilidade; em smartphone, refluem ou se reorganizam sem rolagem horizontal e sem perder acesso às funções.
- [ ] `Catálogo` abre o catálogo. Busca por texto e categoria combinados navega para o catálogo com filtros aplicados.
- [ ] Ao mudar de tela, busca e categoria do cabeçalho reiniciam; enviar a busca abre o catálogo com os filtros submetidos aplicados.
- [ ] O menu `Olá, {nome}` abre e fecha por interação acessível, não escurece o fundo e contém `Ver pedidos` antes de `Sair da conta`.
- [ ] `Ver pedidos` lista todos os pedidos criados pelo usuário autenticado, sem filtros adicionais e com paginação; mantém as regras existentes de autorização por empresa e usuário.
- [ ] `Ver Carrinho` leva ao carrinho dedicado; o badge reflete a quantidade atual de unidades e atualiza após alterações no carrinho.
- [ ] Não há barra inferior fixa de resumo do carrinho no catálogo.
- [ ] Não há links redundantes de retorno/continuação ao catálogo nas telas autenticadas; o cabeçalho fornece esse acesso.
- [ ] Inclusão bem-sucedida mostra feedback visual e acessível de sucesso; falhas continuam identificáveis sem alegar sucesso.
- [ ] Busca, navegação, logout e carrinho usam rotas/valores já existentes e respeitam autorização e dados do usuário autenticado.
- [ ] Documentação de design não descreve mais a barra inferior como comportamento vigente.

## Plano de implementação

Inspecionar o compartilhamento atual de props Inertia e os componentes de página; introduzir o cabeçalho comum no ponto adequado; fornecer nome autenticado e resumo do carrinho às páginas necessárias usando dados já disponíveis ou compartilhamento global mínimo; integrar filtros e logout às rotas existentes; trocar o feedback do catálogo por notificação; remover barra inferior e links redundantes; criar listagem paginada restrita aos pedidos do usuário autenticado; ajustar documentação e testes focados.

## Arquivos prováveis

- `resources/js/Components/PageChrome.module.css` e um componente compartilhado novo em `resources/js/Components/`.
- `resources/js/Pages/Catalog/Index.tsx` e `resources/js/Pages/Catalog/Catalog.module.css`.
- Páginas autenticadas que precisem do cabeçalho global: `Catalog/Show.tsx`, `Cart/Index.tsx`, `Checkout/Index.tsx`, `Orders/Show.tsx` e respectivos módulos de estilo, conforme inspeção.
- Nova página de listagem de pedidos e controller/rota Laravel; consulta paginada limitada ao usuário autenticado e compatível com o escopo de empresa existente.
- Compartilhamento de props Inertia no middleware Laravel pertinente, somente se os dados de usuário/carrinho ainda não forem globais.
- Testes de feature relacionados às props compartilhadas e ao fluxo de busca/carrinho; teste frontend conforme estrutura disponível.
- `docs/design/product.md`, `docs/design/user-flows.md`, `docs/design/design-system.md`, `docs/design/screens/catalog.md` e telas autenticadas pertinentes.

## Testes e riscos

- Preparar testes focados para props/navegação, reset dos campos entre telas, busca combinada, autorização e paginação da listagem de pedidos, quantidade do badge e inclusão com feedback; incluir verificação responsiva e de teclado no roteiro manual.
- Risco técnico: props atuais de carrinho podem estar disponíveis apenas em algumas páginas; evitar consultas redundantes ou alteração de regras de sessão ao compartilhar o resumo.
- Risco de layout: barra com muitos controles exige reflow cuidadoso em telas estreitas; definir comportamento mobile sem esconder busca, conta ou acesso ao carrinho.
- Fora do escopo: busca em tempo real, alteração de regras comerciais de catálogo/pedidos, outros itens no menu, carrinho lateral, overlay, reserva de estoque, filtros de pedidos ou qualquer evolução do roadmap.

## Aprovação

Este documento define o escopo proposto. Implementação, revisão adversarial, validação formal e aprovação permanecem sujeitas aos gates do repositório.
