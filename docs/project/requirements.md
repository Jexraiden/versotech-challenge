# Requisitos e critérios de aceitação — B2B Commerce

**Versão:** 1.1 · **Status:** escopo do MVP implementado até o INC-07 · **Atualizado em:** 17/11/2026

As caixas abaixo definem critérios de referência e não registram sua validação. Aprovações, resultados de testes e limitações estão nos documentos dos incrementos em `docs/implementation/`.

## 1. Premissas dos dados

Os arquivos `data/` contêm empresas, produtos, estoque, tabelas de preços, promoções e condições de pagamento. Não incluem usuários, senhas ou imagens. Os usuários de demonstração serão criados via seeders.

Dados de referência:
- Bom Preço: tabela `PADRAO`, limite R$ 15.000, utilizado R$ 3.200, disponível R$ 11.800.
- Atacadão Central: `ATACADISTA`, limite R$ 80.000, utilizado R$ 78.500, disponível R$ 1.500.
- Empório São Jorge: `PADRAO`, bloqueado.
- Condições: `PIX` (sem mínimo/acréscimo); `BOL28` (mínimo R$ 300); `BOL2856` (mínimo R$ 1.000, acréscimo 2,5%).

## 2. Autenticação e empresas

### AUTH-01 — Login
- [ ] Três usuários de demonstração, cada um vinculado a uma empresa.
- [ ] Login por e-mail e senha, logout e proteção de rotas.
- [ ] Credenciais demonstrativas documentadas no README, sem reutilização em produção.
- [ ] Login com limitação de tentativas, sessão regenerada após autenticação.

### AUTH-02 — Isolamento
- [ ] Empresa derivada do usuário autenticado, nunca de ID informado pelo cliente.
- [ ] Preços, pedidos e carrinho pertencem ao contexto correto.
- [ ] Um usuário não acessa pedidos de outra empresa.
- [ ] Carrinho em sessão é descartado/isolado ao trocar de usuário.

### AUTH-03 — Empresa bloqueada
- [ ] Pode consultar catálogo, mas não confirmar pedido.
- [ ] Bloqueio é validado no backend.
- [ ] Pedido rejeitado não altera crédito nem estoque.

## 3. Catálogo

### CAT-02 — Busca
- [ ] Busca por nome parcial, SKU e EAN.
- [ ] Filtro por categoria e paginação.
- [ ] Não apresenta produtos inativos como compráveis.
- [ ] Não expõe preços de outra tabela comercial.

### CAT-03 — Detalhe
- [ ] Exibe nome, descrição quando existente, marca, categoria, SKU, preço e disponibilidade.
- [ ] Detalhe informativo; a quantidade positiva é selecionada na lista do catálogo, onde o produto é adicionado ao carrinho.
- [ ] Produto inativo, sem preço aplicável ou sem estoque não pode ser comprado.
- [ ] Indica preço de atacado quando aplicável.
- [ ] **Não exibe imagens de produtos no MVP**; layout textual intencional.

## 4. Precificação

### PRICE-01 — Tabela da empresa
- [ ] Usa exclusivamente a tabela de preço atribuída à empresa.
- [ ] Produto ausente da tabela não pode ser comprado; sem fallback para `PADRAO`.

### PRICE-02 — Faixa de atacado
- [ ] Quantidade abaixo do mínimo usa preço regular.
- [ ] Quantidade igual ou acima do mínimo torna o preço de atacado elegível para todas as unidades.
- [ ] Alterar quantidade recalcula o preço da linha.

### PRICE-03 — Promoções
- [ ] Vigência inclui as datas inicial e final no fuso configurado.
- [ ] Promoção futura ou expirada não é aplicada.
- [ ] Preço unitário = menor valor elegível entre preço regular, preço de atacado (se aplicável) e preço regular com promoção (se vigente).
- [ ] Promoção não é acumulada sobre preço de atacado.
- [ ] Valores monetários não podem ser negativos.

### PRICE-04 — Precisão
- [ ] Todos os valores monetários são inteiros em centavos, persistidos em `BIGINT` com campos `*_cents`; percentuais são decimais exatos (`NUMERIC(7,4)`), nunca `float`. Arredondamento financeiro half-up explícito.
- [ ] Arredondamento monetário consistente em duas casas decimais.
- [ ] Subtotal da linha = quantidade × preço unitário; subtotal do pedido = soma das linhas.
- [ ] Backend recalcula preços e totais, ignorando valores enviados pelo navegador.

## 5. Carrinho

### CART-01 — Operações
- [ ] Adicionar, atualizar quantidade, remover e consolidar produto repetido.
- [ ] Quantidades devem ser inteiras positivas.
- [ ] Carrinho persiste na sessão durante navegação.
- [ ] Recalcula preço/totais após alterações.
- [ ] Isolamento do carrinho por usuário autenticado.

### CART-02 — Sem reserva
- [ ] Carrinho não altera estoque nem crédito.
- [ ] Estoque e preços são revalidados no checkout.
- [ ] Alterações comerciais podem impedir a compra; apresentar erro compreensível.

## 6. Checkout e crédito

### CHECK-01 — Condições de pagamento
- [ ] `PIX` elegível para qualquer subtotal positivo.
- [ ] `BOL28` requer subtotal >= R$ 300.
- [ ] `BOL2856` requer subtotal >= R$ 1.000 e aplica 2,5% ao subtotal.
- [ ] Total final = subtotal + acréscimo arredondado.
- [ ] Elegibilidade revalidada no servidor.

### CHECK-02 — Crédito
- [ ] Crédito disponível = limite − utilizado.
- [ ] Boleto consome o total final na confirmação do pedido.
- [ ] Pedido a prazo acima do crédito disponível é rejeitado.
- [ ] PIX não consome limite.
- [ ] Consumo de crédito protegido por transação e bloqueio de concorrência.

### CHECK-03 — Resumo
- [ ] Exibe empresa, endereço de entrega obrigatório informado para o pedido, itens, preços, pagamento e total.
- [ ] Endereço de entrega é salvo como snapshot estruturado: `zipcode`, `state`, `city`, `district`, `address` e `address_extra`; os cinco primeiros são obrigatórios e `address_extra` é opcional.
- [ ] `state` aceita apenas siglas válidas de UFs brasileiras; cidade permanece texto livre.
- [ ] Valida bloqueio, estoque, preço, condição e crédito antes de confirmar.
- [ ] Falha não cria pedido nem altera estoque/crédito.

## 7. Pedido e estoque

### ORDER-01 — Persistência
- [ ] Pedido vinculado à empresa autenticada.
- [ ] Persiste condição, subtotal, acréscimo, total, estado inicial `RECEBIDO`.
- [ ] Itens preservam snapshot de SKU/nome, quantidade, preço unitário e subtotal.
- [ ] Confirmação exibe identificador do pedido.
- [ ] Carrinho é limpo somente após confirmação bem-sucedida.

### ORDER-02 — Baixa imediata
- [ ] Confirmação diminui estoque imediatamente.
- [ ] Estoque insuficiente rejeita pedido; estoque nunca negativo.
- [ ] Concorrência não permite venda além do estoque disponível.

### ORDER-03 — Atomicidade
- [ ] Pedido, itens, baixa de estoque e consumo de crédito ocorrem na mesma transação.
- [ ] Qualquer falha provoca rollback integral.
- [ ] Locks em ordem determinística para reduzir risco de deadlock.

### ORDER-04 — Idempotência
- [ ] Tentativa de checkout possui chave única.
- [ ] Reenvio da mesma chave não cria outro pedido nem consome recursos novamente.
- [ ] Banco possui restrição única para a chave.
- [ ] Reenvio concluído retorna o pedido já criado.

## 8. Requisitos não funcionais

- [ ] Proteção CSRF, cookies de sessão seguros, validação server-side e autorização por empresa.
- [ ] Migrations, seeders e ambiente reproduzível com Laravel Sail.
- [ ] Testes automatizados para preço, isolamento, bloqueio, crédito, concorrência, rollback e idempotência.
- [ ] README com instalação, execução, testes, credenciais demo e trade-offs.
- [ ] Sem dependência de serviços externos para executar a aplicação.

## 9. Cenários prioritários de demonstração

1. Bom Preço e Atacadão Central veem preços diferentes do mesmo produto.
2. Quantidade mínima habilita preço de atacado.
3. Promoção vigente concorre com atacado; prevalece menor preço.
4. Produto sem preço na tabela não é comercializado.
5. Empório bloqueado não consegue confirmar pedido.
6. Atacadão não consegue comprar a prazo acima de R$ 1.500 disponíveis.
7. PIX não consome crédito.
8. Duas compras simultâneas não excedem estoque/crédito.
9. Reenvio do checkout não duplica pedido.
10. Falha no checkout reverte todas as alterações.

## 10. Fora do escopo

Home/vitrine separada do catálogo, compra a partir do detalhe, imagens, cadastro público, painel administrativo, gateway real, frete, ERP, cancelamento/reembolso, Redis, API REST pública, MFA e SSO. A rota inicial redireciona para o catálogo.

## 11. Pontos para validar durante a implementação

- Se houver múltiplas promoções simultâneas para um produto, escolher o menor preço elegível.
- Definir explicitamente o fuso horário da aplicação (sugestão: `America/Sao_Paulo`).
- Ao usar sessão para carrinho, garantir que troca de identidade não compartilhe itens.
- Validar se o subtotal mínimo para pagamento é calculado antes do acréscimo (decisão atual: sim).
