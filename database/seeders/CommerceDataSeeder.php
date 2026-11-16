<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Inventory;
use App\Models\PaymentTerm;
use App\Models\PriceTable;
use App\Models\Product;
use App\Models\Promotion;
use App\Support\SourceData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommerceDataSeeder extends Seeder
{
    public function run(): void
    {
        $priceTables = SourceData::decodeFile(base_path('data/tabelas_preco.json'));

        foreach ($priceTables as $sourceTable) {
            PriceTable::updateOrCreate(
                ['code' => $sourceTable['codigo']],
                ['description' => $sourceTable['descricao']],
            );
        }

        foreach (SourceData::decodeFile(base_path('data/clientes.json')) as $sourceCompany) {
            $companyId = SourceData::integer($sourceCompany['id'], 'clientes.id');
            $company = Company::find($companyId) ?? new Company;
            $isNew = ! $company->exists;
            $company->id = $companyId;

            $company->fill([
                'tax_id' => $sourceCompany['cnpj'],
                'legal_name' => $sourceCompany['razao_social'],
                'trade_name' => $sourceCompany['nome_fantasia'],
                'city' => $sourceCompany['cidade'],
                'state' => $sourceCompany['uf'],
                'price_table_id' => $sourceCompany['tabela_preco'],
                'credit_limit_cents' => SourceData::moneyToCents($sourceCompany['limite_credito'], 'clientes.limite_credito'),
                'is_blocked' => $sourceCompany['bloqueado'],
                'block_reason' => $sourceCompany['motivo_bloqueio'] ?? null,
            ]);

            if ($isNew) {
                $company->credit_used_cents = SourceData::moneyToCents($sourceCompany['limite_utilizado'], 'clientes.limite_utilizado');
            }

            $company->save();
        }

        foreach (SourceData::decodeFile(base_path('data/produtos.json')) as $sourceProduct) {
            $productId = SourceData::integer($sourceProduct['id'], 'produtos.id');
            $product = Product::find($productId) ?? new Product;
            $product->id = $productId;
            $product->fill([
                'sku' => $sourceProduct['sku'],
                'ean' => $sourceProduct['ean'],
                'name' => $sourceProduct['nome'],
                'description' => $sourceProduct['descricao'],
                'brand' => $sourceProduct['marca'],
                'category' => $sourceProduct['categoria'],
                'is_active' => $sourceProduct['ativo'],
            ])->save();
        }

        foreach (SourceData::decodeFile(base_path('data/estoque.json')) as $sourceInventory) {
            $inventory = Inventory::firstOrNew([
                'product_id' => SourceData::integer($sourceInventory['produto_id'], 'estoque.produto_id'),
            ]);

            if (! $inventory->exists) {
                $inventory->quantity = SourceData::integer($sourceInventory['quantidade'], 'estoque.quantidade');
            }

            $inventory->save();
        }

        foreach ($priceTables as $sourceTable) {
            foreach ($sourceTable['itens'] as $sourceItem) {
                $hasWholesalePrice = array_key_exists('preco_atacado', $sourceItem);
                DB::table('price_table_items')->updateOrInsert(
                    [
                        'price_table_id' => $sourceTable['codigo'],
                        'product_id' => SourceData::integer($sourceItem['produto_id'], 'tabelas_preco.itens.produto_id'),
                    ],
                    [
                        'regular_price_cents' => SourceData::moneyToCents($sourceItem['preco'], 'tabelas_preco.itens.preco'),
                        'wholesale_price_cents' => $hasWholesalePrice
                            ? SourceData::moneyToCents($sourceItem['preco_atacado'], 'tabelas_preco.itens.preco_atacado')
                            : null,
                        'wholesale_min_qty' => $hasWholesalePrice
                            ? SourceData::integer($sourceItem['qtd_minima_atacado'], 'tabelas_preco.itens.qtd_minima_atacado')
                            : null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }
        }

        foreach (SourceData::decodeFile(base_path('data/promocoes.json')) as $sourcePromotion) {
            $promotionId = SourceData::integer($sourcePromotion['id'], 'promocoes.id');
            $promotion = Promotion::find($promotionId) ?? new Promotion;
            $promotion->id = $promotionId;
            $promotion->fill([
                'product_id' => SourceData::integer($sourcePromotion['produto_id'], 'promocoes.produto_id'),
                'discount_percent' => SourceData::percentageToDatabaseDecimal($sourcePromotion['percentual_desconto'], 'promocoes.percentual_desconto'),
                'starts_on' => $sourcePromotion['inicio'],
                'ends_on' => $sourcePromotion['fim'],
            ])->save();
        }

        foreach (SourceData::decodeFile(base_path('data/condicoes_pagamento.json')) as $sourcePaymentTerm) {
            PaymentTerm::updateOrCreate(
                ['code' => $sourcePaymentTerm['codigo']],
                [
                    'description' => $sourcePaymentTerm['descricao'],
                    'minimum_amount_cents' => SourceData::moneyToCents($sourcePaymentTerm['valor_minimo'], 'condicoes_pagamento.valor_minimo'),
                    'surcharge_percent' => SourceData::percentageToDatabaseDecimal($sourcePaymentTerm['acrescimo_percentual'], 'condicoes_pagamento.acrescimo_percentual'),
                    'is_credit' => $sourcePaymentTerm['a_prazo'],
                ],
            );
        }

        $this->synchronizePostgresSequence('companies');
        $this->synchronizePostgresSequence('products');
        $this->synchronizePostgresSequence('promotions');
    }

    private function synchronizePostgresSequence(string $table): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), GREATEST(COALESCE(MAX(id), 1), 1), MAX(id) IS NOT NULL) FROM {$table}");
    }
}
