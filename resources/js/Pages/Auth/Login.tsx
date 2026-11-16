import { Head, useForm } from '@inertiajs/react';
import type { SyntheticEvent } from 'react';
import pageChrome from '../../Components/PageChrome.module.css';
import primaryButton from '../../Components/PrimaryButton.module.css';
import styles from './Login.module.css';

export default function Login() {
    const form = useForm({ email: '', password: '' });

    function submit(event: SyntheticEvent<HTMLFormElement>) {
        event.preventDefault();
        form.post('/login', { onFinish: () => form.reset('password') });
    }

    return (
        <main className={pageChrome.pageShell}>
            <Head title="Entrar" />
            <section className={styles.card} aria-labelledby="login-title">
                <p className={pageChrome.eyebrow}>Versotech Commerce</p>
                <h1 id="login-title">Acesse sua conta</h1>
                <p className={styles.intro}>Entre para consultar o catálogo da sua empresa.</p>
                <form onSubmit={submit} aria-busy={form.processing}>
                    <div className={styles.field}>
                        <label htmlFor="email">E-mail</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autoComplete="username"
                            required
                            autoFocus
                            value={form.data.email}
                            onChange={(event) => form.setData('email', event.target.value)}
                            aria-invalid={Boolean(form.errors.email)}
                            aria-describedby={form.errors.email ? 'login-error' : undefined}
                        />
                    </div>
                    <div className={styles.field}>
                        <label htmlFor="password">Senha</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autoComplete="current-password"
                            required
                            value={form.data.password}
                            onChange={(event) => form.setData('password', event.target.value)}
                            aria-invalid={Boolean(form.errors.password)}
                            aria-describedby={form.errors.password ? 'login-error' : undefined}
                        />
                    </div>
                    {Object.keys(form.errors).length > 0 && (
                        <p id="login-error" className={styles.error} role="alert">
                            {form.errors.email ?? form.errors.password}
                        </p>
                    )}
                    <button className={primaryButton.primaryButton} type="submit" disabled={form.processing}>
                        {form.processing ? 'Entrando…' : 'Entrar'}
                    </button>
                </form>
            </section>
        </main>
    );
}
