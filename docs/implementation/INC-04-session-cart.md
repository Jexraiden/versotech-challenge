# INC-04 — Carrinho em sessão

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** Incrementos anteriores aprovados

## Objetivo

Adicionar, atualizar e remover itens sem reservar estoque ou crédito.

## Implementação esperada

Guardar apenas product_id e quantity em sessão; consolidar itens repetidos; consultar preço e estoque no servidor; exibir totais recalculados; validar quantidade.

## Critérios de aceitação

Carrinho persiste na navegação da sessão; mudança de quantidade recalcula faixa; remoção funciona; itens inválidos são tratados; não há alteração de estoque/crédito.

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
