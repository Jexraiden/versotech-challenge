import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import pageChrome from '../../Components/PageChrome.module.css';
import primaryButton from '../../Components/PrimaryButton.module.css';
import styles from './Cart.module.css';

type CartLine = {
    id: number; sku?: string; name?: string; quantity: number; valid: boolean; issue?: string;
    stock_quantity?: number; regular_price_cents?: number; wholesale_price_cents?: number | null; wholesale_min_qty?: number | null;
    unit_price_cents?: number; line_total_cents?: number; can_update?: boolean;
};
type Props = {
    cart: { items: CartLine[]; item_count: number; subtotal_cents: number; savings_cents: number; total_cents: number; notice: string | null };
    companyName: string;
    flash: { cart_success?: string | null };
};

const formatPrice = (cents: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);

export default function Index({ cart, companyName, flash }: Props) {
    const [quantities, setQuantities] = useState<Record<number, number>>({});
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState('');
    const update = (event: FormEvent, item: CartLine) => {
        event.preventDefault();
        setError('');
        router.patch(`/cart/items/${item.id}`, { quantity: quantities[item.id] ?? item.quantity }, {
            preserveScroll: true,
            onStart: () => setBusy(true),
            onFinish: () => setBusy(false),
            onError: errors => setError(errors.quantity ?? 'Não foi possível atualizar a quantidade. Confira o estoque e tente novamente.'),
        });
    };
    const remove = (item: CartLine) => {
        setError('');
        router.delete(`/cart/items/${item.id}`, { preserveScroll: true, onStart: () => setBusy(true), onFinish: () => setBusy(false), onError: () => setError('Não foi possível remover este item. Atualize a página e tente novamente.') });
    };

    return <main className={pageChrome.pageShell}>
        <div className={styles.cartPage} aria-busy={busy}>
            <header className={styles.header}>
                <div><p className={pageChrome.eyebrow}>Carrinho · {companyName}</p><h1>Seu pedido</h1><p>Confira os itens e os valores atuais para sua empresa.</p></div>
                <Link className={styles.continueShopping} href="/catalog">Continuar comprando</Link>
            </header>
            {flash.cart_success && <p className={styles.success} role="status">{flash.cart_success}</p>}
            {cart.notice && <p className={styles.issue} role="status">{cart.notice}</p>}
            {error && <p className={styles.error} role="alert">{error}</p>}
            {cart.items.length === 0 ? <section className={styles.empty}><h2>Seu carrinho está vazio</h2><p>Explore o catálogo e adicione produtos para montar seu pedido.</p><Link className={primaryButton.primaryButton} href="/catalog">Voltar ao catálogo</Link></section> : <div className={styles.layout}>
                <section className={styles.items} aria-label="Itens do carrinho">
                    {cart.items.map(item => <article className={styles.item} key={item.id}>
                        <div className={styles.itemInfo}><span>{item.sku ?? `Produto ${item.id}`}</span><h2>{item.name ?? 'Produto indisponível'}</h2>
                            {!item.valid && <p className={styles.issue} role="status">{item.issue}</p>}
                            {item.valid && <p>
                                {item.regular_price_cents! > item.unit_price_cents! && <><s>{formatPrice(item.regular_price_cents!)}</s>{' '}</>}
                                <strong>{formatPrice(item.unit_price_cents!)}</strong> por unidade
                            </p>}
                            {item.valid && item.wholesale_price_cents !== null && item.wholesale_price_cents !== undefined && item.wholesale_min_qty !== null && item.wholesale_min_qty !== undefined && <small className={styles.wholesale}>Atacado: {formatPrice(item.wholesale_price_cents)} a partir de {item.wholesale_min_qty} unidades</small>}
                        </div>
                        <div className={styles.controls}>
                            {(item.valid || item.can_update) && <form onSubmit={event => update(event, item)}>
                                <label htmlFor={`quantity-${item.id}`}>Quantidade</label>
                                <input id={`quantity-${item.id}`} type="number" min="1" step="1" max={item.stock_quantity ?? item.quantity} value={quantities[item.id] ?? item.quantity} onChange={event => { const parsed = Number(event.target.value); setQuantities({ ...quantities, [item.id]: Number.isFinite(parsed) ? Math.max(1, parsed) : 1 }); }} />
                                <button type="submit" disabled={busy}>Atualizar</button>
                            </form>}
                            {item.valid && <strong>{formatPrice(item.line_total_cents!)}</strong>}
                            <button className={styles.remove} type="button" disabled={busy} onClick={() => remove(item)}>Remover</button>
                        </div>
                    </article>)}
                </section>
                <aside className={styles.summary} aria-label="Resumo financeiro">
                    <h2>Resumo</h2><div><span>Unidades válidas</span><strong>{cart.item_count}</strong></div>
                    {cart.savings_cents > 0 && <div><span>Você economiza</span><strong>{formatPrice(cart.savings_cents)}</strong></div>}
                    <div><span>Subtotal</span><strong>{formatPrice(cart.subtotal_cents)}</strong></div>
                    <div className={styles.total}><span>Total atual</span><strong>{formatPrice(cart.total_cents)}</strong></div>
                    {cart.items.some(item => !item.valid) && <p className={styles.issue}>Remova ou ajuste os itens com impedimentos para continuar.</p>}
                    <Link className={primaryButton.primaryButton} href="/checkout" aria-disabled={cart.items.some(item => !item.valid)} onClick={event => { if (cart.items.some(item => !item.valid)) event.preventDefault(); }}>Continuar para checkout</Link>
                </aside>
            </div>}
        </div>
    </main>;
}
