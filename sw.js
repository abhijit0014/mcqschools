const cacheName = 'pwa-conf-v1';
const filesToCache = [
    '/template/resource/app.css',
    '/template/resource/jquery.min.js',
    '/template/resource/bootstrap.min.css',
    '/template/resource/bootstrap.bundle.min.js',
    '/template/resource/js.cookie.min.js',
    '/template/resource/highlight.css',
    '/template/resource/highlight.min.js'
];

/* Start the service worker and cache all of the app's content */
self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open(cacheName).then(function(cache) {
      return cache.addAll(filesToCache);
    })
  );
});

/* Serve cached content when offline */
self.addEventListener('fetch', function (e) {

    // This fixes a weird bug in Chrome when you open the Developer Tools
    if (e.request.cache === 'only-if-cached' && e.request.mode !== 'same-origin') {
        return;
    }
    e.respondWith(
        caches.match(e.request).then(function (response) {
            return response || fetch(e.request);
        })
    );
});