# INC-01 — Fundação e importação dos dados

**Status:** Concluído; commit manual realizado
**Fase atual:** Concluída
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

- [x] Implementação concluída e explicada
- [x] Revisão adversarial concluída; problemas identificados corrigidos, conforme informado pelo desenvolvedor
- [x] Achados identificados corrigidos, conforme informado pelo desenvolvedor
- [x] Revisão manual aprovada pelo desenvolvedor
- [x] Testes formais executados com comandos e resultados registrados
- [x] Critérios de aceitação validados
- [x] Aprovação explícita do desenvolvedor (16/11/2026)
- [x] Commit manual após aprovação (`871881c` — `feat: establish application foundation and seed initial data`)

## Registro da execução

**Arquivos alterados:** implementação do INC-01 já presente no workspace antes da validação formal; nenhum arquivo de aplicação foi alterado durante a validação.  
**Achados da revisão:** revisão adversarial concluída e problemas identificados corrigidos, conforme informado pelo desenvolvedor.  
**Testes e resultados:** `docker compose exec -T laravel.test php artisan migrate --force` — sem migrations pendentes; duas execuções de `docker compose exec -T laravel.test php artisan db:seed --force` — sucesso; suíte no Sail — 15 testes e 50 asserções passaram; `npm run typecheck` — sucesso; `npm run build` — sucesso; requisição local — HTTP 200 com componente Inertia `Welcome`. Preservação de estoque e crédito confirmada com valores-sentinela dentro de transação revertida.  
**Pendências / decisões:** a execução de `php artisan test` no host falhou por ausência do driver SQLite e uma asserção Inertia; a suíte passou no container Sail. Commit manual realizado posteriormente.
**Aprovação:** aprovada explicitamente pelo desenvolvedor em 16/11/2026.
