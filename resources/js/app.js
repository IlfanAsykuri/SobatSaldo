import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Swal from 'sweetalert2';

// ── Alpine Plugins ──────────────────────────────────────────────────────────
Alpine.plugin(focus);

// ── SweetAlert2 — Custom Theme SobatSaldo ───────────────────────────────────
const SobatSwal = Swal.mixin({
    customClass: {
        popup:          'swal-sobat-popup',
        title:          'swal-sobat-title',
        htmlContainer:  'swal-sobat-html',
        confirmButton:  'swal-sobat-confirm',
        cancelButton:   'swal-sobat-cancel',
        icon:           'swal-sobat-icon',
    },
    buttonsStyling: false,
    showCloseButton: false,
    backdrop: 'rgba(15,23,42,0.55)',
});

// Ekspor ke window agar bisa dipakai di Blade templates
window.SobatSwal = SobatSwal;

// ── Helper: Konfirmasi Hapus ─────────────────────────────────────────────────
window.confirmDelete = async function(message = 'Data ini akan dihapus permanen.') {
    const result = await SobatSwal.fire({
        title:              'Yakin ingin menghapus?',
        html:               `<p class="text-sm">${message}</p>`,
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonText:  '🗑️ Ya, Hapus',
        cancelButtonText:   'Batal',
        reverseButtons:     true,
        focusCancel:        true,
    });
    return result.isConfirmed;
};

// ── Helper: Alert Sukses ─────────────────────────────────────────────────────
window.alertSuccess = function(message, title = 'Berhasil!') {
    return SobatSwal.fire({
        title,
        html:               `<p class="text-sm">${message}</p>`,
        icon:               'success',
        confirmButtonText:  'OK',
        timer:              3000,
        timerProgressBar:   true,
    });
};

// ── Helper: Alert Error ──────────────────────────────────────────────────────
window.alertError = function(message, title = 'Terjadi Kesalahan') {
    return SobatSwal.fire({
        title,
        html:               `<p class="text-sm">${message}</p>`,
        icon:               'error',
        confirmButtonText:  'OK',
    });
};

// ── Helper: Alert Info ───────────────────────────────────────────────────────
window.alertInfo = function(message, title = 'Info') {
    return SobatSwal.fire({
        title,
        html:               `<p class="text-sm">${message}</p>`,
        icon:               'info',
        confirmButtonText:  'OK',
    });
};

// ── Alpine Global Store ─────────────────────────────────────────────────────
Alpine.store('ui', {
    openModal: null,
    openSheet: null,

    modal(id) {
        this.openModal = id;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.openModal = null;
        document.body.style.overflow = '';
    },
    sheet(id) {
        this.openSheet = id;
        document.body.style.overflow = 'hidden';
    },
    closeSheet() {
        this.openSheet = null;
        document.body.style.overflow = '';
    },
    reset() {
        this.openModal = null;
        this.openSheet = null;
        document.body.style.overflow = '';
    }
});

// ── Keyboard ESC: Tutup semua overlay ───────────────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        Alpine.store('ui').reset();
    }
});

// ── PWA: Simpan transaksi offline ke IndexedDB ───────────────────────────────
window.saveOfflineTransaction = async function(rawText, walletId, csrfToken) {
    try {
        const db = await openOfflineDB();
        const tx = db.transaction('pending-transactions', 'readwrite');
        tx.objectStore('pending-transactions').add({
            raw_text:  rawText,
            wallet_id: walletId,
            csrf:      csrfToken,
            timestamp: new Date().toISOString(),
        });
        return true;
    } catch (e) {
        console.error('Gagal menyimpan offline:', e);
        return false;
    }
};

function openOfflineDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('sobatsaldo-offline', 1);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('pending-transactions')) {
                db.createObjectStore('pending-transactions', { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = reject;
    });
}

// ── Initialize Alpine ────────────────────────────────────────────────────────
window.Alpine = Alpine;
Alpine.start();

// ── PWA Service Worker Registration ─────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then(reg => {
            console.log('[SW] Registered successfully.', reg.scope);
        }).catch(err => {
            console.error('[SW] Registration failed:', err);
        });
    });
}
