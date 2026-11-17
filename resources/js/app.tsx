import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import type { ResolvedComponent } from '@inertiajs/react';
import type { ReactNode } from 'react';
import AuthenticatedLayout from './Layouts/AuthenticatedLayout';

type PageModule = { default: ResolvedComponent };

const pages = import.meta.glob<PageModule>('./Pages/**/*.tsx', { eager: true });

createInertiaApp({
    resolve: (name) => {
        const page = pages[`./Pages/${name}.tsx`];

        if (!page) {
            throw new Error(`Inertia page not found: ${name}`);
        }

        const resolved = page.default;
        if (name !== 'Auth/Login') {
            resolved.layout = resolved.layout ?? ((content: ReactNode) => <AuthenticatedLayout>{content}</AuthenticatedLayout>);
        }

        return resolved;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
