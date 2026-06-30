import { createInertiaApp } from '@inertiajs/vue3';
import axios from 'axios';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';
import { showToastError } from '@/lib/alert';
import '../css/app.css';

// Global Axios Request Interceptor for Auth Bearer Tokens
axios.interceptors.request.use((config) => {
    const url = config.url || '';
    const cleanUrl = url.toLowerCase();
    let token = null;

    if (cleanUrl.includes('api/superadmin')) {
        token = localStorage.getItem('superadmin_auth_token');
    } else if (cleanUrl.includes('api/nazhir')) {
        token = localStorage.getItem('nazhir_auth_token');
    } else if (cleanUrl.includes('api/wakif')) {
        token = localStorage.getItem('wakif_auth_token');
    }

    if (token && token !== 'undefined' && token !== 'null') {
        config.headers = config.headers || {};
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
}, (error) => {
    return Promise.reject(error);
});

// Global Axios Response Interceptor for showing Toast Errors
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        let message = 'Terjadi kesalahan sistem.';

        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.errors;

                if (errors && Object.keys(errors).length > 0) {
                    const firstKey = Object.keys(errors)[0];
                    message = errors[firstKey][0];
                } else {
                    message = error.response.data.message || 'Data yang dimasukkan tidak valid.';
                }
            } else if (error.response.data && error.response.data.message) {
                message = error.response.data.message;
            } else {
                message = `Error ${error.response.status}: ${error.response.statusText || 'Terjadi kesalahan'}`;
            }
        } else if (error.request) {
            message = 'Tidak dapat menghubungi server. Periksa koneksi internet Anda.';
        } else {
            message = error.message;
        }

        showToastError(message);

        return Promise.reject(error);
    }
);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
