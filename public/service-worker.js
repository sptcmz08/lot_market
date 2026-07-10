const CACHE_NAME = 'tent-market-v1';
const APP_SHELL = [
    '/',
    '/manifest.json',
    '/images/tent.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys
                .filter((key) => key !== CACHE_NAME)
                .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const requestUrl = new URL(event.request.url);
    const cacheableAsset = requestUrl.origin === self.location.origin
        && (
            requestUrl.pathname === '/manifest.json'
            || requestUrl.pathname.startsWith('/images/')
            || ['style', 'script', 'image', 'font', 'manifest'].includes(event.request.destination)
        );

    if (!cacheableAsset) {
        event.respondWith(fetch(event.request));
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const copy = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});
