/* Laundrix service worker — app shell caching, offline support, push notifications. */

const VERSION = 'laundrix-v1';
const SHELL = ['/', '/about', '/services', '/contact', '/manifest.json'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(VERSION).then((cache) => cache.addAll(SHELL)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== VERSION).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Never cache non-GET, Livewire, broadcasting or API traffic.
    if (request.method !== 'GET') return;
    const url = new URL(request.url);
    if (url.pathname.startsWith('/livewire') || url.pathname.startsWith('/broadcasting') || url.pathname.startsWith('/api')) return;

    // Network-first for pages so realtime content stays fresh; cache fallback offline.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open(VERSION).then((cache) => cache.put(request, copy));
                    return response;
                })
                .catch(() => caches.match(request).then((hit) => hit || caches.match('/')))
        );
        return;
    }

    // Cache-first for static assets.
    event.respondWith(
        caches.match(request).then(
            (hit) =>
                hit ||
                fetch(request).then((response) => {
                    if (response.ok && url.origin === self.location.origin) {
                        const copy = response.clone();
                        caches.open(VERSION).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
        )
    );
});

/* Push notifications (Firebase Cloud Messaging delivers via the Push API). */
self.addEventListener('push', (event) => {
    let data = {};
    try { data = event.data ? event.data.json() : {}; } catch (e) { data = { body: event.data?.text() }; }
    const n = data.notification || data;

    event.waitUntil(
        self.registration.showNotification(n.title || 'Laundrix', {
            body: n.body || 'You have a new update.',
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: { url: n.click_action || n.url || '/' },
            vibrate: [80, 40, 80],
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((wins) => {
            const open = wins.find((w) => w.url.includes(self.location.origin));
            return open ? open.focus().then(() => open.navigate(url)) : clients.openWindow(url);
        })
    );
});
