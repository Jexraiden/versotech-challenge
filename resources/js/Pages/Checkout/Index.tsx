import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import pageChrome from '../../Components/PageChrome.module.css';
import primaryButton from '../../Components/PrimaryButton.module.css';
import styles from './Checkout.module.css';

type Line = { id: number; sku?: string; name?: string; quantity: number; valid: boolean; issue?: string; unit_price_cents?: number; line_total_cents?: number };
type Term = { code: string; description: string; minimum_amount_cents: number; surcharge_percent: string; eligible: boolean; restriction: string | null };
type Props = {
    cart: { items: Line[]; subtotal_cents: number; savings_cents: number };
    company: { name: string; city: string; state: string; is_blocked: boolean };
    paymentTerms: Term[];
    checkoutKey: string;
    states: string[];
    errors?: Record<string, string>;
};
const formatPrice = (cents: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);
const percentageOf = (amountCents: number, percentage: string) => {
    const [whole, fraction = ''] = percentage.split('.');
    const scaled = BigInt(whole) * 10_000n + BigInt(fraction.padEnd(4, '0').slice(0, 4));
    return Number((BigInt(amountCents) * scaled + 500_000n) / 1_000_000n);
};

const emptyAddress = { zipcode: '', state: '', city: '', district: '', address: '', address_extra: '' };

export default function Index({ cart, company, paymentTerms, checkoutKey, states, errors = {} }: Props) {
    const [paymentTerm, setPaymentTerm] = useState(paymentTerms.find(term => term.eligible)?.code ?? '');
    const [deliveryAddress, setDeliveryAddress] = useState(emptyAddress);
    const [busy, setBusy] = useState(false);
    const [submitError, setSubmitError] = useState('');
    const invalidItems = cart.items.some(item => !item.valid);
    const selectedTerm = paymentTerms.find(term => term.code === paymentTerm && term.eligible);
    const surcharge = selectedTerm ? percentageOf(cart.subtotal_cents, selectedTerm.surcharge_percent) : 0;
    const total = cart.subtotal_cents + surcharge;
    const submit = (event: FormEvent) => {
        event.preventDefault();
        setSubmitError('');
        router.post('/orders', { payment_term: paymentTerm, ...deliveryAddress, checkout_key: checkoutKey }, {
            onStart: () => setBusy(true), onFinish: () => setBusy(false),
            onError: response => setSubmitError(response.checkout ?? response.payment_term ?? Object.values(response)[0] ?? 'Não foi possível confirmar. Revise os dados e tente novamente.'),
        });
    };

    return <main className={pageChrome.pageShell}><div className={styles.page}>
        <header className={styles.header}><div><p className={pageChrome.eyebrow}>Checkout · {company.name}</p><h1>Revisar pedido</h1><p>O endereço informado será usado para a entrega deste pedido.</p></div><Link href="/cart">Voltar ao carrinho</Link></header>
        {submitError && <p className={styles.error} role="alert">{submitError}</p>}
        {invalidItems && <p className={styles.error} role="alert">Há itens com impedimentos. Volte ao carrinho para corrigir antes de continuar.</p>}
        <form onSubmit={submit} aria-busy={busy}>
            <div className={styles.layout}>
                <div className={styles.mainColumn}>
                    <section className={styles.card} aria-labelledby="company-heading"><h2 id="company-heading">Empresa</h2><strong>{company.name}</strong><p>{company.city} · {company.state}</p></section>
                    <section className={styles.card} aria-labelledby="delivery-heading">
                        <h2 id="delivery-heading">Endereço de entrega</h2><p>Informe o endereço completo para esta compra.</p>
                        <div className={styles.addressGrid}>
                            <label htmlFor="zipcode"><span>CEP <span aria-hidden="true">*</span></span><input id="zipcode" name="zipcode" autoComplete="postal-code" required maxLength={20} value={deliveryAddress.zipcode} onChange={event => setDeliveryAddress({ ...deliveryAddress, zipcode: event.target.value })} aria-invalid={Boolean(errors.zipcode)} /></label>
                            <label htmlFor="state"><span>UF <span aria-hidden="true">*</span></span><select id="state" name="state" autoComplete="address-level1" required value={deliveryAddress.state} onChange={event => setDeliveryAddress({ ...deliveryAddress, state: event.target.value })} aria-invalid={Boolean(errors.state)}><option value="">Selecione</option>{states.map(state => <option key={state} value={state}>{state}</option>)}</select></label>
                            <label htmlFor="city"><span>Cidade <span aria-hidden="true">*</span></span><input id="city" name="city" autoComplete="address-level2" required maxLength={120} value={deliveryAddress.city} onChange={event => setDeliveryAddress({ ...deliveryAddress, city: event.target.value })} aria-invalid={Boolean(errors.city)} /></label>
                            <label htmlFor="district"><span>Bairro <span aria-hidden="true">*</span></span><input id="district" name="district" required maxLength={120} value={deliveryAddress.district} onChange={event => setDeliveryAddress({ ...deliveryAddress, district: event.target.value })} aria-invalid={Boolean(errors.district)} /></label>
                            <label className={styles.addressWide} htmlFor="address"><span>Logradouro <span aria-hidden="true">*</span></span><input id="address" name="address" autoComplete="address-line1" required maxLength={255} value={deliveryAddress.address} onChange={event => setDeliveryAddress({ ...deliveryAddress, address: event.target.value })} aria-invalid={Boolean(errors.address)} /></label>
                            <label className={styles.addressWide} htmlFor="address_extra">Complemento<input id="address_extra" name="address_extra" autoComplete="address-line2" maxLength={255} value={deliveryAddress.address_extra} onChange={event => setDeliveryAddress({ ...deliveryAddress, address_extra: event.target.value })} aria-invalid={Boolean(errors.address_extra)} /></label>
                        </div>
                        {Object.entries(errors).filter(([field]) => field in emptyAddress).map(([field, message]) => <p key={field} className={styles.error} role="alert">{message}</p>)}
                    </section>
                    <section className={styles.card} aria-labelledby="items-heading"><h2 id="items-heading">Itens ({cart.items.length})</h2><ul className={styles.lines}>{cart.items.map(item => <li key={item.id}><div><strong>{item.name ?? `Produto ${item.id}`}</strong><span>{item.sku} · {item.quantity} un. × {formatPrice(item.unit_price_cents ?? 0)}</span>{!item.valid && <span className={styles.error}>{item.issue}</span>}</div><strong>{formatPrice(item.line_total_cents ?? 0)}</strong></li>)}</ul></section>
                </div>
                <aside className={styles.summary} aria-label="Condição e total do pedido"><section className={styles.card}><h2>Condição de pagamento</h2><fieldset><legend>Selecione uma condição disponível</legend>{paymentTerms.map(term => <label className={`${styles.term} ${!term.eligible ? styles.termDisabled : ''}`} key={term.code}><input type="radio" name="payment-term" value={term.code} checked={paymentTerm === term.code} onChange={() => setPaymentTerm(term.code)} disabled={!term.eligible} required /><span><strong>{term.description}</strong><small>{term.surcharge_percent !== '0.0000' ? `Acréscimo de ${term.surcharge_percent}%` : 'Sem acréscimo'}</small>{term.restriction && <small className={styles.restriction}>{term.restriction}</small>}</span></label>)}</fieldset><div className={styles.totals}><div><span>Subtotal</span><strong>{formatPrice(cart.subtotal_cents)}</strong></div><div><span>Acréscimo</span><strong>{formatPrice(surcharge)}</strong></div><div className={styles.total}><span>Total do pedido</span><strong>{formatPrice(total)}</strong></div></div><p className={styles.note}>A confirmação revalida preço, estoque, condição comercial e crédito.</p>{company.is_blocked && <p id="checkout-blocked-message" className={styles.blockedNotice} role="alert">Sua empresa está bloqueada e não pode confirmar pedidos.</p>}<button className={`${primaryButton.primaryButton} ${company.is_blocked ? styles.blockedButton : ''}`} type="submit" disabled={busy || company.is_blocked || invalidItems || !selectedTerm} aria-describedby={company.is_blocked ? 'checkout-blocked-message' : undefined}>{busy ? 'Enviando pedido…' : company.is_blocked ? 'Empresa bloqueada' : 'Confirmar pedido'}</button></section></aside>
            </div>
        </form>
    </div></main>;
}
