const SITE_PREFIX = 'ci-';
const CACHE_NAME_STATIC = SITE_PREFIX + 'static-cache-v1';
const CACHE_NAME_IMAGES = SITE_PREFIX + 'images-cache-v1';
const ALL_CACHES = [CACHE_NAME_STATIC, CACHE_NAME_IMAGES];
const IMAGE_FOLDER = '/images/';

self.addEventListener('install', function (event) {
    event.waitUntil(caches.open(CACHE_NAME_STATIC).then(function (cache) {
        return cache.addAll([
            '/'
        ]);
    }));
});

self.addEventListener('activate', function (event) {
    event.waitUntil(caches.keys().then(function (cacheNames) {
        let filtered = cacheNames.filter(function (cacheName) {
            return cacheName.startsWith(SITE_PREFIX) && !ALL_CACHES.includes(cacheName);
        });
        let cleared = filtered.map(function (cacheName) {
            return caches.delete(cacheName);
        });
        Promise.all(cleared);
    }));
});

//Only caches when same origin (type = basic) and where status was success (200)
self.addEventListener('fetch', function (event) {
    event.respondWith(caches.match(event.request).then(function (response) {
        if (response) {
            return response;
        }

        return fetch(event.request.clone()).then(function (response) {
            if (!response || response.status !== 200 || response.type !== 'basic') {
                return response;
            }

            caches.open(CACHE_NAME_IMAGES).then(function (cache) {
                cache.put(event.request, response.clone());
            });

            let requestUrl = new URL(event.request.url);
            if (requestUrl.pathname.startsWith(IMAGE_FOLDER)) {
                event.respondWith(serveImage(event.request));
                return;
            }

            return response;
        });
    }));
});

function serveImage(request) {
    let storageUrl = request.url.replace(/-\d+px\.jpg$/, '');

    return caches.open(CACHE_NAME_IMAGES).then(function (cache) {
        return cache.match(storageUrl).then(function (response) {
            if (response) {
                return response;
            }
            return fetch(request).then(function (networkResponse) {
                cache.put(storageUrl, networkResponse.clone());
                return networkResponse;
            });
        });
    });
}