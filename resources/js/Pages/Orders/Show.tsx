import pageChrome from '../../Components/PageChrome.module.css';
import styles from './Show.module.css';

type Order = { id: number; status: string; delivery_address: { zipcode: string; state: string; city: string; district: string; address: string; address_extra: string | null } | null; subtotal_cents: number; surcharge_cents: number; total_cents: number; payment_term: string; items: Array<{ sku: string; name: string; quantity: number; unit_price_cents: number; line_total_cents: number }> };
const formatPrice = (cents: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);

export default function Show({ order }: { order: Order }) {
    return <main className={pageChrome.pageShell}><article className={styles.page}>
        <p className={pageChrome.eyebrow}>Pedido recebido</p><h1>Pedido #{order.id} confirmado</h1><p className={styles.status}>Status: {order.status}</p>
        <section><h2>Entrega</h2>{order.delivery_address ? <p>{order.delivery_address.address}{order.delivery_address.address_extra ? `, ${order.delivery_address.address_extra}` : ''}<br />{order.delivery_address.district}, {order.delivery_address.city} - {order.delivery_address.state}<br />CEP {order.delivery_address.zipcode}</p> : <p>Endereço estruturado indisponível para este pedido.</p>}</section>
        <section><h2>Condição de pagamento</h2><p>{order.payment_term}</p></section>
        <section><h2>Resumo</h2><ul>{order.items.map((item, index) => <li key={`${item.sku}-${index}`}><span>{item.name} · {item.quantity} un.</span><strong>{formatPrice(item.line_total_cents)}</strong></li>)}</ul><p>Subtotal: {formatPrice(order.subtotal_cents)}</p><p>Acréscimo: {formatPrice(order.surcharge_cents)}</p><p className={styles.total}>Total: {formatPrice(order.total_cents)}</p></section>
    </article></main>;
}
