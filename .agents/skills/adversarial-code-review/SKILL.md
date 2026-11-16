---
name: adversarial-code-review
description: Revisão adversarial, independente e somente leitura de um incremento do Versotech B2B, com achados verificáveis, severidade e avaliação de cobertura. Use após implementação e antes da revisão manual.
---

# Adversarial code review — Versotech B2B

## Entrada e escopo

Receba o identificador do incremento (`INC-01` a `INC-06`) e, se disponível, a referência Git anterior à implementação. Se o identificador não estiver claro, pergunte antes de revisar. **Não presuma que a árvore de trabalho está limpa.** Inspecione `git status`, `git diff` e o histórico pertinente sem modificar o repositório. Caso não exista baseline confiável, declare a limitação e revise os arquivos relacionados ao incremento.

Leia, nesta ordem: `AGENTS.md`, `docs/implementation/INC-XX-*.md`, `docs/workflows/adversarial-review.md`, `docs/project/requirements.md`, `docs/architecture/system-design.md` e ADRs relevantes. Verifique se os arquivos realmente existem; se faltar algum, relate.

## Postura

Aja como revisor independente e cético. Trate o código como potencialmente incorreto e procure falhas concretas; não invente achados para preencher o relatório. Considere caminhos felizes, entradas inválidas, estados inconsistentes, falhas parciais, reexecução e concorrência. Rastreie o fluxo completo entre origem de dados, regras de negócio, persistência e interface, conforme o incremento.

## Restrições de segurança e ferramentas

- **Somente leitura:** não edite arquivos, não aplique patches, não instale dependências, não faça commit, não rode migrations/seeders, não inicie serviços nem execute comandos que alterem o banco ou o projeto.
- São permitidos comandos de inspeção sem efeitos colaterais, como `git diff`, `git status`, leitura de arquivos e buscas no código. Não presuma que `artisan test`, build ou scripts são inofensivos; não os execute nesta fase.
- Se o terminal falhar, registre comando, erro e impacto. Falha de ferramenta não comprova defeito no código.
- Não confunda ausência de evidência com aprovação. Nunca alegue execução de testes que não ocorreu.

## Checklist orientado por risco

1. **Dados e migrations:** tipos, nulabilidade, FKs, unicidade, checks, índices, compatibilidade PostgreSQL, IDs de origem e sequências.
2. **Importação e seeders:** JSON real, valores opcionais, referências, transações, falhas parciais, reexecução, preservação de estoque e crédito operacional.
3. **Dinheiro:** inteiros em centavos, `_cents`, nenhuma conversão monetária via float, precisão de origem, arredondamento explícito apenas em cálculos.
4. **Segurança e tenancy:** derivar empresa da sessão autenticada, impedir IDOR, proteger rotas e dados, validar entradas, não expor segredos.
5. **Preço e checkout (quando implementados):** tabela da empresa, atacado, promoção não cumulativa, termos de pagamento, estoque, crédito, locks, transações, idempotência, rollback.
6. **Laravel/Inertia:** convenções idiomáticas, sessão/CSRF, relacionamentos, queries N+1, props compartilhadas, React/TypeScript e dependências.
7. **Testes existentes:** qualidade das asserções, cenários faltantes, falsos positivos e testes que podem passar com comportamento incorreto.

Aplique apenas os itens relevantes ao incremento. Não sinalize funcionalidades de incrementos futuros como defeitos do atual, salvo quando violarem requisito já exigido.

## Relatório obrigatório

Apresente primeiro os achados, em ordem de severidade: **Critical, High, Medium, Low**. Para cada um:

- ID e título
- Severidade e classificação: **defeito confirmado**, **risco fundamentado** ou **não verificado**
- Arquivo e linha(s) específicas
- Evidência observável no código e cenário de reprodução
- Impacto e correção recomendada

Depois inclua:

1. **Lacunas de testes** relevantes ao incremento.
2. **Verificações realizadas** (comandos e resultados efetivos).
3. **Verificações não realizadas** e motivos.
4. **Conclusão para revisão manual:** pronto / pronto com ressalvas / bloqueado, explicando os critérios.

Se não encontrar achados, diga que não identificou defeitos na inspeção realizada, sem garantir ausência de bugs.

**Não corrija nada. Não avance de fase. Aguarde triagem e autorização humana.**
