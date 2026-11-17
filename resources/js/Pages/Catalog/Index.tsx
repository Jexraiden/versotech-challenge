import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import pageChrome from '../../Components/PageChrome.module.css';
import primaryButton from '../../Components/PrimaryButton.module.css';
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
    categories: string[]; filters: { search: string; category: string }; companyName: string;
    cart: { items: unknown[]; item_count: number; total_cents: number };
    flash: { cart_success?: string | null };
};

const formatPrice = (cents: number | null) => cents === null ? 'Indisponível' : new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);

export default function Index({ products, categories, filters, companyName, cart, flash }: Props) {
    const [search, setSearch] = useState(filters.search);
    const [category, setCategory] = useState(filters.category);
    const [quantity, setQuantity] = useState<Record<number, number>>({});
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [adding, setAdding] = useState<number | null>(null);
    const [loggingOut, setLoggingOut] = useState(false);
    const submitSearch = (event: FormEvent) => {
        event.preventDefault();
        setError('');
        router.get('/catalog', { search, category }, {
            preserveState: true,
            preserveScroll: true,
            onStart: () => setLoading(true),
            onFinish: () => setLoading(false),
            onError: () => setError('Não foi possível atualizar a busca. Confira os filtros e tente novamente.'),
        });
    };

    return <main className={pageChrome.pageShell}>
        <div className={styles.catalog}>
            <header className={styles.header}>
                <div><p className={pageChrome.eyebrow}>Catálogo · {companyName}</p><h1>Produtos</h1><p>Consulte preços e disponibilidade da sua empresa.</p></div>
                <button className={primaryButton.primaryButton} type="button" disabled={loggingOut} onClick={() => router.post('/logout', {}, { onStart: () => setLoggingOut(true), onFinish: () => setLoggingOut(false) })}>{loggingOut ? 'Saindo…' : 'Sair'}</button>
            </header>
            <form className={styles.filters} onSubmit={submitSearch} aria-busy={loading}>
                <label className={styles.search}>Buscar por nome, SKU ou EAN<input value={search} onChange={event => setSearch(event.target.value)} placeholder="Ex.: café, SKU ou EAN" /></label>
                <label>Categoria<select value={category} onChange={event => setCategory(event.target.value)}><option value="">Todas</option>{categories.map(item => <option key={item}>{item}</option>)}</select></label>
                <button className={primaryButton.primaryButton} type="submit" disabled={loading}>{loading ? 'Buscando…' : 'Buscar'}</button>
            </form>
            {error && <p className={styles.error} role="alert">{error}</p>}
            {flash.cart_success && <p className={styles.success} role="status">{flash.cart_success}</p>}
            {loading && <p className={styles.loading} role="status">Atualizando catálogo…</p>}
            <p className={styles.count} aria-live="polite">{products.total} produtos encontrados</p>
            {products.data.length === 0 ? <section className={styles.empty}><h2>{filters.search || filters.category ? 'Nenhum produto encontrado' : 'Catálogo vazio'}</h2><p>Tente ajustar a busca ou escolher outra categoria.</p></section> : <div className={styles.productList} aria-busy={loading || adding !== null}>
                {products.data.map(product => <article className={styles.product} key={product.id}>
                    <div className={styles.productInfo}><span className={styles.code}>{product.sku} · {product.ean}</span><h2><Link href={`/catalog/${product.id}`}>{product.name}</Link></h2><p>{product.brand} · {product.category}</p></div>
                    <div className={styles.price}>
                        {product.regular_price_cents !== null && getUnitPrice(product, quantity[product.id] ?? 1) !== null && getUnitPrice(product, quantity[product.id] ?? 1)! < product.regular_price_cents && <s>{formatPrice(product.regular_price_cents)}</s>}
                        <strong>{formatPrice(getUnitPrice(product, quantity[product.id] ?? 1))}</strong>
                    </div>
                    <div className={styles.wholesaleSlot}>
                        {product.wholesale_price_cents !== null && <><span>Atacado</span><strong>{formatPrice(product.wholesale_price_cents)}</strong><span>a partir de {product.wholesale_min_qty}</span></>}
                    </div>
                    <span className={product.available ? styles.available : styles.unavailable}>{product.available ? `${product.stock_quantity} disponíveis` : 'Indisponível'}</span>
                    <label className={styles.quantity}>Quantidade<input type="number" min="1" step="1" max={Math.max(product.stock_quantity, 1)} value={quantity[product.id] ?? 1} disabled={!product.available} onChange={event => { const parsed = Number(event.target.value); setQuantity({ ...quantity, [product.id]: Number.isFinite(parsed) ? Math.min(product.stock_quantity, Math.max(1, parsed)) : 1 }); }} /></label>
                    <button className={styles.addButton} type="button" disabled={!product.available || adding !== null} onClick={() => {
                            setError('');
                            router.post(`/cart/items/${product.id}`, { quantity: quantity[product.id] ?? 1 }, {
                                preserveScroll: true,
                                onStart: () => setAdding(product.id),
                                onFinish: () => setAdding(null),
                                onError: errors => setError(errors.quantity ?? 'Não foi possível adicionar este produto. Atualize a página e tente novamente.'),
                            });
                        }}>{adding === product.id ? 'Adicionando…' : 'Adicionar ao carrinho'}</button>
                </article>)}
            </div>}
            <nav className={styles.pagination} aria-label="Paginação">{products.links.map((link, index) => {
                const isPrevious = link.label === 'pagination.previous' || link.label.includes('&laquo;');
                const isNext = link.label === 'pagination.next' || link.label.includes('&raquo;');
                const label = isPrevious ? '←' : isNext ? '→' : link.label;
                const accessibleLabel = isPrevious ? 'Página anterior' : isNext ? 'Próxima página' : `Página ${link.label}`;

                return <button key={`${index}-${link.label}`} type="button" disabled={!link.url || link.active || loading || adding !== null} aria-label={accessibleLabel} aria-current={link.active ? 'page' : undefined} onClick={() => link.url && router.visit(link.url, { preserveScroll: true })}>{label}</button>;
            })}</nav>
        </div>
        {cart.items.length > 0 && <aside className={styles.cartBar} aria-label="Resumo do carrinho">
            <span>{cart.item_count} {cart.item_count === 1 ? 'unidade' : 'unidades'} · {formatPrice(cart.total_cents)}</span>
            <Link href="/cart" className={styles.cartLink}>Ver carrinho</Link>
        </aside>}
    </main>;
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
