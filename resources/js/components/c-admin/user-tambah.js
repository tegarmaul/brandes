/**
 * ==========================================================================
 * user-tambah.js
 * Deskripsi: Mengelola logika modal Tambah User, termasuk fitur auto-polling
 *            data dari alat IoT (Fingerprint & PIN) secara real-time.
 * ==========================================================================
 */

// ==========================================================================
// 1. KONFIGURASI ICON (SVG)
// ==========================================================================

// Template SVG untuk ikon mata (Terbuka & Tertutup)
const EYE_OPEN_ICON  = `<path d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" /><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
const EYE_SLASH_ICON = `<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22" stroke-linecap="round" stroke-linejoin="round"/>`;


// ==========================================================================
// 2. KONTROL MODAL & POLLING
// ==========================================================================

// Variabel penampung interval agar bisa dihentikan saat modal tutup
let iotPollingInterval = null;

/**
 * Membuka modal Tambah User dan memulai proses polling IoT
 */
window.openTambahModalUser = function() {
    // Tutup modal lain yang sedang terbuka
    if (window.closeAllModals) window.closeAllModals();

    const modal = document.getElementById('userTambahModalOverlay');
    if (modal) {
        modal.classList.add('show');
        document.body.classList.add('no-scroll');
        
        // Jalankan polling segera dan ulangi tiap 3 detik
        window.checkLatestIotData();
        iotPollingInterval = setInterval(window.checkLatestIotData, 3000);
    }
};

/**
 * Menutup modal Tambah User dan menghentikan polling IoT
 */
window.closeTambahModalUser = function() {
    if (window.closeAllModals) window.closeAllModals();

    // Hentikan interval polling untuk menghemat resource
    if (iotPollingInterval) {
        clearInterval(iotPollingInterval);
        iotPollingInterval = null;
    }
};

/**
 * Menutup modal saat area background (overlay) diklik
 */
window.closeTambahModalUserOutside = function(e) {
    if (e.target.id === 'userTambahModalOverlay') {
        window.closeTambahModalUser();
    }
};


// ==========================================================================
// 3. INTEGRASI IOT (API)
// ==========================================================================

/**
 * Mengambil data registrasi terbaru (Fingerprint & PIN) dari alat IoT melalui API.
 * Data akan otomatis mengisi form jika field masih kosong.
 */
window.checkLatestIotData = function() {
    const inputFp  = document.querySelector('input[name="fingerprint_id"]');
    const inputPin = document.getElementById('userPinInput');

    fetch('/api/iot/latest-registration')
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                // Isi otomatis hanya jika user belum mengetikkan apapun (mencegah overwrite manual)
                if (inputFp  && !inputFp.value)  inputFp.value  = res.data.fingerprint_id;
                if (inputPin && !inputPin.value) inputPin.value = res.data.pin;
            }
        })
        .catch(err => console.error('Gagal mengambil data IoT:', err));
};


// ==========================================================================
// 4. UTILITAS FORM
// ==========================================================================

/**
 * Toggle visibilitas karakter pada input PIN (Password/Text)
 */
window.toggleUserPin = function() {
    const input = document.getElementById('userPinInput');
    const icon  = document.getElementById('userEyeIcon');
    if (!input || !icon) return;

    const isHidden = input.type === 'password';
    input.type     = isHidden ? 'text' : 'password';
    icon.innerHTML = isHidden ? EYE_SLASH_ICON : EYE_OPEN_ICON;
};