import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import fs from 'fs';

// Check if SSL certificate exists
const sslKeyPath = './docker/ssl/bkk-esemkasari.key';
const sslCertPath = './docker/ssl/bkk-esemkasari.crt';
const hasSSL = fs.existsSync(sslKeyPath) && fs.existsSync(sslCertPath);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        // Use HTTPS if certificate exists, otherwise fallback to HTTP
        ...(hasSSL && {
            https: {
                key: fs.readFileSync(sslKeyPath),
                cert: fs.readFileSync(sslCertPath),
            },
            hmr: {
                host: 'bkk-esemkasari.dev',
                protocol: 'wss',
            },
        }),
        // Fallback to localhost if no SSL
        ...(!hasSSL && {
            hmr: {
                host: 'localhost',
            },
        }),
        cors: true,
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
});
