import fs from 'node:fs';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// The mkcert pair nginx serves for pwa.test, mounted read-only into the
// container by docker-compose. The dev server has to speak https as well:
// the app is served over https, and http assets on an https page are mixed
// content the browser refuses to load.
const certDir = '/home/dev/certs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        // Bind all interfaces: vite runs in a container and the host browser reaches
        // it through the published port, which the default loopback bind refuses.
        host: '0.0.0.0',
        // 5173 belongs to the ai project, so this one is published on 5174.
        port: 5174,
        strictPort: true,
        https: {
            cert: fs.readFileSync(`${certDir}/pwa.test.pem`),
            key: fs.readFileSync(`${certDir}/pwa.test-key.pem`),
        },
        // The browser resolves HMR on the host, not inside the container. The
        // cert covers pwa.test, so use that name rather than localhost or the
        // wss:// handshake fails on a hostname mismatch.
        hmr: {
            host: 'pwa.test',
            protocol: 'wss',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
