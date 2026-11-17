import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import pageChrome from '../../Components/PageChrome.module.css';
import styles from './Catalog.module.css';

type Product = {
    id: number; sku: string; ean: string; name: string; description: string | null;
    brand: string; category: string; available: boolean; stock_quantity: number;
    unit_price_cents: number | null; regular_price_cents: number | null;
    promotion_price_cents: number | null; wholesale_price_cents: number | null; wholesale_min_qty: number | null;
};
type LinkItem = { url: string | null; label: string; active: boolean };
type Props = {
    products: { data: Product[]; links: LinkItem[]; current_page: number; last_page: number; total: number };
    filters: { search: string; category: string }; companyName: string;
    flash: { cart_success?: string | null };
};

const formatPrice = (cents: number | null) => cents === null ? 'Indisponível' : new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);

export default function Index({ products, filters, companyName, flash }: Props) {
    const [quantity, setQuantity] = useState<Record<number, string>>({});
    const [error, setError] = useState('');
    const [adding, setAdding] = useState<number | null>(null);
    const [dismissed, setDismissed] = useState(false);

    return <main className={pageChrome.pageShell}>
        <div className={styles.catalog}>
            <header className={styles.header}>
                <div><p className={pageChrome.eyebrow}>Catálogo · {companyName}</p><h1>Produtos</h1><p>Consulte preços e disponibilidade da sua empresa.</p></div>
            </header>
            {error && <p className={styles.error} role="alert">{error}</p>}
            {flash.cart_success && !dismissed && <aside className={styles.toast} role="status" aria-live="polite"><span className={styles.toastIcon} aria-hidden="true">✓</span><div><strong>Sucesso</strong><p>Item adicionado ao carrinho</p></div><button type="button" aria-label="Dispensar notificação" onClick={() => setDismissed(true)}>×</button></aside>}
            <p className={styles.count} aria-live="polite">{products.total} produtos encontrados</p>
            {products.data.length === 0 ? <section className={styles.empty}><h2>{filters.search || filters.category ? 'Nenhum produto encontrado' : 'Catálogo vazio'}</h2><p>Tente ajustar a busca ou escolher outra categoria.</p></section> : <div className={styles.productList} aria-busy={adding !== null}>
                {products.data.map(product => {
                    const quantityInput = quantity[product.id] ?? '1';
                    const selectedQuantity = parseQuantity(quantityInput, product.stock_quantity);
                    const unitPrice = getUnitPrice(product, selectedQuantity ?? 1);

                    return <article className={styles.product} key={product.id}>
                        <div className={styles.productInfo}><span className={styles.code}>{product.sku} · {product.ean}</span><h2><Link href={`/catalog/${product.id}`}>{product.name}</Link></h2><p>{product.brand} · {product.category}</p></div>
                        <div className={styles.price}>
                            {product.regular_price_cents !== null && unitPrice !== null && unitPrice < product.regular_price_cents && <s>{formatPrice(product.regular_price_cents)}</s>}
                            <strong>{formatPrice(unitPrice)}</strong>
                        </div>
                        <div className={styles.wholesaleSlot}>
                            {product.wholesale_price_cents !== null && <><span>Atacado</span><strong>{formatPrice(product.wholesale_price_cents)}</strong><span>a partir de {product.wholesale_min_qty}</span></>}
                        </div>
                        <span className={product.available ? styles.available : styles.unavailable}>{product.available ? `${product.stock_quantity} disponíveis` : 'Indisponível'}</span>
                        <label className={styles.quantity}>Quantidade<input type="number" min="1" step="1" max={Math.max(product.stock_quantity, 1)} value={quantityInput} disabled={!product.available} aria-invalid={quantityInput !== '' && selectedQuantity === null} onChange={event => setQuantity(current => ({ ...current, [product.id]: event.target.value }))} /></label>
                        <button className={styles.addButton} type="button" disabled={!product.available || selectedQuantity === null || adding !== null} onClick={() => {
                            if (selectedQuantity === null) return;
                            setError('');
                            router.post(`/cart/items/${product.id}`, { quantity: selectedQuantity }, {
                                preserveScroll: true,
                                onStart: () => { setAdding(product.id); setDismissed(false); },
                                onFinish: () => setAdding(null),
                                onError: errors => setError(errors.quantity ?? 'Não foi possível adicionar este produto. Atualize a página e tente novamente.'),
                            });
                        }}>{adding === product.id ? 'Adicionando…' : 'Adicionar ao carrinho'}</button>
                    </article>;
                })}
            </div>}
            <nav className={styles.pagination} aria-label="Paginação">{products.links.map((link, index) => {
                const isPrevious = link.label === 'pagination.previous' || link.label.includes('&laquo;');
                const isNext = link.label === 'pagination.next' || link.label.includes('&raquo;');
                const label = isPrevious ? '←' : isNext ? '→' : link.label;
                const accessibleLabel = isPrevious ? 'Página anterior' : isNext ? 'Próxima página' : `Página ${link.label}`;

                return <button key={`${index}-${link.label}`} type="button" disabled={!link.url || link.active || adding !== null} aria-label={accessibleLabel} aria-current={link.active ? 'page' : undefined} onClick={() => link.url && router.visit(link.url, { preserveScroll: true })}>{label}</button>;
            })}</nav>
        </div>
    </main>;
}

function parseQuantity(value: string, stockQuantity: number): number | null {
    if (!/^[1-9]\d*$/.test(value)) return null;
    const parsed = Number(value);
    return Number.isSafeInteger(parsed) && parsed <= stockQuantity ? parsed : null;
}

function getUnitPrice(product: Product, quantity: number): number | null {
    if (product.regular_price_cents === null) return null;
    const candidates = [product.regular_price_cents];
    if (product.promotion_price_cents !== null) candidates.push(product.promotion_price_cents);
    if (product.wholesale_price_cents !== null && product.wholesale_min_qty !== null && quantity >= product.wholesale_min_qty) {
        candidates.push(product.wholesale_price_cents);
    }
    return Math.min(...candidates);
}
