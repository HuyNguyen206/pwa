const PRECACHE_ENDPOINT = '/precache-manifest.json';
const CACHE_PREFIX = 'app-shell-';
const META_CACHE = 'meta-precache';
const PAGE_CACHE = 'PAGE_CACHE';
const IMAGE_PLACEHOLDER = '/images/placeholder.svg';

async function cacheFirst({request, shellName}) {
    const cache = await caches.open(shellName);
    const cached = await cache.match(request);

    if (cached) return cached;

    try {
        const res = await fetch(request);

        if ((res.ok && (res.type === 'basic' || res.type === 'cors'))
            || request.url.endsWith('app.css')) {
            cache.put(request, res.clone()).catch(() => {});
        }

        return res;
    } catch {
        return new Response('Offline', {status: 503});
    }
}

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

    // A 304 carries no body, so res.json() below would throw. Only reachable
    // when the response bypasses the HTTP cache; fall back to the stored copy.
    if (res.status === 304) {
        const meta = await caches.open(META_CACHE);
        const cached = await meta.match(PRECACHE_ENDPOINT);

        if (cached) return cached.json();

        throw new Error('Precache manifest not modified but no copy cached');
    }

    if (!res.ok) throw new Error(`Precache manifest returned ${res.status}`);

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
        path.startsWith('/build/') ||
        path.endsWith('.css') ||
        path.endsWith('.js') ||
        path.endsWith('.png') ||
        path.endsWith('.svg') ||
        path.endsWith('.jpg')
    );
}

async function staleWithRevalidate({request, shellName}) {
    const cache = await caches.open(shellName);
    const cached = await cache.match(request);

    if (request.destination === 'image') {
        try {
            return await fetch(request);
        } catch {
            return await cache.match(IMAGE_PLACEHOLDER)
                ?? new Response('Offline', {status: 503});
        }
    }


    const response = await fetch(request)
        .then((res) => {
            if ((res.ok && (res.type === 'basic' || res.type === 'cors'))
                || request.url.endsWith('app.css')) {
                cache.put(request, res.clone()).catch(() => {});
            }
            return res;
        })
        .catch(() => null);

    return cached || response || new Response('Offline', {status: 503});
}

async function precache({cache, urls}) {
    const failures = [];

    // One cache.add() per URL rather than a single cache.addAll(): addAll is
    // atomic, so one unreachable entry rejects the whole batch and the install
    // ends up caching nothing at all. A partial shell still beats an empty one,
    // and every fetch handler already falls back to the network.
    await Promise.all(urls.map(async url => {
        try {
            await cache.add(url);
        } catch (err) {
            failures.push(url);
            console.warn('[sw] precache failed:', url, err);
        }
    }));

    console.info(`[sw] precached ${urls.length - failures.length}/${urls.length} urls`);

    return failures;
}

self.addEventListener('install', e => {
    e.waitUntil((async () => {
        const {version, urls} = await getCurrentVersionAndUrls();
        const cache = await caches.open(`${CACHE_PREFIX}${version}`);

        await precache({cache, urls});
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
            const cache = await caches.open(PAGE_CACHE);
            try {
                const res = await fetch(request);

                if (res.ok && request.url.startsWith(self.location.origin)) {
                    cache.put(request, res.clone()).catch(() => {});
                }

                return res;
            } catch {
                const cachedPage = await cache.match(request);
                if (cachedPage) return cachedPage;

                return (await caches.match('/offline')) ??
                    new Response('Offline', {status: 503})
            }
        })());
        return;
    }

    if (request.url.startsWith(self.location.origin) && isStaticAsset(url)) {
        e.respondWith((async () => {
            const shellName = await getNewestShellName();

            if (!shellName) {
                try {
                    return await fetch(request);
                } catch {
                    return new Response('Offline', {status: 503});
                }
            }

            return await staleWithRevalidate({request, shellName});
            // return await cacheFirst({request, shellName});
        })());
    }
});
