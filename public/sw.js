/**
 * Nabha LMS — Service Worker
 * Strategy:
 *   - CDN / same-origin static assets  → Cache First
 *   - HTML navigation requests          → Network First  (fallback: cached page or offline.html)
 *   - POST / non-GET                    → pass-through (never cached)
 */

const CACHE_VERSION = 'nabha-lms-v1';

// Assets pre-cached at install time
const PRECACHE_URLS = [
    '/',
    '/manifest.json',
    '/offline.html',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    // CDN assets (CORS-enabled)
    'https://cdn.tailwindcss.com',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
    'https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js',
];

// ─── Install ─────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then(cache =>
            // allSettled: one CDN failure won't abort the whole install
            Promise.allSettled(PRECACHE_URLS.map(url => cache.add(url)))
        ).then(() => self.skipWaiting())
    );
});

// ─── Activate ────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(k => k !== CACHE_VERSION).map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ─── Fetch ───────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;

    // Never intercept non-GET or browser-extension requests
    if (request.method !== 'GET') return;
    if (new URL(request.url).protocol === 'chrome-extension:') return;

    const isNavigation = request.mode === 'navigate' ||
        request.headers.get('Accept')?.includes('text/html');

    if (isNavigation) {
        event.respondWith(networkFirst(request));
    } else {
        event.respondWith(cacheFirst(request));
    }
});

// ─── Strategies ──────────────────────────────────────────────────────────────

/**
 * Network First — tries network, falls back to cache, then offline.html.
 * Used for HTML pages so users always get fresh content when online.
 */
async function networkFirst(request) {
    const cache = await caches.open(CACHE_VERSION);
    try {
        const response = await fetch(request);
        // Only cache successful same-origin responses
        if (response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await cache.match(request);
        if (cached) return cached;

        // Last resort: serve the offline page
        const offline = await cache.match('/offline.html');
        return offline || new Response(
            '<!DOCTYPE html><html><head><title>Offline</title></head>' +
            '<body style="font-family:sans-serif;text-align:center;padding:3rem">' +
            '<h1>You are offline</h1>' +
            '<p>Please check your connection and try again.</p>' +
            '<a href="/" style="color:#4f46e5">Retry</a>' +
            '</body></html>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

/**
 * Cache First — returns cached asset immediately, fetches & stores if missing.
 * Used for static assets (CSS, JS, images, fonts) where staleness is fine.
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok || response.type === 'opaque') {
            const cache = await caches.open(CACHE_VERSION);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        // Return empty 503 for non-navigational assets
        return new Response('', { status: 503, statusText: 'Offline' });
    }
}
