import { Link, router } from '@inertiajs/react';
import pageChrome from '../../Components/PageChrome.module.css';
import styles from './Index.module.css';

type Order = { id: number; created_at: string; status: string; total_cents: number };
type Props = { orders: { data: Order[]; links: Array<{ url: string | null; label: string; active: boolean }>; current_page: number; last_page: number; total: number } };
const formatPrice = (cents: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);

export default function Index({ orders }: Props) {
    return <main className={pageChrome.pageShell}><section className={styles.page}>
        <p className={pageChrome.eyebrow}>Conta</p><h1>Meus pedidos</h1>
        {orders.data.length === 0 ? <p className={styles.empty}>Você ainda não tem pedidos.</p> : <>
            <div className={styles.list}>{orders.data.map(order => <article className={styles.order} key={order.id}>
                <div><Link href={`/orders/${order.id}`}>Pedido #{order.id}</Link><time dateTime={order.created_at}>{new Intl.DateTimeFormat('pt-BR', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(order.created_at))}</time></div>
                <span>{order.status}</span><strong>{formatPrice(order.total_cents)}</strong>
            </article>)}</div>
            <nav className={styles.pagination} aria-label="Paginação dos pedidos">{orders.links.map((link, index) => {
                const previous = link.label === 'pagination.previous' || link.label.includes('&laquo;');
                const next = link.label === 'pagination.next' || link.label.includes('&raquo;');
                const label = previous ? '←' : next ? '→' : link.label;
                return <button key={`${index}-${link.label}`} type="button" disabled={!link.url || link.active} aria-label={previous ? 'Página anterior' : next ? 'Próxima página' : `Página ${link.label}`} aria-current={link.active ? 'page' : undefined} onClick={() => link.url && router.visit(link.url, { preserveScroll: true })}>{label}</button>;
            })}</nav>
        </>}
    </section></main>;
}
