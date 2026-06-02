// SobatSaldo Service Worker v1.0
// Strategi: Cache First untuk aset statis, Network First untuk API

const CACHE_NAME = 'sobatsaldo-v1';
const OFFLINE_DB = 'sobatsaldo-offline';
const OFFLINE_STORE = 'pending-transactions';

const STATIC_ASSETS = [];

// ── Install: Cache aset statis ──────────────────────────────────────────────
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// ── Activate: Hapus cache lama ──────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                          .map(name => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// ── Fetch: Network First dengan fallback cache ──────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip non-GET dan request browser extensions
    if (request.method !== 'GET') return;
    if (!request.url.startsWith('http')) return;

    // API calls (transaksi) → Network Only dengan offline queue
    if (request.url.includes('/transactions')) return;

    event.respondWith(
        fetch(request)
            .then((response) => {
                // Cache response yang berhasil
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => {
                // Fallback ke cache
                return caches.match(request, { ignoreSearch: true }).then(cached => {
                    if (cached) return cached;
                    
                    // Jika ini request HTML/Navigasi dan tidak ada di cache, arahkan ke workspace (jika ada) atau tampilkan offline page
                    if (request.headers.get('accept').includes('text/html')) {
                        return caches.match('/workspace', { ignoreSearch: true }).then(wsCached => {
                            return wsCached || new Response(
                                '<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Offline - SobatSaldo</title><style>body{font-family:sans-serif;background:#059669;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;margin:0;text-align:center;}h1{font-size:24px;margin-bottom:10px;}p{font-size:14px;opacity:0.9;}</style></head><body><h1>SobatSaldo Offline 📡</h1><p>Koneksi internetmu terputus.<br>Silakan nyalakan internet untuk menggunakan aplikasi.</p></body></html>',
                                { headers: { 'Content-Type': 'text/html' } }
                            );
                        });
                    }
                });
            })
    );
});

// ── Background Sync: Kirim transaksi yang tertunda ──────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-transactions') {
        event.waitUntil(syncPendingTransactions());
    }
});

async function syncPendingTransactions() {
    try {
        const db = await openOfflineDB();
        const tx = db.transaction(OFFLINE_STORE, 'readwrite');
        const store = tx.objectStore(OFFLINE_STORE);
        const all = await getAllRecords(store);

        for (const item of all) {
            try {
                const response = await fetch('/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': item.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ raw_text: item.raw_text, wallet_id: item.wallet_id }),
                });

                if (response.ok) {
                    // Hapus dari antrian setelah berhasil
                    const delTx = db.transaction(OFFLINE_STORE, 'readwrite');
                    delTx.objectStore(OFFLINE_STORE).delete(item.id);
                }
            } catch (e) {
                // Biarkan di antrian, coba lagi nanti
            }
        }
    } catch (e) {
        console.error('[SW] Sync gagal:', e);
    }
}

function openOfflineDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(OFFLINE_DB, 1);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(OFFLINE_STORE)) {
                db.createObjectStore(OFFLINE_STORE, { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror = reject;
    });
}

function getAllRecords(store) {
    return new Promise((resolve, reject) => {
        const req = store.getAll();
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror = reject;
    });
}
