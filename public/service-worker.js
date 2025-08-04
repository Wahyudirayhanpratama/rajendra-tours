const CACHE_NAME = "pwa-cache-v3";
const FILES_TO_CACHE = [
    "/",
    "/cari-jadwal",
    "/css/caleran.min.css",
    "/css/style_pwa.css",
    "/css/color_palette.css",
    "/css/custom_tam_fixed.css",
    "/css/custom_tam.css",
    "/css/custom.css",
    "/js/moment.min.js",
    "/js/caleran.min.js",
    "/js/base.js",
    "/storage/logo_kecil_rajendra.png",
    "/storage/logo_rajendra.png",
    "/storage/unit_rajendra.jpg",
    "/storage/no_connection.jpg",
    "/icons/icon-512x512.png",
    "/icons/icon-192x192.png",
    "/manifest.json",
    "/offline.html"
];

// Saat install: cache semua file penting
self.addEventListener("install", (event) => {
    console.log("Service Worker installed");
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.all(
                FILES_TO_CACHE.map((file) =>
                    cache.add(file)
                        .then(() => console.log("✅ Cached:", file))
                        .catch((error) => console.warn("❌ Failed to cache:", file, error))
                )
            );
        })
    );
    self.skipWaiting();
});

// Saat activate: hapus cache lama
self.addEventListener("activate", (event) => {
    console.log("Service Worker activated");
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        console.log("🗑️ Deleting old cache:", name);
                        return caches.delete(name);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// Saat fetch: gunakan cache dulu, jika tidak ada ambil dari network lalu cache-kan
self.addEventListener("fetch", (event) => {
    const request = event.request;

    // Jika permintaan HTML (misalnya halaman)
    if (request.headers.get("accept")?.includes("text/html")) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    return cachedResponse || caches.match('/offline.html');
                })
        );
        return;
    }

    // Untuk asset statis: ambil dari cache dulu, lalu network jika tidak ada
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            return cachedResponse || fetch(request).then((response) => {
                if (!response || response.status !== 200 || response.type === 'opaque') {
                    return response;
                }
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(request, responseClone);
                }).catch(err => {
                    console.warn("❌ Gagal menyimpan ke cache:", err);
                });
                return response;
            }).catch(() => {
                // Fallback untuk gambar jika offline
                if (request.destination === 'image') {
                    return caches.match('/storage/no_connection.jpg');
                }
            });
        })
    );
});
