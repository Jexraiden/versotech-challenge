# INC-01 — Fundação e importação dos dados

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** Nenhuma

## Objetivo

Ambiente Laravel + Sail + PostgreSQL + Inertia + React + TypeScript + Vite inicializa localmente; migrations e importação reproduzível dos seis JSONs.

## Implementação esperada

Criar estrutura da aplicação e conexão PostgreSQL; migrations com identificadores ingleses; BIGINT *_cents para dinheiro; importar empresas, produtos, estoque, tabelas, preços, promoções e pagamentos; seeders idempotentes ou estratégia clara de reset em desenvolvimento; três usuários demo vinculados às empresas.

## Critérios de aceitação

Servidor inicia; página React via Inertia; seeders importam contagens e vínculos corretos; preços e limites são convertidos exatamente para centavos; reexecução não duplica dados.

## Fora do escopo

Não construir catálogo, checkout ou API; não alterar JSONs originais.

## Foco da revisão adversarial

Verificar campos opcionais, datas, arredondamento de importação, unicidade de SKU/CNPJ, FK e migrations reversíveis.

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
