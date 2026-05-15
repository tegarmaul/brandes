/**
 * dashboard-user.js
 * Logika interaksi untuk Dashboard User: Jam Real-time, Filter History, dan Status Brankas.
 */

// ==========================================================================
// 1. UTILITAS & GREETING
// ==========================================================================


// ==========================================================================
// 2. KOMPONEN REAL-TIME (Jam & Status Brankas)
// ==========================================================================

/**
 * Memperbarui tampilan jam dan tanggal real-time di Dashboard.
 */
function updateRealtimeClock() {
    const dateElement = document.getElementById("realtime-date");
    const clockElement = document.getElementById("realtime-clock");

    if (!dateElement || !clockElement) return;

    const now = new Date();

    // Format Tanggal: DD Bulan YYYY (contoh: 06 April 2026)
    const dateOptions = { day: "2-digit", month: "long", year: "numeric" };
    const dateStr = now.toLocaleDateString("id-ID", dateOptions);

    // Format Waktu: HH:mm:ss
    const timeStr = now.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false
    }).replace(/\./g, ":");

    dateElement.textContent = dateStr;
    clockElement.textContent = timeStr;
}

/**
 * Memperbarui tampilan kartu status Brankas (Terkunci/Terbuka/Offline).
 * @param {boolean} isOnline - true jika terhubung ke IoT.
 * @param {string} statusPintu - 'TERKUNCI' atau 'TERBUKA'.
 */
function updateBrankasStatus(isOnline, statusPintu) {
    const card = document.getElementById('brankas-card');
    const label = document.getElementById('status-label');
    const liveText = document.querySelector('.live-text');
    
    if (!card || !label) return;

    const iconLocked = card.querySelector('.icon-locked');
    const iconUnlocked = card.querySelector('.icon-unlocked');
    const iconOffline = card.querySelector('.icon-offline');

    // 1. Tangani Kondisi Offline
    if (!isOnline) {
        card.classList.add('is-offline');
        card.classList.remove('is-open');
        label.innerText = 'TIDAK TERHUBUNG';
        if (liveText) liveText.innerText = 'OFFLINE';
        
        if (iconLocked) iconLocked.style.display = 'none';
        if (iconUnlocked) iconUnlocked.style.display = 'none';
        if (iconOffline) iconOffline.style.display = 'block';
        return;
    }

    // 2. Tangani Kondisi Online
    card.classList.remove('is-offline');
    if (liveText) liveText.innerText = 'LIVE';
    if (iconOffline) iconOffline.style.display = 'none';

    if (statusPintu === 'TERBUKA') {
        card.classList.add('is-open');
        label.innerText = 'TERBUKA';
        if (iconLocked) iconLocked.style.display = 'none';
        if (iconUnlocked) iconUnlocked.style.display = 'block';
    } else {
        card.classList.remove('is-open');
        label.innerText = 'TERKUNCI';
        if (iconLocked) iconLocked.style.display = 'block';
        if (iconUnlocked) iconUnlocked.style.display = 'none';
    }
}

/**
 * Fungsi polling untuk mengambil status terbaru dari database.
 */
function checkRealtimeStatus() {
    fetch('/api/brankas/status-realtime')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBrankasStatus(data.is_online, data.status_pintu);
            }
        })
        .catch(err => console.error('Polling error:', err));
}

// ==========================================================================
// 3. INISIALISASI & GREETING (Salam)
// ==========================================================================

// Menjalankan pembaruan jam setiap detik
setInterval(updateRealtimeClock, 1000);

// Jalankan polling status setiap 3 detik
setInterval(checkRealtimeStatus, 3000);

// EKSPOS KE WINDOW (Agar bisa dipanggil dari atribut onclick/oninput di HTML)
window.checkRealtimeStatus = checkRealtimeStatus;
window.updateBrankasStatus = updateBrankasStatus;
window.updateRealtimeClock = updateRealtimeClock;

document.addEventListener("DOMContentLoaded", () => {
    updateRealtimeClock();

    // Mengatur ucapan salam berdasarkan waktu saat ini
    const hour = new Date().getHours();
    let greeting = 'Selamat Datang';

    if (hour >= 5 && hour < 12) {
        greeting = 'Selamat Pagi';
    } else if (hour >= 12 && hour < 15) {
        greeting = 'Selamat Siang';
    } else if (hour >= 15 && hour < 18) {
        greeting = 'Selamat Sore';
    } else {
        greeting = 'Selamat Malam';
    }

    // Menerapkan salam ke elemen Topbar
    const subtitle = document.querySelector('.topbar-left p');
    if (subtitle) {
        const currentText = subtitle.textContent;
        // Cek placeholder standar
        if (currentText.includes('Selamat datang')) {
            subtitle.textContent = currentText.replace('Selamat datang', greeting);
        }
    }
});
