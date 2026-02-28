/* ═══════════════════════════════════════
   BRANDES — Notifikasi Keamanan JS
   File: public/js/notifikasi-keamanan.js
═══════════════════════════════════════ */

/* ── Buka date picker saat area diklik ── */
function openDatePicker() {
    const input = document.getElementById('dateFilter');
    if (input.showPicker) {
        input.showPicker();
    } else {
        input.click();
    }
}

/* ── Filter notif berdasarkan search input ── */
function filterNotif() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    const date    = document.getElementById('dateFilter').value;
    applyFilter(keyword, date);
}

/* ── Filter notif berdasarkan tanggal ── */
function filterByDate() {
    const date = document.getElementById('dateFilter').value;

    // Update label tanggal
    const label = document.getElementById('dateLabel');
    if (date) {
        const d = new Date(date + 'T00:00:00');
        label.textContent = d.toLocaleDateString('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric'
        });
        label.style.color = 'var(--text)';
    } else {
        label.textContent = 'mm/dd/yyyy';
        label.style.color = 'var(--text-muted)';
    }

    const keyword = document.getElementById('searchInput').value.toLowerCase();
    applyFilter(keyword, date);
}

/* ── Filter tab: Semua / Belum dibaca ── */
function filterTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.notif-card').forEach(card => {
        if (tab === 'semua') {
            card.style.display = '';
        } else {
            card.style.display = card.dataset.dibaca === 'tidak' ? '' : 'none';
        }
    });
}

/* ── Kombinasi filter search + date ── */
function applyFilter(keyword, date) {
    document.querySelectorAll('.notif-card').forEach(card => {
        const text = card.textContent.toLowerCase();

        const matchKeyword = keyword === '' || text.includes(keyword);
        const matchDate    = date === '' || text.includes(date);

        card.style.display = (matchKeyword && matchDate) ? '' : 'none';
    });
}