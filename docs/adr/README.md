# Architecture Decision Records (ADRs)

Record decisions with trade-offs that another engineer needs in order to understand the project.

## Decisions

| ADR | Decision |
|-----|----------|
| [ADR-004](./004-web-auth-without-sanctum.md) | Laravel web session authentication without Sanctum |
| [ADR-005](./005-auth-security-scope.md) | Authentication security and MVP scope |
| [ADR-006](./006-product-images.md) | No product images in the MVP; future storage options |

## When to write an ADR

Write an ADR when the decision:

- involves alternatives with lasting consequences;
- affects latency, concurrency, persistence, cache, or contracts;
- would probably need to be explained outside the code.

Do not write an ADR for trivial implementation details.

## Format

```text
# ADR-NNN: Title

## Context

## Alternatives considered

## Decision

## Consequences

## Status
```

## Evolution

Decisions can be replaced by later ADRs. Prefer recording the change (status `superseded` / new ADR) instead of deleting the previous reasoning.
