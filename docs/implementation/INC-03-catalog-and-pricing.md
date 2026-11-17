# INC-03 — Catálogo e precificação

**Status:** Aprovado
**Fase atual:** Concluída; commit manual realizado
**Dependências:** INC-02 aprovado para autenticação e contexto da empresa; depende dos dados/migrations de INC-01.

**Especificações de UI/UX e frontend:** [produto](../design/product.md), [catálogo](../design/screens/catalog.md), [detalhe do produto](../design/screens/product-detail.md), [fluxos de usuário](../design/user-flows.md) (catálogo, busca e detalhe informativo), [design system](../design/design-system.md) e [diretrizes frontend](../design/frontend-guidelines.md). `roadmap.md` delimita imagens, grade e compra a partir do detalhe como fora do MVP.

## Objetivo

O catálogo reúne a entrada da aplicação, busca, filtro e paginação; a página de detalhe informa os dados comerciais. A rota inicial redireciona para o catálogo, sem vitrine separada.

## Implementação esperada

Criar páginas Inertia e consultas eficientes; serviço de precificação puro/testável; tabela exclusiva por empresa; atacado por quantidade; promoções por vigência; sem imagens.

## Critérios de aceitação

Busca por nome/SKU/EAN; categorias e paginação; produto inativo/sem preço não comprável; atacado ao atingir mínimo; promoção válida compete com regular/atacado sem acumular; cálculos em centavos.

### Critérios de aceite de UI/UX

- [x] Exibe catálogo em lista compacta textual, sem imagens, com código, nome, preço aplicável, disponibilidade e quantidade. A ação funcional de adicionar e a compra pela lista pertencem ao INC-04.
- [x] Busca por nome parcial, SKU ou EAN, filtro por categoria e paginação estão disponíveis na UI; preservam contexto e apresentam estados de carregamento, vazio, sem resultados, indisponível e erro.
- [x] Página de detalhe apresenta as informações disponíveis do produto sem ação de adicionar ao carrinho. A ação funcional de compra pela lista será entregue no INC-04.
- [x] Em desktop, informações e controles permanecem na linha; em smartphone, formam blocos sem rolagem horizontal.
- [x] Informação comercial reflete a empresa autenticada; preço e disponibilidade são apresentados como dados retornados pelo backend.

## Fora do escopo

Não criar imagens, integração externa ou sistema de descontos genérico.

## Foco da revisão adversarial

Verificar N+1, vazamento de preços, promoções futuras/expiradas, limites de data, precisão de percentuais, quantidades e itens sem descrição.

## Evidências para aprovação

- [x] Implementação concluída e explicada
- [x] Revisão adversarial realizada; revisão nesta conversa, sem sessão separada
- [x] Achado High no cálculo promocional corrigido; revisão subsequente não encontrou novos defeitos
- [x] Revisão manual aprovada pelo desenvolvedor, incluindo os ajustes visuais solicitados
- [x] Testes formais executados com comando e resultado registrados
- [x] Critérios de aceitação do INC-03 validados; ação funcional de adicionar transferida para INC-04
- [x] Aprovação explícita do desenvolvedor recebida em 16/11/2026
- [x] Commit manual após aprovação (`4045fe7` — `feat: implement catalog and pricing for INC-03`)

## Registro da execução

**Arquivos alterados:** controller e consultas de catálogo, serviço de precificação, modelo de produto/tabela de preço, rotas, páginas e estilos do catálogo, testes de autenticação, catálogo e precificação; este registro.
**Achados da revisão:** revisão adversarial encontrou erro de escala percentual que podia gerar preço negativo. Corrigido com cálculo na escala correta e validação de preço/desconto; revisão subsequente não encontrou novos defeitos. Revisões ocorreram nesta conversa, sem sessão separada.
**Testes e resultados:** `docker compose run --rm --no-deps laravel.test php artisan test` — 40 testes passaram, 357 assertions, duração 25,39s. A primeira execução revelou uma asserção incorreta no novo teste de busca; o teste foi ajustado e a execução final passou. `git diff --check` sem apontamentos.
**Pendências / decisões:** ação funcional de adicionar ao carrinho transferida para INC-04 e refletida no respectivo critério; o botão no catálogo permanecia desabilitado nesta fase e foi habilitado no INC-04. Ajustes feitos após a validação deste incremento não foram cobertos pela sua suíte PHP nem por seu build frontend; as validações posteriores constam dos incrementos seguintes. Sem pendências de commit.
**Aprovação:** INC-03 aprovado explicitamente pelo desenvolvedor em 16/11/2026.
