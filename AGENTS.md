# AGENTS.md — Versotech B2B Commerce

Instruções para Codex e demais agentes de IA que atuem neste repositório.

## 1. Leia antes de implementar

Fontes de verdade, nesta ordem:

1. [`docs/project/project-context.md`](docs/project/project-context.md) — contexto, prioridades e escopo.
2. [`docs/project/requirements.md`](docs/project/requirements.md) — regras e critérios de aceitação.
3. [`docs/architecture/system-design.md`](docs/architecture/system-design.md) — arquitetura proposta e fluxos críticos.
4. [`docs/adr/README.md`](docs/adr/README.md) e ADRs pertinentes — decisões e trade-offs.
5. [`docs/design/README.md`](docs/design/README.md), especificações em `docs/design/` — decisões de produto, UI, UX e implementação frontend para interfaces do MVP.

Para decisões visuais, consultar também as telas relevantes em `docs/design/screens/`, os fluxos em `user-flows.md`, os tokens/comportamentos responsivos em `design-system.md` e as convenções em `frontend-guidelines.md`. `roadmap.md` descreve melhorias futuras e não amplia o MVP. As especificações de design complementam, mas não substituem, requisitos funcionais e regras de negócio. Em conflito relevante, registrar a divergência e solicitar decisão; não alterar unilateralmente escopo, regras aprovadas ou comportamento do backend.

Se código e documentação divergirem, **relate a divergência**. Não altere silenciosamente regras de negócio para fazer o código se encaixar.

## 2. Stack e limites

- PHP + Laravel; React + TypeScript + Vite + Inertia.js.
- PostgreSQL; Laravel Sail para desenvolvimento.
- Monólito web; rotas Laravel/Inertia; autenticação por sessão.
- **Não introduzir Sanctum, API REST pública, Redis, filas, serviços externos, imagens de produtos ou painel administrativo** sem solicitação explícita.
- Os JSONs de `data/` são a fonte inicial de dados. Criar importação reproduzível via seeders/comandos.
- Não introduzir abstrações genéricas, microsserviços ou bibliotecas adicionais sem necessidade demonstrável.

## 3. Invariantes de negócio

- A empresa sempre deriva do usuário autenticado; nunca confiar em `company_id` do navegador.
- Preço vem exclusivamente da tabela da empresa.
- Preço final unitário é o menor preço elegível entre regular, atacado por quantidade e promoção sobre o regular; **sem empilhar descontos**.
- Produto inativo, sem preço aplicável ou sem estoque não pode ser comprado.
- Carrinho na sessão não reserva estoque nem consome crédito.
- Empresa bloqueada pode navegar, mas não confirmar pedido.
- `PIX` não consome crédito; boletos consomem o total final, inclusive acréscimos.
- Pedido, itens, estoque e crédito devem ser confirmados atomicamente.
- Concorrência não pode gerar estoque negativo nem crédito acima do limite.
- Reenvio com mesma chave idempotente não pode criar segundo pedido.
- Backend recalcula preços e totais; não confiar em valores enviados pelo frontend.
- **Todos os identificadores técnicos em inglês.** Tabelas e colunas em `snake_case`; classes em `PascalCase`; variáveis/métodos PHP e TypeScript em `camelCase`; componentes React em `PascalCase`. Preservar nomes de negócio em português somente como dados ou textos da interface.
- **Todo dinheiro em inteiros de centavos**, armazenado como `BIGINT` no PostgreSQL. Campos monetários devem ter sufixo `_cents` (`unit_price_cents`, `credit_limit_cents`, `total_cents`).
- Percentuais não são centavos: armazenar como `NUMERIC(7,4)` e calcular com aritmética decimal exata ou inteiros escalados, **nunca `float`**.
- Arredondamento half-up: preço unitário promocional antes da multiplicação pela quantidade; acréscimo calculado sobre subtotal e arredondado uma vez.
- Importar valores dos JSONs para centavos sem operações intermediárias em ponto flutuante; mapear chaves de origem em português para colunas em inglês.

## 4. Segurança

- Usar autenticação convencional do Laravel, hash de senha, CSRF e limitação de login.
- Regenerar sessão no login; invalidar no logout.
- Autorizar acesso a recursos pelo contexto da empresa.
- Validar todas as entradas no backend.
- Não registrar segredos, senhas, tokens ou dados sensíveis em logs.
- Não implementar SSO, MFA ou magic link no MVP; são evoluções documentadas em ADR.

## 5. Fluxo de trabalho SDD leve

Para cada tarefa:

1. Identificar requisito(s) e critério(s) de aceitação.
2. Para funcionalidades visuais, consultar as especificações de UI/UX e frontend pertinentes (`docs/design/README.md`, fluxo, design system, frontend guidelines e telas) e critérios do incremento; apresentar plano curto, arquivos que serão modificados e ambiguidades/conflitos identificados.
3. Implementar o menor incremento funcional.
4. Escrever/ajustar testes para o comportamento afetado.
5. Preparar testes pertinentes; executar a validação formal após revisão adversarial e revisão humana, conforme os gates do incremento.
6. Verificar autorização, precisão monetária, concorrência e efeitos colaterais quando aplicável.
7. Atualizar `docs/architecture/system-design.md` se a arquitetura implementada divergir do plano.
8. Resumir alterações, testes executados, riscos e pendências.

**Não afirmar que testes passaram se não foram executados.**

## 6. Git e operações destrutivas

- Não executar `git add`, `git commit`, `git push` ou criar PR sem autorização explícita.
- Não executar `migrate:fresh`, `db:wipe`, exclusão de arquivos ou comandos destrutivos sem autorização.
- Não modificar arquivos fora do escopo da tarefa.
- Preservar mudanças existentes do desenvolvedor; não sobrescrever trabalho não relacionado.
- Preferir commits pequenos e descritivos, feitos após revisão humana.

## 7. Prioridade de implementação

1. Ambiente Sail, PostgreSQL, migrations, seeders e usuários demo.
2. Login, catálogo, busca, detalhe e precificação testada.
3. Carrinho em sessão e checkout.
4. Confirmação transacional, estoque, crédito e idempotência.
5. Testes críticos, revisão de UX, README e preparação da demonstração.

## 8. Critérios de conclusão

Uma tarefa só está concluída quando:

- Cumpre os critérios de aceitação relevantes.
- Não quebra isolamento entre empresas.
- Mantém regras comerciais no backend.
- Possui testes adequados ao risco, ou limitações explicitamente documentadas.
- Pode ser explicada de forma simples durante a apresentação técnica.

## 9. Processo obrigatório por incremento

Consulte `docs/implementation/README.md` e o arquivo `INC-XX` correspondente. Execute as fases em ordem: implementação → revisão adversarial (preferencialmente sessão nova, somente leitura) → correções e revisão manual → validação formal de testes → aprovação humana → commit manual.

Ao implementar um incremento, use a skill `.agents/skills/implement-increment/SKILL.md` para análise, planejamento e execução dos gates. Apresente o plano e aguarde aprovação explícita antes de implementar.

- Não avançar de fase sem autorização do desenvolvedor.
- Implementação pode criar testes, mas não declarar incremento aprovado.
- Revisão adversarial deve buscar defeitos concretos, com evidências, sem corrigir automaticamente.
- Testes formais devem ser executados após a revisão manual; informar comandos, resultados e limitações.
- Não criar commits sem aprovação explícita.
- Registrar status de fase e pendências no documento do incremento, quando solicitado.
- Prompts reutilizáveis em `docs/workflows/`.

## 10. Falhas de ferramentas e execução

- Falha de terminal, Docker, WSL2, Sail, permissão ou dependência **não é automaticamente falha do código**.
- Registre o comando tentado, ambiente, saída/erro e impacto na verificação.
- Classifique conclusões como **defeito confirmado**, **risco fundamentado** ou **não verificado**.
- Não afirme que build, testes ou aplicação funcionam sem execução bem-sucedida.
- Em revisão adversarial, não altere arquivos; correções exigem aprovação e nova verificação quando relevantes.
