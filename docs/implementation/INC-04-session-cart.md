# INC-04 — Carrinho em sessão

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** INC-03 aprovado para catálogo e precificação; INC-02 aprovado para isolamento da sessão por identidade.

**Especificações de UI/UX e frontend:** [carrinho](../design/screens/cart.md), [catálogo](../design/screens/catalog.md) (adição e barra fixa), [fluxos de usuário](../design/user-flows.md), [design system](../design/design-system.md) e [diretrizes frontend](../design/frontend-guidelines.md).

## Objetivo

Adicionar, atualizar e remover itens sem reservar estoque ou crédito.

## Implementação esperada

Guardar apenas product_id e quantity em sessão; consolidar itens repetidos; consultar preço e estoque no servidor; exibir totais recalculados; validar quantidade.

## Critérios de aceitação

Carrinho persiste na navegação da sessão; mudança de quantidade recalcula faixa; remoção funciona; itens inválidos são tratados; não há alteração de estoque/crédito.

### Critérios de aceite de UI/UX

- [ ] A ação funcional de adicionar pela lista (critério transferido do INC-03) mantém o usuário no catálogo, consolida produto repetido e fornece feedback acessível; a barra inferior aparece somente com itens e mostra unidades, subtotal e ação para abrir o carrinho.
- [ ] Barra fixa não cobre conteúdo/controles em smartphone e respeita áreas seguras; reservar espaço inferior apropriado.
- [ ] Página dedicada permite editar/remover, mostra resumo financeiro recalculado pelo backend e oferece retorno ao catálogo preservando o carrinho.
- [ ] Estado vazio orienta retorno ao catálogo; erros/alterações comerciais são compreensíveis e não deixam totais inconsistentes.
- [ ] Layout desktop separa itens e resumo; mobile empilha conteúdo com ação principal visível.

## Fora do escopo

Não persistir carrinho no banco nem implementar reserva de estoque.

## Foco da revisão adversarial

Verificar manipulação de quantidades, produto desativado, estoque reduzido, troca de usuário, CSRF e ausência de preços confiados à sessão.

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
