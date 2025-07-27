const CACHE_NAME = "pwa-cache-v1";
const FILES_TO_CACHE = [
    "/",                          // halaman utama
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
    "/icons/icon-512x512.png",
    "/icons/icon-192x192.png",
    "/manifest.json"
];

// Install: simpan file ke cache
self.addEventListener("install", (event) => {
    console.log("Service Worker installed");
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(FILES_TO_CACHE))
    );
    self.skipWaiting();
});

// Activate: hapus cache lama jika ada
self.addEventListener("activate", (event) => {
    console.log("Service Worker activated");
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// Fetch: gunakan cache jika ada, kalau tidak ambil dari server
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
