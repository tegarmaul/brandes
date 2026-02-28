/* ═══════════════════════════════════════
   BRANDES — History Akses JS
   File: public/js/history-akses.js
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

/* ── Filter tabel berdasarkan search input ── */
function filterTable() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    applyFilter(keyword, document.getElementById('dateFilter').value);
}

/* ── Filter tabel berdasarkan tanggal ── */
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

    applyFilter(document.getElementById('searchInput').value.toLowerCase(), date);
}

/* ── Kombinasi filter search + date ── */
function applyFilter(keyword, date) {
    document.querySelectorAll('#historyTable tbody tr').forEach(row => {
        const text  = row.textContent.toLowerCase();
        const waktu = row.querySelector('.time-main')?.textContent || '';

        const matchKeyword = keyword === '' || text.includes(keyword);
        const matchDate    = date === '' || waktu.includes(date);

        row.style.display = (matchKeyword && matchDate) ? '' : 'none';
    });
}

/* ── Download Rekap CSV ── */
function downloadRekap() {
    const rows = document.querySelectorAll('#historyTable tbody tr');
    let csv    = 'Nama,Aktivitas,Waktu,Total Akses,Status\n';

    rows.forEach(row => {
        if (row.style.display === 'none') return; // skip baris tersembunyi
        const cols = row.querySelectorAll('td');
        if (cols.length < 5) return;

        const nama      = cols[0].textContent.trim();
        const aktivitas = cols[1].textContent.trim();
        const waktu     = cols[2].querySelector('.time-main')?.textContent.trim() || '';
        const total     = cols[3].textContent.trim();
        const status    = cols[4].textContent.trim();

        csv += `"${nama}","${aktivitas}","${waktu}","${total}","${status}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = 'rekap-history-akses.csv';
    a.click();
    URL.revokeObjectURL(url);
}