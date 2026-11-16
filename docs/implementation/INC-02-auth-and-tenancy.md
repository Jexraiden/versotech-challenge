# INC-02 — Autenticação e isolamento

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** Incrementos anteriores aprovados

## Objetivo

Login/logout com sessão Laravel e acesso vinculado à empresa autenticada.

## Implementação esperada

Implementar login por e-mail e senha, throttle, regeneração de sessão, logout, middleware auth; company context derivado do usuário; bloqueio comercial não impede navegação; limpar carrinho na mudança de identidade.

## Critérios de aceitação

Cada usuário entra com suas credenciais; convidado é redirecionado; acesso indevido a recursos de outra empresa é negado; sessões são invalidadas corretamente.

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
