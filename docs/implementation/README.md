# Plano de implementação incremental

Cada incremento passa por **implementação → revisão adversarial → revisão humana → testes formais → aprovação → commit**. Testes podem ser escritos durante a implementação, mas a validação formal ocorre após a revisão manual. O desenvolvedor controla cada passagem de fase.

| Ordem | Incremento | Dependência |
|---|---|---|
| 01 | [INC-01 — Fundação e importação dos dados](./INC-01-foundation-and-data.md) | — |
| 02 | [INC-02 — Autenticação e isolamento](./INC-02-auth-and-tenancy.md) | Anterior aprovado |
| 03 | [INC-03 — Catálogo e precificação](./INC-03-catalog-and-pricing.md) | Anterior aprovado |
| 04 | [INC-04 — Carrinho em sessão](./INC-04-session-cart.md) | Anterior aprovado |
| 05 | [INC-05 — Checkout e pedidos transacionais](./INC-05-checkout-and-orders.md) | Anterior aprovado |
| 06 | [INC-06 — Finalização e demonstração](./INC-06-polish-and-demo.md) | Anterior aprovado |

## Gates

1. **Implementação:** Codex implementa apenas o incremento selecionado, escreve testes pertinentes e reporta mudanças; não faz commit.
2. **Revisão adversarial:** nova sessão, idealmente sem edição; assume que pode haver defeitos, examina diffs e caminhos críticos, produz achados verificáveis.
3. **Revisão humana:** desenvolvedor avalia achados, solicita correções e verifica decisões. Repetir revisão se houver alterações relevantes.
4. **Testes formais:** Codex executa testes automatizados e cenários manuais assistidos, reporta comandos e saídas; desenvolvedor valida resultados.
5. **Aprovação:** só após aval explícito o incremento é concluído e o commit é realizado manualmente.

## Convenções

Ver `AGENTS.md`, requisitos e System Design. Identificadores técnicos em inglês; valores monetários em inteiros de centavos (`*_cents`); Laravel web + Inertia; sem Sanctum.

## Prompts

- [Implementar](../workflows/implement.md)
- [Revisar adversarialmente](../workflows/adversarial-review.md)
- [Validar testes](../workflows/validate.md)
