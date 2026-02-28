@extends('layouts.app')

@section('title', 'List Admin')
@section('page_title', 'List Admin')
@section('page_subtitle', 'Kelola adminstrator sistem keamanan brankas.')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
        animation: fadeUp 0.4s ease both;
    }

    .stat-card:nth-child(2) { animation-delay: 0.08s; }
    .stat-card:nth-child(3) { animation-delay: 0.16s; }

    .stat-info { display: flex; flex-direction: column; gap: 6px; }
    .stat-label { font-size: 13px; font-weight: 500; color: var(--text-muted); }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }

    .stat-icon {
        width: 48px; height: 48px;
        background: var(--green-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon svg { width: 26px; height: 26px; stroke: var(--green-dark); }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        background: #fff;
        flex: 1;
        max-width: 520px;
        transition: border-color 0.2s;
    }

    .search-box:focus-within { border-color: var(--green); }
    .search-box svg { width: 16px; height: 16px; stroke: var(--text-muted); flex-shrink: 0; }

    .search-box input {
        border: none; outline: none;
        font-family: inherit;
        font-size: 13.5px;
        color: var(--text);
        width: 100%;
        background: transparent;
    }

    .search-box input::placeholder { color: var(--text-muted); }

    .btn-tambah {
        display: flex;
        align-items: center;
        gap: 7px;
        background: var(--green);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 13.5px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s;
        white-space: nowrap;
    }

    .btn-tambah:hover { background: var(--green-dark); box-shadow: 0 4px 16px rgba(34,197,94,0.3); }
    .btn-tambah svg { width: 16px; height: 16px; }

    .table-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp 0.4s ease 0.1s both;
    }

    .table-wrap { overflow-x: auto; }

    table { width: 100%; border-collapse: collapse; }

    thead tr { border-bottom: 1.5px solid var(--border); background: #FAFAFA; }

    thead th {
        padding: 12px 20px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-align: left;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    thead th .sort { display: inline-flex; align-items: center; gap: 3px; cursor: pointer; }
    thead th svg { width: 13px; height: 13px; stroke: var(--text-muted); }

    tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FAFAFA; }

    tbody td { padding: 16px 20px; font-size: 13.5px; color: var(--text); }

    .td-no { color: var(--text-muted); font-weight: 600; }
    .td-nama { font-weight: 600; }

    .td-pin { display: flex; align-items: center; gap: 6px; font-weight: 500; }
    .td-pin svg { width: 15px; height: 15px; stroke: var(--green); }

    .badge-role { font-size: 12px; font-weight: 600; }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-status.aktif    { background: var(--green-light); color: var(--green-dark); }
    .badge-status.nonaktif { background: #FEF2F2; color: var(--red); }

    .aksi-wrap { position: relative; }

    .btn-aksi {
        background: none; border: none; cursor: pointer;
        padding: 6px; border-radius: 6px;
        color: var(--text-muted); transition: background 0.15s; display: flex;
    }

    .btn-aksi:hover { background: var(--border); color: var(--text); }
    .btn-aksi svg { width: 18px; height: 18px; }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0; top: 110%;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        min-width: 140px;
        z-index: 50;
        overflow: hidden;
    }

    .dropdown-menu.show { display: block; }

    .dropdown-item {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 14px; font-size: 13px; color: var(--text);
        cursor: pointer; transition: background 0.15s;
        border: none; background: none; width: 100%;
        font-family: inherit; text-decoration: none;
    }

    .dropdown-item:hover { background: #F9FAFB; }
    .dropdown-item.danger { color: var(--red); }
    .dropdown-item.danger:hover { background: #FEF2F2; }
    .dropdown-item svg { width: 15px; height: 15px; }

    /* ── MODAL ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 200;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show { display: flex; }

    .modal {
        background: #fff;
        border-radius: 20px;
        padding: 32px 28px 28px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.15);
        animation: fadeUp 0.25s ease;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }

    .modal-title { font-size: 20px; font-weight: 800; color: var(--text); }

    .modal-close {
        background: #F3F4F6;
        border: none; cursor: pointer;
        color: var(--text);
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s;
    }

    .modal-close:hover { background: var(--border); }
    .modal-close svg { width: 18px; height: 18px; }

    .form-group { margin-bottom: 18px; }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
    }

    .input-wrapper { position: relative; display: flex; align-items: center; }

    .input-icon {
        position: absolute; left: 14px;
        color: #9CA3AF; display: flex;
        align-items: center; pointer-events: none;
    }

    .input-icon svg { width: 18px; height: 18px; }

    .form-control {
        width: 100%;
        padding: 13px 14px;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-size: 14px;
        font-family: inherit;
        color: var(--text);
        outline: none;
        background: #F9FAFB;
        transition: border-color 0.2s, background 0.2s;
    }

    .form-control.with-icon { padding-left: 44px; }
    .form-control:focus { border-color: var(--green); background: #fff; box-shadow: 0 0 0 3px rgba(34,197,94,0.1); }
    .form-control::placeholder { color: #9CA3AF; }

    .select-wrapper select.with-icon { padding-right: 44px; appearance: none; cursor: pointer; }

    .select-arrow { position: absolute; right: 14px; pointer-events: none; color: #6B7280; display: flex; }
    .select-arrow svg { width: 18px; height: 18px; }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: var(--green);
        color: white; border: none;
        border-radius: 12px;
        font-size: 15px; font-weight: 700;
        font-family: inherit; cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s;
        margin-top: 8px;
    }

    .btn-submit:hover { background: var(--green-dark); box-shadow: 0 4px 16px rgba(34,197,94,0.3); }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .search-box { max-width: 100%; }
    }
</style>
@endpush

@section('content')

    {{-- STAT CARDS --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Total Admin</span>
                <span class="stat-value">{{ $totalAdmin }}</span>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M18.6654 24.5V22.1667C18.6654 20.929 18.1737 19.742 17.2985 18.8668C16.4234 17.9917 15.2364 17.5 13.9987 17.5H6.9987C5.76102 17.5 4.57404 17.9917 3.69887 18.8668C2.8237 19.742 2.33203 20.929 2.33203 22.1667V24.5" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.668 3.64941C19.6687 3.90885 20.5549 4.49322 21.1876 5.31082C21.8203 6.12842 22.1635 7.13295 22.1635 8.16675C22.1635 9.20054 21.8203 10.2051 21.1876 11.0227C20.5549 11.8403 19.6687 12.4246 18.668 12.6841" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M25.668 24.4997V22.1664C25.6672 21.1324 25.3231 20.1279 24.6896 19.3107C24.0561 18.4935 23.1691 17.9099 22.168 17.6514" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.4987 12.8333C13.076 12.8333 15.1654 10.744 15.1654 8.16667C15.1654 5.58934 13.076 3.5 10.4987 3.5C7.92137 3.5 5.83203 5.58934 5.83203 8.16667C5.83203 10.744 7.92137 12.8333 10.4987 12.8333Z" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Admin Aktif</span>
                <span class="stat-value">{{ $adminAktif }}</span>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M18.668 12.8333L21.0013 15.1667L25.668 10.5" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.6654 24.5V22.1667C18.6654 20.929 18.1737 19.742 17.2985 18.8668C16.4234 17.9917 15.2364 17.5 13.9987 17.5H6.9987C5.76102 17.5 4.57404 17.9917 3.69887 18.8668C2.8237 19.742 2.33203 20.929 2.33203 22.1667V24.5" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.4987 12.8333C13.076 12.8333 15.1654 10.744 15.1654 8.16667C15.1654 5.58934 13.076 3.5 10.4987 3.5C7.92137 3.5 5.83203 5.58934 5.83203 8.16667C5.83203 10.744 7.92137 12.8333 10.4987 12.8333Z" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Admin Nonaktif</span>
                <span class="stat-value">{{ $adminNonaktif ?: '-' }}</span>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M18.6654 24.5V22.1667C18.6654 20.929 18.1737 19.742 17.2985 18.8668C16.4234 17.9917 15.2364 17.5 13.9987 17.5H6.9987C5.76102 17.5 4.57404 17.9917 3.69887 18.8668C2.8237 19.742 2.33203 20.929 2.33203 22.1667V24.5" stroke="#00A63E" stroke-width="2.33333" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.4987 12.8333C13.076 12.8333 15.1654 10.744 15.1654 8.16667C15.1654 5.58934 13.076 3.5 10.4987 3.5C7.92137 3.5 5.83203 5.58934 5.83203 8.16667C5.83203 10.744 7.92137 12.8333 10.4987 12.8333Z" stroke="#00A63E" stroke-width="2.33333" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 9L26.4 15.4M26.4 9L20 15.4" stroke="#00A63E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M21.0002 21.0002L16.6602 16.6602" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input type="text" id="searchInput" placeholder="Cari Admin..." onkeyup="filterTable()">
        </div>
        <button class="btn-tambah" onclick="openModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Admin
        </button>
    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-wrap">
            <table id="adminTable">
                <thead>
                    <tr>
                        <th><span class="sort">NO</span></th>
                        <th>
                            <span class="sort">NAMA</span>
                        </th>
                        <th>
                            <span class="sort">PIN</span>
                        </th>
                        <th>
                            <span class="sort">ROLE</span>
                        </th>
                        <th>
                            <span class="sort">STATUS</span>
                        </th>
                        <th>
                            <span class="sort">AKSI</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $index => $admin)
                    <tr>
                        <td class="td-no">#{{ $index + 1 }}</td>
                        <td class="td-nama">{{ $admin->nama }}</td>
                        <td>
                            <div class="td-pin">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                </svg>
                                ••••••
                            </div>
                        </td>
                        <td class="badge-role">{{ ucfirst($admin->role) }}</td>
                        <td>
                            <span class="badge-status {{ $admin->aktif ? 'aktif' : 'nonaktif' }}">
                                {{ $admin->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="aksi-wrap">
                                <button class="btn-aksi" onclick="toggleDropdown(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#101828" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="#101828" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="#101828" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                        Edit
                                    </button>
                                    <button class="dropdown-item danger">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada data admin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH ADMIN --}}
    <div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">Tambah Admin</span>
                <button class="modal-close" onclick="closeModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="admin">

                {{-- Nama --}}
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </span>
                        <input type="text" name="nama" class="form-control with-icon" placeholder="Masukan nama lengkap admin" required>
                    </div>
                </div>

                {{-- Username --}}
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </span>
                        <input type="text" name="username" class="form-control with-icon" placeholder="Masukan username unik" required autocomplete="off">
                    </div>
                </div>

                {{-- PIN --}}
                <div class="form-group">
                    <label>PIN</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </span>
                        <input type="password" name="pin" id="adminPinInput" class="form-control with-icon with-icon-right"
                               placeholder="6 digit PIN" maxlength="6" inputmode="numeric" required>
                        <button type="button" class="toggle-eye" onclick="toggleAdminPin()" title="Tampilkan PIN">
                            <svg id="adminEyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="form-hint">PIN 6 digit yang digunakan pada keypad IoT untuk verifikasi akses</p>
                </div>

                <button type="submit" class="btn-submit">Tambah Admin</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openModal()  { document.getElementById('modalOverlay').classList.add('show'); }
    function closeModal() { document.getElementById('modalOverlay').classList.remove('show'); }
    function closeModalOutside(e) { if (e.target.id === 'modalOverlay') closeModal(); }

    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        document.querySelectorAll('.dropdown-menu.show').forEach(m => { if (m !== menu) m.classList.remove('show'); });
        menu.classList.toggle('show');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.aksi-wrap')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
        }
    });

    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#adminTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(input) ? '' : 'none';
        });
    }

    function toggleAdminPin() {
        const input = document.getElementById('adminPinInput');
        const icon  = document.getElementById('adminEyeIcon');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`;
    }
</script>
@endpush