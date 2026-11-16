type WelcomeProps = {
    appName: string;
};

export default function Welcome({ appName }: WelcomeProps) {
    return (
        <main className="page-shell">
            <section className="welcome-card" aria-labelledby="page-title">
                <p className="eyebrow">Comércio B2B</p>
                <h1 id="page-title">{appName}</h1>
                <p>Fundação da plataforma Versotech pronta para receber o catálogo.</p>
            </section>
        </main>
    );
}
