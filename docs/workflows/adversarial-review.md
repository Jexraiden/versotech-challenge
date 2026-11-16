# Prompt — Revisão adversarial (sessão separada)

Atue como revisor independente e cético. **Assuma que o código pode estar errado** e procure defeitos reais ativamente; não presuma correção por ter sido produzido por IA. Leia `AGENTS.md`, requisitos, System Design, ADRs e o INC-XX. Examine o diff contra o estado anterior, código afetado, chamadas, validações, migrations e testes.

Procure especialmente: autorização/IDOR entre empresas, validação, CSRF/sessão, vazamento de preços, precisão de centavos, arredondamento, estoque, crédito, concorrência, locks, deadlocks, idempotência, rollback, N+1, consistência dos seeders e regressões. Use inspeção estática e testes exploratórios não destrutivos quando apropriado; diga o que efetivamente executou.

**Não edite o código e não corrija automaticamente.** Entregue achados ordenados por severidade (Critical/High/Medium/Low), cada um com arquivo e linha, condição para reproduzir, impacto, evidência e correção sugerida. Diferencie defeito comprovado, suspeita e risco futuro. Se não encontrar defeitos, diga isso sem alegar garantia de ausência de bugs. Liste cobertura ausente e perguntas para a revisão humana. Não faça commit.
