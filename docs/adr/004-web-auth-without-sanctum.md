# ADR-004 — Autenticação web sem Sanctum

**Status:** Aceita · **Data:** 15/11/2026

## Contexto

O MVP utiliza Laravel + Inertia + React em um monólito web. Não há aplicativo móvel nem integração externa que consuma uma API independente.

## Alternativas consideradas

1. Sessão nativa do Laravel.
2. Laravel Sanctum para autenticação de API.
3. API REST separada com autenticação própria.

## Decisão

Usar autenticação web por sessão Laravel, cookies e CSRF, sem Sanctum e sem API REST pública no MVP.

## Consequências e trade-offs

- Menor custo de integração e configuração.
- Frontend e backend compartilham o fluxo web do Inertia.
- A aplicação monolítica pode escalar horizontalmente sem exigir API REST.
- Para novos consumidores (mobile, parceiros, integrações), considerar API versionada no próprio Laravel.
- Sanctum poderá ser avaliado para consumidores de API, conforme requisitos.
- Escala horizontal exigirá estratégia compartilhada de sessão/cache, independentemente da existência de API.
