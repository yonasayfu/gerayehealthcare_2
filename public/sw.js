/*
  Simple cache-first service worker for Vite build assets.
  Caches requests under /build/ so the app can reload offline once assets are cached.
*/

const CACHE_NAME = 'ghc-static-v1';
const BUILD_PREFIX = '/build/';

self.addEventListener('install', (event) => {
  // Activate immediately after installation
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  // Clean up old caches
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return; // only cache GETs

  const url = new URL(req.url);
  const isSameOrigin = url.origin === self.location.origin;
  const isBuildAsset = isSameOrigin && url.pathname.startsWith(BUILD_PREFIX);

  if (isBuildAsset) {
    event.respondWith(cacheFirst(req));
  }
});

async function cacheFirst(request) {
  const cache = await caches.open(CACHE_NAME);
  const cached = await cache.match(request, { ignoreSearch: true });
  if (cached) return cached;

  const response = await fetch(request);
  if (response && response.status === 200) {
    cache.put(request, response.clone());
  }
  return response;
}

