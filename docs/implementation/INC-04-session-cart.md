# INC-04 — Carrinho em sessão

**Status:** Concluído — aprovado pelo desenvolvedor; commit manual pendente
**Fase atual:** Concluída
**Dependências:** INC-03 aprovado para catálogo e precificação; INC-02 aprovado para isolamento da sessão por identidade.

**Especificações de UI/UX e frontend:** [carrinho](../design/screens/cart.md), [catálogo](../design/screens/catalog.md) (adição e barra fixa), [fluxos de usuário](../design/user-flows.md), [design system](../design/design-system.md) e [diretrizes frontend](../design/frontend-guidelines.md).

## Objetivo

Adicionar, atualizar e remover itens sem reservar estoque ou crédito.

## Implementação esperada

Guardar apenas product_id e quantity em sessão; consolidar itens repetidos; consultar preço e estoque no servidor; exibir totais recalculados; validar quantidade.

## Critérios de aceitação

Carrinho persiste na navegação da sessão; mudança de quantidade recalcula faixa; remoção funciona; itens inválidos são tratados; não há alteração de estoque/crédito.

### Critérios de aceite de UI/UX

- [x] A ação funcional de adicionar pela lista (critério transferido do INC-03) mantém o usuário no catálogo, consolida produto repetido e fornece feedback acessível; a barra inferior aparece somente com itens e mostra unidades, subtotal e ação para abrir o carrinho.
- [x] Barra fixa não cobre conteúdo/controles em smartphone e respeita áreas seguras; reservar espaço inferior apropriado.
- [x] Página dedicada permite editar/remover, mostra resumo financeiro recalculado pelo backend e oferece retorno ao catálogo preservando o carrinho.
- [x] Estado vazio orienta retorno ao catálogo; erros/alterações comerciais são compreensíveis e não deixam totais inconsistentes.
- [x] Layout desktop separa itens e resumo; mobile empilha conteúdo com ação principal visível.

## Fora do escopo

Não persistir carrinho no banco nem implementar reserva de estoque.

## Foco da revisão adversarial

Verificar manipulação de quantidades, produto desativado, estoque reduzido, troca de usuário, CSRF e ausência de preços confiados à sessão.

## Evidências para aprovação

- [ ] Implementação concluída e explicada
- [x] Revisão adversarial dispensada pelo desenvolvedor para este ciclo
- [ ] Achados críticos/altos corrigidos ou explicitamente aceitos pelo desenvolvedor
- [x] Revisão manual aprovada pelo desenvolvedor
- [x] Testes formais executados com comandos e resultados registrados
- [x] Critérios de aceitação revisados pelo desenvolvedor e suíte automatizada aprovada
- [x] Aprovação explícita do desenvolvedor
- [ ] Commit manual após aprovação

## Registro da execução

**Arquivos alterados:** controller e serviço do carrinho, rotas web, middleware Inertia, modelo Product, páginas e estilos de catálogo/carrinho e `tests/Feature/SessionCartTest.php`; há também alterações não commitadas em registros de INC-02 e INC-03.

**Achados da revisão:** nenhum defeito funcional concreto identificado na inspeção inicial. Achado Low: status e registro deste documento estavam desatualizados; corrigidos. Cenários de alterações comerciais após adicionar ao carrinho permanecem como lacuna de cobertura identificada. As alterações visuais posteriores ainda não passaram por nova revisão adversarial.

**Testes e resultados:** validação formal após revisão manual: `docker compose run --rm --no-deps laravel.test php artisan test` — 48 testes passaram, 474 assertions, duração 33,27s; `npm run typecheck` — passou; `npm run build` — passou. A suíte PHP usa SQLite em memória conforme `phpunit.xml`; PostgreSQL não foi validado nesta execução. O desenvolvedor informou que revisou manualmente os ajustes visuais. Por autorização explícita do desenvolvedor em 16/11/2026, a nova revisão adversarial foi dispensada neste ciclo por os últimos ajustes serem exclusivamente de frontend.

**Pendências / decisões:** revisão adversarial dispensada por autorização explícita do desenvolvedor em 16/11/2026. Testes formais concluídos; integração com PostgreSQL não verificada. Aprovação explícita do INC-04 recebida em 16/11/2026. Commit manual pendente.
**Aprovação:** aprovada pelo desenvolvedor em 16/11/2026; commit manual pendente.
