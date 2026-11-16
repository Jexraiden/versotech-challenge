# INC-06 — Finalização e demonstração

**Status:** Não iniciado  
**Fase atual:** Planejamento  
**Dependências:** INC-02 a INC-05 aprovados; consolida a experiência implementada e corrige problemas de integração sem ampliar requisitos funcionais.

**Especificações de UI/UX e frontend:** [produto](../design/product.md), [design system](../design/design-system.md), [diretrizes frontend](../design/frontend-guidelines.md), [fluxos de usuário](../design/user-flows.md) e telas [login](../design/screens/login.md), [catálogo](../design/screens/catalog.md), [carrinho](../design/screens/cart.md) e [checkout](../design/screens/checkout.md). [Roadmap](../design/roadmap.md): melhorias futuras fora dos critérios obrigatórios.

## Objetivo

MVP estável, executável localmente e apresentável em aproximadamente 40 minutos.

## Implementação esperada

Revisar UX/responsividade/acessibilidade básica; mensagens de erro; README raiz com setup, seed, credenciais demo, testes e trade-offs; alinhar system-design ao implementado; roteiro demo e checklist de execução.

## Critérios de aceitação

Setup reproduzível; principais jornadas passam; dados demo confiáveis; documentação reflete o código; demonstração percorre preços distintos, atacado, bloqueio, crédito, estoque e pedido.

### Critérios de aceite de UI/UX

- [ ] Percorrer a jornada obrigatória login → catálogo/busca/detalhe → carrinho → checkout → confirmação sem becos sem saída nem sucesso antecipado; histórico de pedidos é opcional se houver tempo.
- [ ] Revisar responsividade desktop/mobile, foco/teclado, rótulos acessíveis, contraste e estados de carregamento, vazio, erro, indisponibilidade e sucesso nas telas do MVP.
- [ ] Conferir que barra fixa não encobre conteúdo, que os textos refletem regras reais e que nenhuma tela introduz imagens, gateway ou dados logísticos fictícios.
- [ ] Revisar se os componentes mantêm responsabilidades claras, props tipadas, composição adequada, estilos em CSS Modules e tokens compartilhados; justificar hooks, memoização ou primitivas externas adicionadas.
- [ ] Registrar problemas residuais de UX como pendências, sem introduzir itens do roadmap ou marcar o incremento como aprovado.

## Fora do escopo

Não expandir escopo com funcionalidades não essenciais.

## Foco da revisão adversarial

Verificar configuração limpa, variáveis de ambiente, segredos, erros visíveis, comandos de setup e regressões.

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
