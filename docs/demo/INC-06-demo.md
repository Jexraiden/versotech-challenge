# Roteiro de demonstração — INC-06

Apresentação sugerida de 35 a 40 minutos. O roteiro usa somente dados importados e contas criadas pelos seeders; não pressupõe pagamento, frete ou rastreamento externo.

## Preparação

Para valores operacionais iniciais reproduzíveis sem tocar em outros dados locais, use um projeto Compose isolado na primeira execução. A importação repetida atualiza dados de origem, mas preserva estoque e crédito utilizado; ela não restaura um banco já usado em demonstrações.

```bash
export COMPOSE_PROJECT_NAME=versotech-demo
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
npm run dev
```

Na primeira execução desse nome de projeto, Compose cria volume PostgreSQL separado. Mantenha `COMPOSE_PROJECT_NAME=versotech-demo` no terminal durante toda a preparação e confira os serviços com `docker compose ps`. Abra a URL local configurada em `APP_PORT`. O novo projeto usa as mesmas portas de host definidas em `.env`; se outro projeto já estiver ativo, escolha portas livres em `APP_PORT`, `FORWARD_DB_PORT` e `VITE_PORT` e mantenha `APP_URL` coerente. Se este projeto demo já tiver sido usado, confira estoque e crédito antes de apresentar; não use comandos de limpeza em dados que devam ser preservados.

Contas (senha local: `password`):

- Bom Preço: `bompreco@example.test` — tabela PADRAO, empresa liberada.
- Atacadão Central: `atacadao@example.test` — tabela ATACADISTA, crédito inicial disponível de R$ 1.500,00.
- Empório São Jorge: `emporio@example.test` — empresa bloqueada.

## Agenda sugerida

### 1. Contexto e arquitetura — 4 min

- Apresente o fluxo navegador → Laravel/Inertia → PostgreSQL.
- Explique que o backend calcula preço, crédito e estoque; o carrinho na sessão só mantém produto e quantidade.
- Destaque os limites do MVP: sem imagens, gateway, emissão de boleto ou frete.

### 2. Login e contexto da empresa — 4 min

- Entre como Bom Preço.
- Mostre o nome da empresa no catálogo e explique que a sessão determina a tabela comercial.
- Mostre erros de login sem revelar se o e-mail ou a senha estão incorretos; use teclado para percorrer o formulário.

### 3. Catálogo, busca e preço por empresa — 7 min

- Pesquise `SKU-1001` ou “Guaraná”. O Refrigerante Guaraná São João 350ml tem preço PADRAO de R$ 3,07 na unidade.
- Altere a quantidade para 50: o preço por unidade passa para R$ 2,76 conforme a faixa de atacado.
- Adicione ao carrinho e confira o feedback e a barra de resumo; navegue ao detalhe e retorne ao catálogo.
- Saia e entre como Atacadão Central. O mesmo SKU mostra R$ 2,82 na tabela ATACADISTA, sem reutilizar preço da tabela PADRAO.
- Selecione o SKU `SKU-1152` com Bom Preço: a promoção vigente de 20% sobre R$ 16,35 resulta em R$ 13,08 por unidade.

### 4. Carrinho e revalidação — 5 min

- Edite quantidade, confirme o recálculo, remova um item e observe a mensagem de carrinho vazio quando aplicável.
- Explique que carrinho não reserva estoque nem crédito; o checkout revalida ambos no servidor.
- Percorra foco visível, rótulos e layout em largura móvel (devtools ou viewport do navegador).

### 5. Regras comerciais e bloqueio — 6 min

- Com Atacadão Central, use `SKU-1001` e quantidade 533: a R$ 2,82 por unidade, o subtotal supera o crédito inicial disponível de R$ 1.500,00. O boleto a prazo não deve ficar elegível; PIX não consome crédito.
- Reduza para 500 unidades: subtotal R$ 1.410,00 atende ao mínimo do BOL28 e cabe no crédito inicial. Mostre também que BOL2856 considera mínimo e acréscimo de 2,5% no total final. Não é necessário confirmar este pedido.
- Entre como Empório São Jorge, navegue pelo catálogo e tente seguir ao checkout. A interface informa o bloqueio e impede a confirmação; a validação decisiva também ocorre no backend.

### 6. Confirmação e persistência — 7 min

- Volte a Bom Preço, monte um pedido pequeno, selecione PIX, informe o endereço estruturado e confirme.
- Apresente o identificador, status RECEBIDO, endereço, itens e totais da resposta confirmada.
- Explique que criação do pedido, snapshots dos itens, baixa de estoque e eventual consumo de crédito ocorrem na mesma transação. PIX não altera crédito.
- Evite submeter um pedido a prazo se quiser preservar o crédito inicial durante a apresentação.

### 7. Encerramento técnico — 5 min

- Aponte migrations, seeders, `ProductPricing`, `SessionCart` e `OrderCheckout`.
- Resuma os testes registrados nos incrementos anteriores e diga explicitamente que a suíte SQLite não verifica contenção concorrente real no PostgreSQL.
- Liste limitações restantes e perguntas para evolução: integração PostgreSQL concorrente, histórico de pedidos e itens do roadmap.

## Checklist antes de apresentar

- [ ] Aplicação e PostgreSQL iniciam sem erro; Vite está servindo assets.
- [ ] Banco contém os dados importados; saldo de crédito e estoque foram conferidos.
- [ ] Os três e-mails e a senha demo funcionam no ambiente local.
- [ ] Catálogo mostra preços diferentes entre PADRAO e ATACADISTA.
- [ ] `SKU-1001` em PADRAO permite demonstrar preço regular e faixa de atacado.
- [ ] Promoção vigente de `SKU-1152` está no período esperado para a data da apresentação.
- [ ] Carrinho, bloqueio, elegibilidade de crédito, checkout e confirmação estão acessíveis.
- [ ] Tamanho móvel, teclado, foco e mensagens de erro foram percorridos.
- [ ] A limitação de concorrência PostgreSQL está declarada; nenhum resultado de teste não executado será apresentado como aprovado.

## Dependência dos dados operacionais

`migrate --seed` numa base local vazia cria os valores iniciais dos dados demo. Seeders executados novamente preservam estoque e crédito utilizado para não apagar estado operacional. Pedidos confirmados reduzem estoque e boletos consomem crédito; prepare uma base demo inicial antes da apresentação se essas cenas já tiverem sido executadas.
