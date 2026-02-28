/* ═══════════════════════════════════════
   APP LAYOUT — JavaScript
   public/js/app.js
═══════════════════════════════════════ */

/* ── Sidebar: restore state saat halaman load ── */
(function () {
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('mainWrap').classList.add('collapsed');
    }
})();

/* ── Toggle sidebar collapse ── */
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const mainWrap = document.getElementById('mainWrap');
    sidebar.classList.toggle('collapsed');
    mainWrap.classList.toggle('collapsed');
    localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
}

/* ── Modal Logout ── */
function openLogoutModal()     { document.getElementById('logoutOverlay').classList.add('show'); }
function closeLogoutModal()    { document.getElementById('logoutOverlay').classList.remove('show'); }
function closeLogoutOutside(e) { if (e.target.id === 'logoutOverlay') closeLogoutModal(); }