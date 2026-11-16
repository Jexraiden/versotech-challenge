# INC-03 — Catálogo e precificação

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** Incrementos anteriores aprovados

## Objetivo

Home, busca, filtro, paginação e detalhe exibem dados comerciais e menor preço elegível.

## Implementação esperada

Criar páginas Inertia e consultas eficientes; serviço de precificação puro/testável; tabela exclusiva por empresa; atacado por quantidade; promoções por vigência; sem imagens.

## Critérios de aceitação

Busca por nome/SKU/EAN; categorias e paginação; produto inativo/sem preço não comprável; atacado ao atingir mínimo; promoção válida compete com regular/atacado sem acumular; cálculos em centavos.

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
