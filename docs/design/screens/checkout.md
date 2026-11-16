# Checkout — página única

## Objetivo

Registrar pedido mediante seleção de condição comercial de pagamento, **sem gateway online**.

## Seções

1. Revisão dos itens e totais.
2. Condições de pagamento disponíveis para a empresa.
3. Confirmação final.

As condições comerciais são PIX, Boleto 28 dias e Boleto 28/56 dias, conforme elegibilidade e dados do backend. PIX não representa integração de pagamento; não solicitar dados financeiros nem processar cobranças nesta tela.

## Comportamento

- Exibir restrições comerciais conforme resposta do backend.
- Na confirmação, validar novamente preços, estoque, promoções, bloqueios e crédito.
- Estado de envio impede múltiplos cliques acidentais; idempotência real depende do backend.
- Falhas preservam dados e explicam o próximo passo.
- Sucesso apresenta identificador e resumo do pedido; nunca antecipar sucesso.

## Exclusões

Não solicitar cartão, Pix, conta bancária ou dados de gateway.
