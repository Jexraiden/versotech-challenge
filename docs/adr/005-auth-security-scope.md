# ADR-005 — Autenticação simplificada e segurança

**Status:** Aceita · **Data:** 15/11/2026

## Contexto

Autenticação envolve riscos reais: **credential stuffing** (uso automatizado de credenciais vazadas) e **account takeover** via recuperação de conta insegura, além de phishing e sequestro de sessão. Construir uma solução própria completa excede o escopo do desafio.

## Alternativas consideradas

1. Autenticação convencional do Laravel.
2. Provedor de identidade externo via OpenID Connect (ex.: Microsoft Entra ID, Google).
3. Autenticação sem senha por magic link ou código de uso único.
4. MFA como camada adicional.

## Decisão

Usar recursos convencionais de autenticação do Laravel com três usuários de demonstração. Não implementar sistema de autenticação do zero.

## Controles mínimos

- Hash seguro de senhas.
- Proteção CSRF.
- Limitação de tentativas de login.
- Regeneração de sessão após login e invalidação no logout.
- Cookies seguros conforme ambiente (HTTPS em produção).
- Autorização por empresa em toda operação comercial.
- Não usar credenciais de demonstração em produção.

## Consequências e evolução

- MVP mais simples, mas não equivale a solução corporativa de identidade.
- Em produção, avaliar SSO/OIDC e MFA, especialmente para acesso corporativo.
- Magic links e códigos de uso único podem reduzir dependência de senhas, mas exigem expiração, uso único e limitação de tentativas.
- SMS possui riscos como SIM swapping e não deve ser presumido mais seguro.
- Provedor externo reduz responsabilidade sobre credenciais, mas **não substitui autorização interna** nem segurança das sessões.
