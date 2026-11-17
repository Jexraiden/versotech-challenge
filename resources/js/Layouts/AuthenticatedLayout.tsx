import { Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import type { FormEvent, ReactNode } from 'react';
import styles from './AuthenticatedLayout.module.css';

type SharedProps = {
    auth: { user: { name: string } | null };
    navigation: { categories: string[]; cart: { item_count: number } };
};

export default function AuthenticatedLayout({ children }: { children: ReactNode }) {
    const page = usePage<SharedProps>();
    const { auth, navigation } = page.props;
    const url = page.url;
    const [search, setSearch] = useState('');
    const [category, setCategory] = useState('');
    const [accountOpen, setAccountOpen] = useState(false);
    const [busy, setBusy] = useState(false);
    const [searchError, setSearchError] = useState('');

    useEffect(() => {
        setSearch('');
        setCategory('');
        setAccountOpen(false);
    }, [url]);

    const submitSearch = (event: FormEvent) => {
        event.preventDefault();
        setSearchError('');
        router.get('/catalog', { search, category }, {
            onStart: () => setBusy(true),
            onFinish: () => setBusy(false),
            onError: () => setSearchError('Não foi possível atualizar a busca. Confira os filtros e tente novamente.'),
        });
    };

    return <>
        <header className={styles.header}>
            <Link className={styles.brand} href="/catalog">Ecommerce B2B</Link>
            <Link className={styles.catalogLink} href="/catalog">Catálogo</Link>
            <form className={styles.search} onSubmit={submitSearch}>
                <label className={styles.visuallyHidden} htmlFor="global-category">Categoria</label>
                <select id="global-category" value={category} onChange={event => setCategory(event.target.value)}>
                    <option value="">Todas</option>
                    {navigation.categories.map(item => <option key={item} value={item}>{item}</option>)}
                </select>
                <label className={styles.visuallyHidden} htmlFor="global-search">Buscar produtos por nome, SKU ou EAN</label>
                <input id="global-search" value={search} onChange={event => setSearch(event.target.value)} placeholder="Ex.: café, SKU ou EAN" />
                <button type="submit" disabled={busy}>{busy ? 'Buscando…' : 'Buscar'}</button>
            </form>
            <div className={styles.account} onKeyDown={event => { if (event.key === 'Escape') setAccountOpen(false); }}>
                <button type="button" aria-haspopup="menu" aria-expanded={accountOpen} onClick={() => setAccountOpen(open => !open)}>Olá, {auth.user?.name ?? 'Conta'}</button>
                {accountOpen && <div className={styles.menu} role="menu">
                    <Link role="menuitem" href="/orders">Ver pedidos</Link>
                    <button role="menuitem" type="button" onClick={() => router.post('/logout')}>Sair da conta</button>
                </div>}
            </div>
            <Link className={styles.cart} href="/cart">Ver Carrinho <span aria-label={`${navigation.cart.item_count} unidades no carrinho`}>{navigation.cart.item_count}</span></Link>
        </header>
        {searchError && <p className={styles.searchError} role="alert">{searchError}</p>}
        {children}
    </>;
}
