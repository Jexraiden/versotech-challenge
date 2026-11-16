import { useForm } from '@inertiajs/react';
import pageChrome from '../Components/PageChrome.module.css';
import primaryButton from '../Components/PrimaryButton.module.css';
import styles from './Welcome.module.css';

type WelcomeProps = {
    appName: string;
};

export default function Welcome({ appName }: WelcomeProps) {
    const form = useForm();

    return (
        <main className={pageChrome.pageShell}>
            <section className={styles.card} aria-labelledby="page-title">
                <p className={pageChrome.eyebrow}>Comércio B2B</p>
                <h1 id="page-title">{appName}</h1>
                <p>Você está conectado. O catálogo estará disponível em breve.</p>
                <button
                    className={primaryButton.primaryButton}
                    type="button"
                    disabled={form.processing}
                    onClick={() => form.post('/logout')}
                >
                    {form.processing ? 'Saindo…' : 'Sair'}
                </button>
            </section>
        </main>
    );
}
