/**
 * ==========================================================================
 * dashboard-admin.js
 * Deskripsi: Mengelola logika dashboard panel admin, termasuk pemantauan 
 *            real-time status brankas, GPS, jam, dan filter data.
 * ==========================================================================
 */

// ==========================================================================
// 1. UTILITAS FORM & FILTER
// ==========================================================================

/**
 * Toggle status visual sederhana
 */
function toggleStatus() {
    const toggle = document.getElementById('statusToggle');
    if (toggle) toggle.classList.toggle('off');
}

/**
 * Membuka date picker browser untuk pencarian History
 */
function openDatePicker() {
    const input = document.getElementById('historyDate');
    if (input) {
        input.showPicker ? input.showPicker() : input.click();
    }
}

/**
 * Membuka date picker browser untuk pencarian Notifikasi
 */
function openNotifDatePicker() {
    const input = document.getElementById('notifDate');
    if (input) {
        input.showPicker ? input.showPicker() : input.click();
    }
}

/**
 * Memfilter daftar Riwayat Akses berdasarkan kata kunci dan tanggal
 */
function filterHistory() {
    const keywordInput = document.getElementById('historySearch');
    const dateInput    = document.getElementById('historyDateFilter');
    const label        = document.getElementById('dateLabel');

    if (!keywordInput || !dateInput) return;

    const keyword = keywordInput.value.toLowerCase().trim();
    const date    = dateInput.value;

    // 1. Update visual label untuk Date Picker
    if (date) {
        const d = new Date(date + 'T00:00:00');
        label.textContent = d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }).replace(/\//g, ' / ');
        label.style.color = 'var(--C-Black, #101828)';
    } else {
        label.textContent = 'mm / dd / yyyy';
        label.style.color = 'var(--C-Black-Second, #6A7282)';
    }

    // 2. Filter Baris Data
    const items = document.querySelectorAll('#historyList .history-item');
    let visibleCount = 0;

    items.forEach(item => {
        const name     = item.dataset.name || '';
        const itemDate = item.dataset.date || '';
        const matchName = keyword === '' || name.includes(keyword);
        const matchDate = date === '' || itemDate === date;

        if (matchName && matchDate) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // 3. Manajemen Status Visual (Banner & Empty States)
    const isSearching = keyword !== '' || date !== '';
    const isEmpty     = visibleCount === 0;

    // --- Banner Tanggal ---
    const dateGroup = document.getElementById('historyDateGroup');
    const dateText  = document.getElementById('historyDateText');
    if (dateGroup && dateText) {
        if (date !== '') {
            const d = new Date(date + 'T00:00:00');
            const formatted = d.toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            });
            dateText.textContent = `History Akses Pada Tanggal : ${formatted}`;
            dateGroup.classList.add('active');
        } else {
            dateText.textContent = `-`;
            dateGroup.classList.remove('active');
        }
    }

    // --- Label Tanggal Group (Today, dll) ---
    const historyLabelDate = document.querySelector('#historyList .history-label-date');
    if (historyLabelDate) {
        historyLabelDate.style.display = (isEmpty || date !== '') ? 'none' : '';
    }

    // --- Empty States ---
    const emptyState = document.getElementById('historyEmpty');
    const noDataState = document.getElementById('historyNoData');

    if (emptyState) {
        emptyState.style.display = (isEmpty && isSearching) ? 'flex' : 'none';
    }
    if (noDataState) {
        noDataState.style.display = (isEmpty && !isSearching) ? 'flex' : 'none';
    }
}

// State Filter Global
window.notifCurrentTab = 'semua';

/**
 * Menangani perubahan tanggal pada filter notifikasi (Date Picker)
 */
function filterNotifByDate() {
    const input = document.getElementById('notifDateFilter');
    const label = document.getElementById('notifDateLabel');
    if (!input || !label) return;

    const date = input.value;

    // Perbarui label tanggal visual (mm/dd/yyyy)
    if (date) {
        const d = new Date(date + 'T00:00:00');
        label.textContent = d.toLocaleDateString('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric'
        }).replace(/\//g, ' / ');
        label.style.color = 'var(--C-Black, #101828)';
    } else {
        label.textContent = 'mm / dd / yyyy';
        label.style.color = 'var(--C-Black-Second, #6A7282)';
    }

    filterNotifications();
}

/**
 * Memfilter daftar Notifikasi berdasarkan kata kunci dan tanggal
 */
function filterNotifications() {
    const keyword = document.getElementById('notifSearch')?.value.toLowerCase().trim() || '';
    const date    = document.getElementById('notifDateFilter')?.value || '';
    
    applyNotifFilter(keyword, date);
}

/**
 * Menerapkan logika filter ke kartu notifikasi (Tab, Search, & Date)
 */
function applyNotifFilter(keyword, date) {
    const items = document.querySelectorAll('#notifList .notif-item');
    let visibleCount = 0;

    items.forEach(item => {
        const text     = item.textContent.toLowerCase();
        const cardType = item.getAttribute('data-tipe') || '';
        
        // Cek kecocokan Kategori (Tab)
        const matchTab     = window.notifCurrentTab === 'semua' || cardType === window.notifCurrentTab;
        
        // Cek kecocokan Kata Kunci (Search)
        const matchKeyword = keyword === '' || text.includes(keyword);
        
        // Cek kecocokan Tanggal (Date Picker)
        const matchDate    = date === '' || text.includes(date);

        if (matchTab && matchKeyword && matchDate) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Manajemen Tampilan State Kosong (Empty State)
    const emptyState = document.getElementById('notifEmpty');
    const noDataState = document.getElementById('notifNoData');

    const totalCards = items.length;
    const isEmpty = visibleCount === 0;
    const isSearching = keyword !== '' || date !== '';

    if (noDataState) {
        // Tampilkan "Belum Ada Data" jika database kosong DAN tidak sedang mencari
        noDataState.style.display = (totalCards === 0 && !isSearching) ? 'flex' : 'none';
    }

    if (emptyState) {
        // Tampilkan "Data Tidak Ditemukan" jika (ada data tapi tidak cocok) ATAU (database kosong tapi sedang mencari)
        emptyState.style.display = (isEmpty && isSearching) ? 'flex' : 'none';
    }
}

/**
 * Berpindah antar tab kategori (Semua, Kritis, Peringatan, Akses)
 */
function filterTab(tab, btn) {
    if (!btn) return;

    // 1. Simpan state tab aktif secara global
    window.notifCurrentTab = tab;

    // 2. Perbarui UI tombol tab
    const allTabs = document.querySelectorAll('.tab-btn');
    allTabs.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // 3. Terapkan seluruh filter (termasuk pencarian & tanggal)
    filterNotifications();

    // 4. Reset scroll list
    const listWrapper = document.getElementById('notifList');
    if (listWrapper) listWrapper.scrollTop = 0;
}

// EKSPOS KE WINDOW
window.filterTab = filterTab;


// ==========================================================================
// 2. LOGIKA REALTIME CLOCK
// ==========================================================================

/**
 * Memperbarui tampilan jam dan tanggal realtime di header dashboard
 */
function updateRealtimeClock() {
    const dateElement  = document.getElementById("realtime-date");
    const clockElement = document.getElementById("realtime-clock");

    if (!dateElement || !clockElement) return;

    const now = new Date();

    // Format Tanggal: DD Bulan YYYY
    const dateOptions = { day: "2-digit", month: "long", year: "numeric" };
    const dateStr     = now.toLocaleDateString("id-ID", dateOptions);

    // Format Jam: HH:mm:ss
    const timeStr = now.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false
    }).replace(/\./g, ":");

    dateElement.textContent  = dateStr;
    clockElement.textContent = timeStr;
}


// ==========================================================================
// 3. LOGIKA MONITORING IOT (Status & Lokasi)
// ==========================================================================

/**
 * Perbarui UI Card Brankas (Terkunci / Terbuka / Offline)
 */
function updateBrankasStatus(isOnline, statusPintu) {
    const card     = document.getElementById('brankas-card');
    const label    = document.getElementById('status-label');
    const liveText = document.querySelector('.live-text');
    
    if (!card || !label) return;

    const iconLocked   = card.querySelector('.icon-locked');
    const iconUnlocked = card.querySelector('.icon-unlocked');
    const iconOffline  = card.querySelector('.icon-offline');

    // 1. Kondisi Offline (Tidak Terhubung ke Alat)
    if (!isOnline) {
        card.classList.add('is-offline');
        card.classList.remove('is-open');
        label.innerText = 'TIDAK TERHUBUNG';
        if (liveText) liveText.innerText = 'OFFLINE';
        
        if (iconLocked)   iconLocked.style.display   = 'none';
        if (iconUnlocked) iconUnlocked.style.display = 'none';
        if (iconOffline)  iconOffline.style.display  = 'block';
        return;
    }

    // 2. Kondisi Online (Terhubung)
    card.classList.remove('is-offline');
    if (liveText) liveText.innerText = 'LIVE';
    if (iconOffline) iconOffline.style.display = 'none';

    if (statusPintu === 'TERBUKA') {
        card.classList.add('is-open');
        label.innerText = 'TERBUKA';
        if (iconLocked)   iconLocked.style.display   = 'none';
        if (iconUnlocked) iconUnlocked.style.display = 'block';
    } else {
        card.classList.remove('is-open');
        label.innerText = 'TERKUNCI';
        if (iconLocked)   iconLocked.style.display   = 'block';
        if (iconUnlocked) iconUnlocked.style.display = 'none';
    }
}

// Global state untuk tracking koordinat terakhir agar peta tidak reload terus-menerus
let lastLat = null;
let lastLng = null;

/**
 * Perbarui UI Lokasi (Koordinat, Alamat, dan Peta Google)
 */
function updateLocationData(data) {
    if (!data.latitude || !data.longitude) return;

    const latEl  = document.getElementById('brankas-lat-val');
    const lngEl  = document.getElementById('brankas-lng-val');
    const nameEl = document.getElementById('brankas-nama-val');
    const locEl  = document.getElementById('brankas-lokasi-val');
    let mapEl    = document.getElementById('brankas-map');
    const placeholderEl = document.getElementById('brankas-map-placeholder');

    if (latEl)  latEl.innerText  = data.latitude;
    if (lngEl)  lngEl.innerText  = data.longitude;
    if (nameEl) nameEl.innerText = data.nama_brankas || '-';
    if (locEl)  locEl.innerText  = data.lokasi || '-';

    // Jika peta masih berupa placeholder (saat pertama load), ganti ke Iframe
    if (!mapEl && placeholderEl) {
        placeholderEl.outerHTML = `
            <iframe id="brankas-map"
                src="https://maps.google.com/maps?q=${data.latitude},${data.longitude}&t=&z=17&ie=UTF8&iwloc=&output=embed"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        `;
        lastLat = data.latitude;
        lastLng = data.longitude;
        return;
    }

    // Perbarui peta hanya jika koordinat berubah (menghemat bandwidth/request)
    if (mapEl && (lastLat !== data.latitude || lastLng !== data.longitude)) {
        mapEl.src = `https://maps.google.com/maps?q=${data.latitude},${data.longitude}&t=&z=17&ie=UTF8&iwloc=&output=embed`;
        lastLat = data.latitude;
        lastLng = data.longitude;
    }
}


// ==========================================================================
// 4. SISTEM POLLING (Realtime Fetching)
// ==========================================================================

/**
 * Mengambil data status brankas dan GPS terbaru dari server melalui API
 */
function checkRealtimeStatus() {
    fetch('/api/brankas/status-realtime')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Perbarui status pintu & koneksi
                updateBrankasStatus(data.is_online, data.status_pintu);
                
                // Perbarui peta dan data lokasi
                updateLocationData(data);
            }
        })
        .catch(err => console.error('Polling error:', err));
}


// ==========================================================================
// 5. INISIALISASI & LIFECYCLE
// ==========================================================================

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi status sidebar dari localStorage
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        const mainWrap = document.getElementById('mainWrap');
        if (mainWrap) mainWrap.classList.add('collapsed');
    }

    // 2. Event listener untuk filter tanggal (untuk menangani trigger manual dari calendar.js)
    const historyDateFilter = document.getElementById('historyDateFilter');
    if (historyDateFilter) {
        historyDateFilter.addEventListener('change', filterHistory);
    }

    const notifDateFilter = document.getElementById('notifDateFilter');
    if (notifDateFilter) {
        notifDateFilter.addEventListener('change', filterNotifByDate);
    }

    // 3. Jalankan Jam Realtime segera
    updateRealtimeClock();

    // 4. Inisialisasi tampilan filter (Banner, dll)
    filterHistory();
    filterNotifications();
});

// Jalankan update jam setiap detik
setInterval(updateRealtimeClock, 1000);

// Jalankan polling status setiap 3 detik
setInterval(checkRealtimeStatus, 3000);

// EKSPOS KE WINDOW (Agar bisa dipanggil dari atribut onclick/oninput di HTML)
window.filterHistory = filterHistory;
window.filterNotifications = filterNotifications;
window.filterNotifByDate = filterNotifByDate;
window.filterTab = filterTab;
window.openDatePicker = openDatePicker;
window.openNotifDatePicker = openNotifDatePicker;
window.toggleStatus = toggleStatus;
window.checkRealtimeStatus = checkRealtimeStatus;
window.updateBrankasStatus = updateBrankasStatus;
window.updateRealtimeClock = updateRealtimeClock;
window.applyNotifFilter = applyNotifFilter;