<?php

namespace Tests\Unit;

use App\Support\SourceData;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SourceDataTest extends TestCase
{
    public function test_it_preserves_json_number_tokens_as_decimal_strings(): void
    {
        $data = SourceData::decode('{"amount":15000.0,"id":1001,"text":"price 3.07"}');

        $this->assertSame('15000.0', $data['amount']);
        $this->assertSame('1001', $data['id']);
        $this->assertSame('price 3.07', $data['text']);
    }

    #[DataProvider('exactMoneyValues')]
    public function test_it_converts_exact_decimal_money_to_cents(string $amount, int $expectedCents): void
    {
        $this->assertSame($expectedCents, SourceData::moneyToCents($amount, 'amount'));
    }

    public static function exactMoneyValues(): array
    {
        return [
            'two decimal places' => ['3.07', 307],
            'one decimal place' => ['10.7', 1070],
            'whole value with decimal zero' => ['15000.0', 1500000],
            'exponent with exact cents' => ['1.23e2', 12300],
            'extra trailing zeros' => ['1.2300', 123],
        ];
    }

    public function test_it_rejects_values_that_cannot_be_represented_in_cents(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SourceData::moneyToCents('1.001', 'amount');
    }

    public function test_it_converts_percentages_without_float_arithmetic(): void
    {
        $this->assertSame('2.5000', SourceData::percentageToDatabaseDecimal('2.5', 'percentage'));
    }

    public function test_all_monetary_source_values_are_exactly_representable_in_cents(): void
    {
        $dataPath = dirname(__DIR__, 2).'/data/';
        $companies = SourceData::decodeFile($dataPath.'clientes.json');
        $paymentTerms = SourceData::decodeFile($dataPath.'condicoes_pagamento.json');
        $priceTables = SourceData::decodeFile($dataPath.'tabelas_preco.json');
        $products = SourceData::decodeFile($dataPath.'produtos.json');
        $inventory = SourceData::decodeFile($dataPath.'estoque.json');
        $promotions = SourceData::decodeFile($dataPath.'promocoes.json');

        $this->assertCount(3, $companies);
        $this->assertCount(3, $paymentTerms);
        $this->assertCount(500, $products);
        $this->assertCount(500, $inventory);
        $this->assertCount(3, $promotions);
        $this->assertCount(2, $priceTables);

        foreach ($companies as $company) {
            SourceData::moneyToCents($company['limite_credito'], 'limite_credito');
            SourceData::moneyToCents($company['limite_utilizado'], 'limite_utilizado');
        }

        foreach ($paymentTerms as $paymentTerm) {
            SourceData::moneyToCents($paymentTerm['valor_minimo'], 'valor_minimo');
            SourceData::percentageToDatabaseDecimal($paymentTerm['acrescimo_percentual'], 'acrescimo_percentual');
        }

        foreach ($priceTables as $priceTable) {
            foreach ($priceTable['itens'] as $item) {
                SourceData::moneyToCents($item['preco'], 'preco');

                if (isset($item['preco_atacado'])) {
                    SourceData::moneyToCents($item['preco_atacado'], 'preco_atacado');
                }
            }
        }

        foreach ($promotions as $promotion) {
            SourceData::percentageToDatabaseDecimal($promotion['percentual_desconto'], 'percentual_desconto');
        }
    }
}
