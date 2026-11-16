# ADR-006 — Ausência de imagens no MVP e evolução de armazenamento

**Status:** Aceita · **Data:** 15/11/2026 · **Prioridade:** Pós-MVP

## Contexto

Os arquivos JSON fornecidos pela Versotech não incluem imagens nem campos com caminhos ou URLs de imagens. Implementar imagens exigiria obter arquivos, associá-los aos produtos e definir armazenamento/distribuição.

## Alternativas consideradas

1. Criar placeholders locais para todos os produtos.
2. Buscar e associar imagens reais durante o desafio.
3. Não exibir imagens no MVP e documentar evolução futura.

## Decisão

**O MVP não terá imagens de produtos.** Catálogo e detalhe serão projetados para apresentar nome, descrição, marca, categoria, SKU, preço e estoque, sem reservar espaços vazios para imagens.

Não implementar upload, armazenamento ou processamento de imagens no MVP.

## Consequências e trade-offs

**Benefícios:** menor tempo de implementação, sem dependências externas, foco nas regras comerciais e execução local simples.

**Limitações:** identificação visual menos imediata e experiência menos próxima de uma loja em produção.

## Evolução futura

Usar armazenamento externo de objetos, como **Amazon S3**, opcionalmente com CDN. O banco guardará referências aos arquivos, não os binários.

### Alternativa A — Caminho estável

A referência do produto aponta sempre ao mesmo caminho, e o objeto é substituído em atualizações.

- Simplicidade de associação.
- Exige cuidado com invalidação de cache e rollback.

### Alternativa B — Caminhos versionados/imutáveis

Cada atualização gera novo objeto e nova referência; a versão anterior pode ser preservada.

- Melhor previsibilidade de cache, histórico e reversão.
- Exige gerenciamento de versões e limpeza de objetos antigos.

O versionamento nativo do bucket S3 pode complementar as estratégias, mas não substitui necessariamente URLs imutáveis para CDN.

## Critério para revisitar

Quando houver catálogo com imagens disponíveis, exigência explícita de UX visual ou necessidade real de gerenciamento de mídia.
