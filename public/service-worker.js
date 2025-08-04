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

// Simpan file ke cache
self.addEventListener("install", (event) => {
    console.log("Service Worker installed");
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.all(
                FILES_TO_CACHE.map((file) => {
                    return cache.add(file)
                        .then(() => console.log("✅ Cached:", file))
                        .catch((error) => console.warn("❌ Failed to cache:", file, error));
                })
            );
        }).catch((err) => {
            console.error("🔥 Error opening cache:", err);
        })
    );
    self.skipWaiting();
});

// Hapus cache lama jika ada
self.addEventListener("activate", (event) => {
    console.log("Service Worker activated");
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        console.log("Deleting old cache:", name);
                        return caches.delete(name);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// Gunakan cache jika ada, kalau tidak ambil dari server
self.addEventListener("fetch", (event) => {
    const request = event.request;

    // Jika permintaan HTML (misalnya /tiket)
    if (request.headers.get("accept")?.includes("text/html")) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    return response;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    return cachedResponse || caches.match('/offline.html');
                })
        );
        return;
    }

    // Untuk asset statis seperti CSS, JS, gambar
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            return cachedResponse || fetch(request);
        })
    );
});

