import pageChrome from '../../Components/PageChrome.module.css';
import styles from './Catalog.module.css';

type Product = {
    id: number; sku: string; ean: string; name: string; description: string | null; brand: string; category: string;
    is_active: boolean; available: boolean; stock_quantity: number; unit_price_cents: number | null;
    regular_price_cents: number | null; wholesale_price_cents: number | null; wholesale_min_qty: number | null;
};
const formatPrice = (cents: number | null) => cents === null ? 'Sem preço aplicável' : new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);

export default function Show({ product }: { product: Product }) {
    return <main className={pageChrome.pageShell}><article className={styles.detail}>
        <p className={pageChrome.eyebrow}>{product.sku} · {product.ean}</p>
        <h1>{product.name}</h1>
        <p>{product.description || 'Descrição não informada.'}</p>
        <dl><div><dt>Marca</dt><dd>{product.brand || 'Não informada'}</dd></div><div><dt>Categoria</dt><dd>{product.category}</dd></div><div><dt>Preço aplicável</dt><dd>{product.regular_price_cents !== null && product.unit_price_cents !== null && product.unit_price_cents < product.regular_price_cents && <><s>{formatPrice(product.regular_price_cents)}</s>{' '}</>}<strong>{formatPrice(product.unit_price_cents)}</strong></dd></div><div><dt>Disponibilidade</dt><dd>{product.available ? `${product.stock_quantity} unidades disponíveis` : 'Indisponível'}</dd></div>
            {product.wholesale_price_cents !== null && <div><dt>Preço de atacado</dt><dd>{product.regular_price_cents !== null && product.wholesale_price_cents < product.regular_price_cents && <><s>{formatPrice(product.regular_price_cents)}</s>{' '}</>}<strong>{formatPrice(product.wholesale_price_cents)}</strong> a partir de {product.wholesale_min_qty} unidades</dd></div>}
        </dl>
        {!product.is_active && <p className={styles.unavailable}>Produto inativo</p>}
    </article></main>;
}
