const PRECACHE_ENDPOINT = '/precache-manifest.json';
const CACHE_PREFIX = 'app-shell-';
const META_CACHE = 'meta-precache';

async function deleteOldShellCaches(keepName) {
    const keys = await caches.keys();

    await Promise.all(keys.map(k => {
        const isShell = k.startsWith(CACHE_PREFIX);
        const isOld = keepName ? k !== keepName : true;

        if (isShell && isOld) return caches.delete(k);
    }));
}

async function getCurrentVersionAndUrls() {
    const res = await fetch(PRECACHE_ENDPOINT).catch(() => null);

    if (!res) {
        const meta = await caches.open(META_CACHE);
        const cached = await meta.match(PRECACHE_ENDPOINT);
        if (cached) return cached.json();
        throw new Error('No precache manifest available');
    }

    if (res.status === 304) {
        const meta = await caches.open(META_CACHE);
        const cached = await meta.match(PRECACHE_ENDPOINT);
        if (cached) return cached.json();
    }

    const clone = res.clone();
    const json = await res.json();

    const meta = await caches.open(META_CACHE);
    await meta.put(PRECACHE_ENDPOINT, clone);

    return json;
}

async function getNewestShellName() {
    const names = (await caches.keys())
        .filter(k => k.startsWith(CACHE_PREFIX))
        .sort();

    return names.length ? names[names.length - 1] : null;
}

function isStaticAsset(url) {
    const path = url.pathname;

    return (
        path.startsWith('/build/'),
            path.endsWith('.css'),
            path.endsWith('.js'),
            path.endsWith('.png'),
            path.endsWith('.svg'),
            path.endsWith('.jpg')
    );
}

self.addEventListener('install', e => {
    e.waitUntil((async () => {
        const {version, urls} = await getCurrentVersionAndUrls();
        const cache = await caches.open(`${CACHE_PREFIX}${version}`);
        await cache.addAll(urls);
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', e => {
    e.waitUntil((async () => {
        let keepName = null;

        try {
            const {version} = await getCurrentVersionAndUrls();
            keepName = `${CACHE_PREFIX}${version}`;
        } catch {
            keepName = await getNewestShellName();
        }

        await deleteOldShellCaches(keepName);
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', e => {
    const {request} = e;
    const url = new URL(request.url);

    if (request.mode === 'navigate') {
        e.respondWith((async () => {
            try {
                return await fetch(request);
            } catch {
                return (await caches.match('/offline')) ??
                    new Response('Offline', {status: 503})
            }
        })());
        return;
    }

    if (url.origin === self.location.origin && isStaticAsset(url)) {
        e.respondWith((async () => {
            const shellName = await getNewestShellName();

            if (!shellName) {
                try {
                    return await fetch(request);
                } catch {
                    return new Response('Offline', {status: 503});
                }
            }

            const cache = await caches.open(shellName);
            const cached = await cache.match(request);

            const response = await fetch(request)
                .then((res) => {
                    if (res.ok && (res.type === 'basic' || res.type === 'cors')) {
                        cache.put(request, res.clone()).catch(() => {});
                    }
                    return res;
                })
                .catch(() => null);

            return cached || response || new Response('Offline', {status: 503});
        })());
    }
});
