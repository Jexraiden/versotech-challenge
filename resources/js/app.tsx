import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

type PageModule = { default: React.ComponentType };

const pages = import.meta.glob<PageModule>('./Pages/**/*.tsx', { eager: true });

createInertiaApp({
    resolve: (name) => {
        const page = pages[`./Pages/${name}.tsx`];

        if (!page) {
            throw new Error(`Inertia page not found: ${name}`);
        }

        return page.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
