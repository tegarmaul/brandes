/* ═══════════════════════════════════════
   BRANDES — Dashboard User JS
   File: public/js/dashboard-user.js
   Handles: date picker, history filter,
            and UI interactions for user dashboard
═══════════════════════════════════════ */

/* ── Buka date picker saat icon diklik ── */
function openDatePicker() {
    const input = document.getElementById('historyDate');
    if (input.showPicker) {
        input.showPicker();
    } else {
        input.click();
    }
}

/* ── Filter history: search + date ── */
function filterHistory() {
    const keyword = document.getElementById('historySearch').value.toLowerCase().trim();
    const date    = document.getElementById('historyDate').value;

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

    const items = document.querySelectorAll('#historyList .history-item');
    let visibleCount = 0;

    items.forEach(item => {
        const text      = item.textContent.toLowerCase();
        const itemDate  = item.dataset.date || '';

        const matchKeyword = keyword === '' || text.includes(keyword);
        const matchDate    = date === '' || itemDate === date;

        if (matchKeyword && matchDate) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Tampilkan empty state jika tidak ada data yang cocok
    document.getElementById('historyEmpty').style.display =
        visibleCount === 0 ? 'block' : 'none';
}

/* ── Greeting berdasarkan waktu ── */
(function initGreeting() {
    const hour = new Date().getHours();
    let greeting = 'Selamat Datang';

    if (hour >= 5 && hour < 12)       greeting = 'Selamat Pagi';
    else if (hour >= 12 && hour < 15)  greeting = 'Selamat Siang';
    else if (hour >= 15 && hour < 18)  greeting = 'Selamat Sore';
    else                                greeting = 'Selamat Malam';

    // Update subtitle jika ada elemen topbar
    const subtitle = document.querySelector('.topbar-left p');
    if (subtitle) {
        const currentText = subtitle.textContent;
        // Hanya update jika mengandung "Selamat datang"
        if (currentText.includes('Selamat datang')) {
            subtitle.textContent = currentText.replace('Selamat datang', greeting);
        }
    }
})();
