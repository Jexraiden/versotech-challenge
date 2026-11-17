# INC-02 — Autenticação e isolamento

**Status:** Aprovado  
**Fase atual:** Concluída; commit manual realizado
**Dependências:** INC-01 aprovado (usuários demo, empresas e base da aplicação disponíveis); concluir autenticação antes de expor catálogo e recursos restritos em INC-03.

**Especificações de UI/UX e frontend:** [login](../design/screens/login.md), [fluxos de usuário](../design/user-flows.md) (autenticação e entrada no catálogo), [design system](../design/design-system.md), [diretrizes frontend](../design/frontend-guidelines.md) e [produto](../design/product.md).

## Objetivo

Login/logout com sessão Laravel e acesso vinculado à empresa autenticada.

## Implementação esperada

Implementar login por e-mail e senha, throttle, regeneração de sessão, logout, middleware auth; company context derivado do usuário; bloqueio comercial não impede navegação; limpar carrinho na mudança de identidade.

## Critérios de aceitação

Cada usuário entra com suas credenciais; convidado é redirecionado; acesso indevido a recursos de outra empresa é negado; sessões são invalidadas corretamente.

### Critérios de aceite de UI/UX

- [x] Formulário de login claro, rotulado e utilizável por teclado, com foco visível e erros acessíveis que não revelem se e-mail ou senha estão incorretos.
- [x] Envio mostra estado em andamento e impede envios acidentais repetidos; falha mantém orientação para correção e sucesso encaminha ao catálogo.
- [x] Tela funciona em desktop e smartphone e respeita os estados/componentes semânticos do design system.

## Fora do escopo

Não adicionar Sanctum, MFA, OIDC, recuperação de senha ou registro público.

## Foco da revisão adversarial

Testar IDOR, troca de usuário, session fixation, CSRF, brute force e redirecionamentos.

## Evidências para aprovação

- [x] Implementação concluída e explicada
- [x] Revisão adversarial aprovada pelo desenvolvedor; detalhes dos achados não registrados nesta conversa
- [x] Achados críticos/altos tratados ou aceitos pelo desenvolvedor na revisão aprovada
- [x] Revisão manual aprovada pelo desenvolvedor
- [x] Testes formais concluídos e aprovados pelo desenvolvedor; resultado final detalhado não registrado nesta conversa
- [x] Critérios de aceitação validados pelo desenvolvedor
- [x] Aprovação explícita do desenvolvedor recebida em 16/11/2026
- [x] Commit manual após aprovação (`87dd078` — `feat(auth): implement login and company access flow`)

## Registro da execução

**Arquivos alterados:** autenticação, middleware Inertia, configuração de middleware, telas e estilos de login/catálogo, configuração, rotas, testes de autenticação, README e este registro.  
**Achados da revisão:** revisão adversarial aprovada pelo desenvolvedor; relatório não fornecido para transcrição.  
**Testes e resultados:** testes formais concluídos e aprovados pelo desenvolvedor; comando/saída final não fornecidos. Uma execução anterior local falhou por ausência do driver SQLite e o Sail estava indisponível naquele ambiente.  
**Pendências / decisões:** sem pendências de commit.
**Aprovação:** INC-02 aprovado explicitamente pelo desenvolvedor em 16/11/2026.
