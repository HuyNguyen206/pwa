const CACHE_NAME = 'fieldlogger-v1';
const PRECACHE_URLS = [
    '/',
    '/offline'
];

self.addEventListener('install', e => {

    self.skipWaiting();

    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE_URLS))
    );
});

self.addEventListener('activate', e => {
    e.waitUntil((async () => {
        const names = await caches.keys();

        await Promise.all(
            names.filter(n => n !== CACHE_NAME).map(n => caches.delete(n))
        );

        await self.clients.claim();
    })());
});

self.addEventListener('fetch', e => {
    const {request} = e;
    if (request.method !== 'GET') return;

    e.respondWith((async () => {
        try {
            const response = await fetch(request);
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());

            return response;
        } catch {
            const cached = await caches.match(request);
            if (cached) {
                return cached;
            }

            if (request.mode === 'navigate') {
                return caches.match('/offline');
            }


            return new Response('Offline', {status: 503});
        }
    })());
});
