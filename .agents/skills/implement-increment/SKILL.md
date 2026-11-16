---
name: implement-increment
description: Analyze, plan, implement, and report a Versotech B2B SDD increment. Use when asked to work on a documented increment such as INC-02; pause at each approval gate and never treat implementation or test results as formal approval.
metadata:
  short-description: Implement a documented SDD increment
---

# Implement an SDD increment — Versotech B2B

Use this workflow for any increment identified by its code (for example, INC-02 or INC-03). The increment document defines scope and acceptance criteria; this skill defines the working process. Do not copy business requirements into this skill.

## 1. Analyze

Confirm the requested increment and locate its document in `docs/implementation/`. Read:

- `AGENTS.md` and `docs/implementation/README.md`;
- the selected `INC-XX` document;
- `docs/project/project-context.md`, `docs/project/requirements.md`, `docs/architecture/system-design.md`, and relevant ADRs;
- when the increment changes a user interface or visual flow, `docs/design/README.md` and the applicable product, user-flow, design-system, frontend-guideline, and screen specifications;
- the relevant implementation, tests, and current Git status/diff to understand the existing state and preserve unrelated work.

Verify paths rather than assuming them. Treat the increment specification and requirements as the source of expected behavior. Report conflicts, missing requirements, dependencies, or discrepancies between documentation and code; do not resolve material scope or business-rule conflicts unilaterally. Consult the roadmap only to confirm that future work stays out of scope.

## 2. Plan — approval gate

Before changing application files, present a concise plan with:

- objective, scope, deliverables, and acceptance criteria;
- affected components/files and relevant technical decisions;
- test strategy, dependencies, risks, and out-of-scope work;
- ambiguities or conflicts needing a decision.

Then stop and wait for the user's explicit approval to implement. A request to analyze or plan is not approval to implement. If a material ambiguity blocks a safe plan, ask for a decision and continue only independent analysis.

## 3. Implement — only after approval

Implement only the approved increment scope, following repository architecture, conventions, security requirements, business invariants, and applicable UI/UX specifications. Prefer the smallest coherent, maintainable change; avoid unnecessary abstractions and dependencies. Do not alter unrelated increments or silently change requirements to fit the code.

Create or update focused tests for changed behavior, but do not claim formal validation at this stage. Preserve existing developer changes. Do not run destructive commands or perform Git operations requiring explicit authorization. Do not commit.

At the end, report changed files, requirement/acceptance-criteria traceability, tests added or prepared, decisions, risks, and unresolved issues. Do not mark the increment approved. Stop and wait for explicit authorization to enter the next phase, starting with adversarial review.

## 4. Review and validate — separate authorized phases

Follow `docs/implementation/README.md`, the selected increment's evidence checklist, and the developer-controlled gates in `AGENTS.md`:

1. After explicit authorization, perform adversarial review as an independent, read-only phase, preferably in a separate session. Use `.agents/skills/adversarial-code-review/SKILL.md` when available. Report evidence-based findings; do not fix them during review.
2. Stop for human triage. Make corrections only when authorized. Material corrections require review again before formal testing.
3. After adversarial review and human review, run formal automated and relevant integrated/manual validations only when authorized. Include regression checks where appropriate. Record exact commands, environment, results, and failures. A tool/environment failure is not by itself a code defect.
4. Map each acceptance criterion to **passed**, **failed**, or **not tested**, with evidence. Report coverage gaps and residual risks. Never claim a check passed if it was not run successfully.

Do not skip, combine, or advance through gates on the basis of elapsed time or implied approval. If authorization is not given, stop at the current phase and report what remains.

## 5. Report and formal approval

For each phase, summarize what changed or was checked, acceptance criteria status, commands/results when applicable, decisions, limitations, risks, and pending work. Distinguish technical completion from formal approval.

After validation, provide a readiness recommendation and wait for the user's explicit approval. Only record the increment as formally approved or complete after that confirmation. A commit remains a separate manual action and must not be performed without explicit authorization. Update the increment's phase/evidence record only when requested or when the user has authorized that documentation change.
