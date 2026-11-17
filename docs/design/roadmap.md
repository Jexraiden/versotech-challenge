# Melhorias futuras — fora do MVP

1. Imagens por produto e modo de visualização em grade, com alternância lista/grade.
2. Adicionar ao carrinho diretamente a partir da página de detalhes do produto.
3. Rastreamento logístico detalhado de separação, expedição, transporte e entrega, condicionado a dados ou integração com um serviço externo.
4. Recomendações personalizadas para cada empresa, com base nos dados de pedidos cujo uso ela autorizou: lembretes de reposição, produtos relacionados e alternativas mais baratas para ajudar a loja a planejar compras e melhorar sua margem.
5. Criar uma área de perfil para o usuário autenticado. Avaliar a inclusão de uma seção financeira para consultar o limite, o crédito utilizado e o crédito disponível da empresa sem precisar iniciar o checkout, respeitando as permissões de acesso.
6. Permitir consultar pedidos por identificador, data e produtos contidos no pedido (nome, SKU ou EAN), com ordenação por data crescente ou decrescente. Definir se a busca permanece limitada aos pedidos do próprio usuário ou passa a incluir outros pedidos da empresa antes de implementar.
7. Investigar e corrigir o deslocamento intermitente para a esquerda do campo de quantidade ao atualizar um item no carrinho, relatado durante a verificação manual. Reproduzir o caso em desktop e smartphone antes de definir a correção.
8. Avaliar ícones para ações frequentes: lupa para buscar, carrinho para abrir o carrinho e símbolo de adição ou carrinho com adição para incluir um produto. Preservar nomes acessíveis, indicação de foco e compreensão da ação sem depender apenas do ícone.
9. Avaliar carrinho persistido no banco e eventual reserva temporária de estoque. Antes de decidir, definir validade da reserva, liberação por abandono ou expiração, comportamento com vários usuários e prevenção de bloqueio indevido de compras. A confirmação do pedido continuaria revalidando estoque, preço e crédito no servidor. A ideia de reduzir apenas a disponibilidade exibida ao próprio usuário precisa ser analisada separadamente, pois não constitui reserva compartilhada de estoque.
10. Oferecer ordenação no catálogo por nome, SKU, EAN, preço aplicável à empresa e quantidade em estoque, em ordem crescente ou decrescente quando fizer sentido. Manter filtros e paginação coerentes com a ordenação selecionada.
11. Avaliar ocultar produtos indisponíveis da lista principal do catálogo para reduzir ruído. Definir como isso afeta buscas, links para detalhes e itens que se tornaram indisponíveis após serem adicionados ao carrinho.

## Recomendações personalizadas — ideia de produto

Com autorização da empresa, a IA poderia analisar o histórico de pedidos para estimar quando a loja costuma repor cada produto e enviar um lembrete ao comprador. Também poderia mostrar produtos relacionados ou alternativas de menor custo. O comprador decide se quer aproveitar a sugestão; ela não adiciona itens nem cria um pedido automaticamente.

O objetivo é ajudar a empresa cliente a planejar melhor suas compras e, quando houver uma substituição adequada, melhorar sua margem. Para isso, recomendações precisam respeitar os dados e os preços daquela empresa e ser avaliadas quanto à relevância e ao benefício real para a loja. Essa é uma ideia a validar, não uma promessa de resultado nem um requisito do MVP.

Busca por nome/SKU/EAN no catálogo, filtro por categoria, paginação e página informativa de detalhes do produto fazem parte do MVP, não deste roadmap. Os itens acima propõem busca e ordenação adicionais.

Essas ideias são evolução de produto, não requisitos aprovados do MVP. Prioridade, requisitos, integrações e critérios de autorização/privacidade devem ser definidos antes da implementação. Nenhum item desta lista deve ser implementado sem aprovação explícita.
