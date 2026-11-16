# INC-03 — Catálogo e precificação

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** INC-02 aprovado para autenticação e contexto da empresa; depende dos dados/migrations de INC-01.

**Especificações de UI/UX e frontend:** [produto](../design/product.md), [catálogo](../design/screens/catalog.md), [detalhe do produto](../design/screens/product-detail.md), [fluxos de usuário](../design/user-flows.md) (catálogo, busca e detalhe informativo), [design system](../design/design-system.md) e [diretrizes frontend](../design/frontend-guidelines.md). `roadmap.md` delimita imagens, grade e compra a partir do detalhe como fora do MVP.

## Objetivo

Home, busca, filtro, paginação e detalhe exibem dados comerciais e menor preço elegível.

## Implementação esperada

Criar páginas Inertia e consultas eficientes; serviço de precificação puro/testável; tabela exclusiva por empresa; atacado por quantidade; promoções por vigência; sem imagens.

## Critérios de aceitação

Busca por nome/SKU/EAN; categorias e paginação; produto inativo/sem preço não comprável; atacado ao atingir mínimo; promoção válida compete com regular/atacado sem acumular; cálculos em centavos.

### Critérios de aceite de UI/UX

- [ ] Exibe catálogo em lista compacta textual, sem imagens, com código, nome, preço aplicável, disponibilidade, quantidade e ação de adicionar; não depende de abrir detalhe para comprar.
- [ ] Busca por nome parcial, SKU ou EAN, filtro por categoria e paginação estão disponíveis na UI; preservam contexto e apresentam estados de carregamento, vazio, sem resultados, indisponível e erro.
- [ ] Página de detalhe apresenta as informações disponíveis do produto, sem ação de adicionar ao carrinho; a compra permanece na lista do catálogo.
- [ ] Em desktop, compra permanece na linha; em smartphone, os dados e controles formam blocos sem rolagem horizontal.
- [ ] Informação comercial reflete a empresa autenticada; preço e disponibilidade não são apresentados como definitivos além dos dados retornados pelo backend.

## Fora do escopo

Não criar imagens, integração externa ou sistema de descontos genérico.

## Foco da revisão adversarial

Verificar N+1, vazamento de preços, promoções futuras/expiradas, limites de data, precisão de percentuais, quantidades e itens sem descrição.

## Evidências para aprovação

- [ ] Implementação concluída e explicada
- [ ] Revisão adversarial em sessão separada, com achados classificados por severidade
- [ ] Achados críticos/altos corrigidos ou explicitamente aceitos pelo desenvolvedor
- [ ] Revisão manual aprovada
- [ ] Testes formais executados com comandos e resultados registrados
- [ ] Critérios de aceitação validados
- [ ] Aprovação explícita do desenvolvedor
- [ ] Commit manual após aprovação

## Registro da execução

**Arquivos alterados:** _preencher_  
**Achados da revisão:** _preencher_  
**Testes e resultados:** _preencher_  
**Pendências / decisões:** _preencher_  
**Aprovação:** _pendente_
