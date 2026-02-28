/* ═══════════════════════════════════════
   BRANDES — Dashboard Admin JS
   File: public/js/dashboard-admin.js
═══════════════════════════════════════ */

/* ── Toggle status brankas ── */
function toggleStatus() {
    document.getElementById('statusToggle').classList.toggle('off');
}

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

    // Update label tanggal yang tampil
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
        const name      = item.dataset.name || '';
        const itemDate  = item.dataset.date || '';
        const matchName = keyword === '' || name.includes(keyword);
        const matchDate = date === '' || itemDate === date;

        if (matchName && matchDate) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Tampilkan empty state jika tidak ada hasil
    document.getElementById('historyEmpty').style.display =
        visibleCount === 0 ? 'block' : 'none';
}