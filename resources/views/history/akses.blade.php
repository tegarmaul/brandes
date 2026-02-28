@extends('layouts.app')

@section('title', 'History Akses')
@section('page_title', 'History Akses')
@section('page_subtitle', 'Riwayat aktivitas akses brankas.')

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
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon svg { width: 26px; height: 26px; }
    .stat-icon.green svg { stroke: var(--green-dark); }
    .stat-icon.red   { background: #FEF2F2; }
    .stat-icon.red svg { stroke: var(--red); }

    /* Toolbar */
    .toolbar {
        display: flex;
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
        transition: border-color 0.2s;
    }

    .search-box:focus-within { border-color: var(--green); }
    .search-box svg { width: 16px; height: 16px; stroke: var(--text-muted); flex-shrink: 0; }

    .search-box input {
        border: none; outline: none;
        font-family: inherit; font-size: 13.5px;
        color: var(--text); width: 100%; background: transparent;
    }

    .search-box input::placeholder { color: var(--text-muted); }

    .btn-download {
        display: flex; align-items: center; gap: 7px;
        background: #fff; color: var(--text);
        border: 1.5px solid var(--border);
        border-radius: 10px; padding: 10px 18px;
        font-size: 13.5px; font-weight: 600;
        font-family: inherit; cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s;
        white-space: nowrap;
    }

    .btn-download:hover { border-color: var(--green); box-shadow: 0 2px 8px rgba(34,197,94,0.15); }
    .btn-download svg { width: 16px; height: 16px; stroke: var(--text-muted); }

    /* Date picker — icon custom, bawaan browser disembunyikan */
    .date-picker {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        background: #fff;
        cursor: pointer;
        white-space: nowrap;
        transition: border-color 0.2s;
        user-select: none;
    }

    .date-picker:hover { border-color: var(--green); }
    .date-picker svg { width: 16px; height: 16px; stroke: var(--text-muted); flex-shrink: 0; }

    /* Input date tersembunyi — hanya trigger */
    .date-picker input[type="date"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    /* Sembunyikan icon kalender bawaan browser */
    input[type="date"]::-webkit-calendar-picker-indicator { display: none; }
    input[type="date"]::-webkit-inner-spin-button          { display: none; }

    /* Table */
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
        font-size: 11.5px; font-weight: 700;
        color: var(--text-muted); text-align: left;
        letter-spacing: 0.5px; text-transform: uppercase;
        white-space: nowrap;
    }

    thead th .sort { display: inline-flex; align-items: center; gap: 3px; cursor: pointer; }
    thead th svg { width: 13px; height: 13px; stroke: var(--text-muted); }

    tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FAFAFA; }
    tbody td { padding: 16px 20px; font-size: 13.5px; color: var(--text); }

    .td-nama { font-weight: 600; }
    .td-aktivitas { color: var(--text); }

    .td-waktu { display: flex; flex-direction: column; gap: 2px; }
    .td-waktu .time-main { font-weight: 500; font-size: 13px; }
    .td-waktu .time-ago  { font-size: 12px; color: var(--text-muted); }

    .td-total { font-weight: 700; color: var(--text); }

    .status-berhasil { color: var(--green-dark); font-weight: 700; font-size: 13px; }
    .status-gagal    { color: var(--red); font-weight: 700; font-size: 13px; }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr; }
        .toolbar { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')

    {{-- STAT CARDS --}}
    <div class="stat-grid">

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Total Akses Hari ini</span>
                <span class="stat-value">{{ $totalAkses }}</span>
            </div>
            <div class="stat-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M8.41711 25.9729C11.9594 25.9729 14.8342 22.9373 14.8342 19.1971C14.8342 17.8148 14.4364 16.5409 13.769 15.4704L18.0428 10.9577L20.3401 13.3834L22.1497 11.4726L19.8524 9.04687L21.2513 7.56973L24.1904 10.6731L26 8.76228L23.061 5.65895L24.7166 3.91078L22.907 2L11.9465 13.5731C10.9326 12.8684 9.71337 12.4483 8.41711 12.4483C4.87487 12.4483 2 15.4839 2 19.2242C2 22.9644 4.87487 26 8.41711 26V25.9729ZM8.41711 15.1316C10.5348 15.1316 12.2674 16.961 12.2674 19.1971C12.2674 21.4331 10.5348 23.2626 8.41711 23.2626C6.29946 23.2626 4.56684 21.4331 4.56684 19.1971C4.56684 16.961 6.29946 15.1316 8.41711 15.1316Z" fill="#00A63E"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Akses Berhasil</span>
                <span class="stat-value">{{ $aksesberhasil }}</span>
            </div>
            <div class="stat-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M14 26C20.6274 26 26 20.6274 26 14C26 7.37258 20.6274 2 14 2C7.37258 2 2 7.37258 2 14C2 20.6274 7.37258 26 14 26Z" stroke="#00A63E" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 14L12.3333 17L19 11" stroke="#00A63E" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Akses Gagal</span>
                <span class="stat-value">{{ $aksesGagal }}</span>
            </div>
            <div class="stat-icon red">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <g clip-path="url(#clip0_208_2614)">
                        <path d="M14 26C20.6274 26 26 20.6274 26 14C26 7.37258 20.6274 2 14 2C7.37258 2 2 7.37258 2 14C2 20.6274 7.37258 26 14 26Z" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.5 10.5L10.5 17.5" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.5 10.5L17.5 17.5" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_208_2614">
                            <rect width="28" height="28" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </div>

    </div>

    {{-- TOOLBAR --}}
    <div class="toolbar">

        {{-- Search --}}
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" id="searchInput" placeholder="Cari History..." oninput="filterTable()">
        </div>

        {{-- Download --}}
        <button class="btn-download" onclick="downloadRekap()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download Rekap
        </button>

        {{-- Date picker — icon custom, input tersembunyi --}}
        <div class="date-picker" onclick="openDatePicker()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <input type="date" id="dateFilter" onchange="filterByDate()">
            <span id="dateLabel" style="font-size:13.5px;color:var(--text-muted);">mm/dd/yyyy</span>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-wrap">
            <table id="historyTable">
                <thead>
                    <tr>
                        <th><span class="sort">NAMA <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">AKTIVITAS <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">WAKTU <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">TOTAL AKSES <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">STATUS <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td class="td-nama">{{ $history['nama'] }}</td>
                        <td class="td-aktivitas">{{ $history['aktivitas'] }}</td>
                        <td>
                            <div class="td-waktu">
                                <span class="time-main">{{ $history['waktu'] }}</span>
                                <span class="time-ago">{{ $history['waktu_lalu'] }}</span>
                            </div>
                        </td>
                        <td class="td-total">{{ $history['total_akses'] }}X</td>
                        <td>
                            <span class="{{ $history['status'] === 'Berhasil' ? 'status-berhasil' : 'status-gagal' }}">
                                {{ $history['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted);">
                            Belum ada history akses.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/history-akses.js') }}"></script>
@endpush