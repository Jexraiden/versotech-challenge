# Diretrizes de implementação frontend

Estas diretrizes complementam o design system e os critérios de cada incremento. Aplicam-se a componentes React e estilos do frontend do MVP.

## React e TypeScript

- Manter componentes pequenos, com responsabilidade clara e props tipadas explicitamente.
- Compor componentes em vez de concentrar interface e comportamento em grandes condicionais.
- Manter estado o mais próximo possível dos componentes que o utilizam.
- Criar hooks personalizados quando houver lógica reutilizável; não criar hooks apenas para encapsular uma única chamada local.
- Evitar `useEffect`, `useMemo` e `useCallback` desnecessários. Usar memoização orientada por necessidade observada e medição.
- Criar componentes React próprios para elementos simples e recorrentes, como `Button`, `Input`, `Badge` e `Card`.
- Considerar biblioteca de primitivas acessíveis, como Radix UI, apenas para interações mais complexas, por exemplo `Dialog` ou `Select`. Não adicionar dependências sem necessidade demonstrável.

## CSS e organização visual

- Usar CSS Modules como padrão para estilos específicos dos componentes React e manter os estilos próximos ao componente responsável.
- Reservar CSS global para reset, tipografia, variáveis/tokens e fundamentos compartilhados.
- Definir tokens compartilhados com CSS Custom Properties, incluindo cores, bordas, espaçamentos e tipografia; consumir tokens em vez de repetir valores.
- Não utilizar Tailwind, Bootstrap ou bibliotecas CSS-in-JS.
- Não usar estilos inline para apresentação estática. Preferir HTML semântico e CSS nativo a soluções baseadas em JavaScript.
- Evitar `!important`, seletores excessivamente específicos e dimensões rígidas sem necessidade.
- Definir estados de hover, focus, disabled, loading e erro quando aplicáveis, mantendo foco visível e estados compreensíveis sem depender apenas de cor.

## Responsividade

- Adotar abordagem mobile-first; preservar experiência desktop cuidadosa, prioritária para o uso B2B.
- Usar layouts flexíveis e pontos de quebra conforme o conteúdo; evitar rolagem horizontal para ações e informações essenciais.
- Garantir alvos de toque confortáveis e respeitar áreas seguras em elementos fixos.
- Cabeçalho global fixo deve reorganizar seus controles em telas estreitas, permanecer legível e reservar espaço no fluxo para não cobrir conteúdo.
