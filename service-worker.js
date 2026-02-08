const CACHE_NAME = 'personnel-pwa-v1';

const CORE_ASSETS = [
  './',
  './index.php',
  './personnel_list.php',
  './personnel_form.php',
  './personnel_import.php',
  './offices_list.php',
  './office_form.php',
  './manifest.json',
  './icon-192.svg',
  './icon-512.svg'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(CORE_ASSETS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((key) => (key !== CACHE_NAME ? caches.delete(key) : Promise.resolve())))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const req = event.request;

  if (req.method !== 'GET') {
    return;
  }

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) {
    return;
  }

  event.respondWith(
    caches.match(req).then((cached) => {
      if (cached) return cached;

      return fetch(req)
        .then((res) => {
          const isOk = res && res.status === 200;
          const isHtml = (res.headers.get('content-type') || '').includes('text/html');

          if (isOk && (isHtml || url.pathname.endsWith('.php') || url.pathname.endsWith('.json') || url.pathname.endsWith('.svg'))) {
            const copy = res.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
          }

          return res;
        })
        .catch(() => cached || caches.match('./index.php'));
    })
  );
});
