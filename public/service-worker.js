self.addEventListener("install", function (event) {
    console.log("Service Worker installed");
    self.skipWaiting();
});

self.addEventListener("fetch", function (event) {
    // Caching strategies bisa ditambahkan di sini
});

self.addEventListener("activate", function (event) {
    console.log("Service Worker activated");
});
