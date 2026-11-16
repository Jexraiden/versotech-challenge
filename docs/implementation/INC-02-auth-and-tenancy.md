# INC-02 — Autenticação e isolamento

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** INC-01 aprovado (usuários demo, empresas e base da aplicação disponíveis); concluir autenticação antes de expor catálogo e recursos restritos em INC-03.

**Especificações de UI/UX e frontend:** [login](../design/screens/login.md), [fluxos de usuário](../design/user-flows.md) (autenticação e entrada no catálogo), [design system](../design/design-system.md), [diretrizes frontend](../design/frontend-guidelines.md) e [produto](../design/product.md).

## Objetivo

Login/logout com sessão Laravel e acesso vinculado à empresa autenticada.

## Implementação esperada

Implementar login por e-mail e senha, throttle, regeneração de sessão, logout, middleware auth; company context derivado do usuário; bloqueio comercial não impede navegação; limpar carrinho na mudança de identidade.

## Critérios de aceitação

Cada usuário entra com suas credenciais; convidado é redirecionado; acesso indevido a recursos de outra empresa é negado; sessões são invalidadas corretamente.

### Critérios de aceite de UI/UX

- [ ] Formulário de login claro, rotulado e utilizável por teclado, com foco visível e erros acessíveis que não revelem se e-mail ou senha estão incorretos.
- [ ] Envio mostra estado em andamento e impede envios acidentais repetidos; falha mantém orientação para correção e sucesso encaminha ao catálogo.
- [ ] Tela funciona em desktop e smartphone e respeita os estados/componentes semânticos do design system.

## Fora do escopo

Não adicionar Sanctum, MFA, OIDC, recuperação de senha ou registro público.

## Foco da revisão adversarial

Testar IDOR, troca de usuário, session fixation, CSRF, brute force e redirecionamentos.

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
