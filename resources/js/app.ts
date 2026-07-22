import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import type { App } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title: string) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    withApp: (app: App) => {
        app.use(createPinia());
    },
});
