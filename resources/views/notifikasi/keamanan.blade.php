@extends('layouts.app')

@section('title', 'Notifikasi Keamanan')
@section('page_title', 'Notifikasi Keamanan')
@section('page_subtitle', 'Pantau aktivitas mencurigakan dan peringatan keamanan.')

@push('styles')
<style>
    /* Stat Grid — 4 kolom */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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

    .stat-card:nth-child(2) { animation-delay: 0.06s; }
    .stat-card:nth-child(3) { animation-delay: 0.12s; }
    .stat-card:nth-child(4) { animation-delay: 0.18s; }

    .stat-info { display: flex; flex-direction: column; gap: 6px; }
    .stat-label { font-size: 13px; font-weight: 500; color: var(--text-muted); }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }

    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon svg { width: 26px; height: 26px; }
    .stat-icon.green  { background: var(--green-light); }
    .stat-icon.green svg { stroke: var(--green-dark); }
    .stat-icon.yellow { background: #FFFBEB; }
    .stat-icon.yellow svg { stroke: #D97706; }
    .stat-icon.red    { background: #FEF2F2; }
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

    /* Filter Tabs */
    .filter-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filter-tabs {
        display: flex;
        gap: 4px;
        background: #F3F4F6;
        border-radius: 10px;
        padding: 4px;
    }

    .tab-btn {
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        background: none;
        font-family: inherit;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .tab-btn.active {
        background: var(--green);
        color: white;
        box-shadow: 0 2px 8px rgba(34,197,94,0.3);
    }

    .legend {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 13px;
        color: var(--text-muted);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot.red    { background: var(--red); }
    .dot.yellow { background: #F59E0B; }
    .dot.green  { background: var(--green); }

    /* Notif Cards */
    .notif-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        animation: fadeUp 0.4s ease 0.1s both;
    }

    .notif-card {
        border-radius: 14px;
        padding: 20px 22px;
        border: 1.5px solid transparent;
        transition: box-shadow 0.2s;
    }

    .notif-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }

    .notif-card.peringatan { background: #FFFDF0; border-color: #FDE68A; }
    .notif-card.kritis     { background: #FFF5F5; border-color: #FECACA; }
    .notif-card.akses      { background: #F0FDF4; border-color: #BBF7D0; }

    .notif-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
    }

    .notif-left {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .notif-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .notif-icon.peringatan { background: #FEF9C3; }
    .notif-icon.peringatan svg { stroke: #D97706; width: 20px; height: 20px; }
    .notif-icon.kritis     { background: #FEE2E2; }
    .notif-icon.kritis svg { stroke: var(--red); width: 20px; height: 20px; }
    .notif-icon.akses      { background: #DCFCE7; }
    .notif-icon.akses svg  { stroke: var(--green-dark); width: 20px; height: 20px; }

    .notif-body { flex: 1; }

    .notif-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .notif-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .badge-type {
        display: inline-flex;
        align-items: center;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .badge-type.peringatan { background: #F59E0B; color: white; }
    .badge-type.kritis     { background: var(--red); color: white; }
    .badge-type.akses      { background: var(--green); color: white; }

    .notif-meta {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(0,0,0,0.06);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
    }

    .meta-detail {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .meta-detail svg { width: 14px; height: 14px; stroke: var(--text-muted); }

    .meta-dot {
        width: 4px; height: 4px;
        border-radius: 50%;
        background: var(--text-muted);
    }

    .notif-time {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .notif-time svg { width: 15px; height: 15px; stroke: var(--text-muted); }

    @media (max-width: 1024px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 640px) {
        .stat-grid { grid-template-columns: 1fr; }
        .filter-row { flex-direction: column; align-items: flex-start; gap: 10px; }
        .toolbar { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')

    {{-- STAT CARDS --}}
    <div class="stat-grid">

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Total Notifikasi</span>
                <span class="stat-value">{{ $totalNotifikasi }}</span>
            </div>
            <div class="stat-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <path d="M11.9805 24.5C12.1853 24.8547 12.4798 25.1492 12.8345 25.354C13.1892 25.5588 13.5916 25.6666 14.0011 25.6666C14.4107 25.6666 14.8131 25.5588 15.1678 25.354C15.5225 25.1492 15.817 24.8547 16.0218 24.5" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.80481 17.88C3.65241 18.0471 3.55183 18.2548 3.51531 18.4779C3.4788 18.7011 3.50792 18.9301 3.59914 19.137C3.69036 19.3439 3.83974 19.5198 4.02911 19.6434C4.21849 19.767 4.43969 19.8328 4.66581 19.833H23.3325C23.5586 19.8331 23.7798 19.7675 23.9693 19.6441C24.1588 19.5208 24.3084 19.345 24.3999 19.1383C24.4913 18.9315 24.5207 18.7026 24.4845 18.4794C24.4483 18.2562 24.348 18.0484 24.1958 17.8812C22.6441 16.2817 20.9991 14.5818 20.9991 9.33301C20.9991 7.47649 20.2617 5.69601 18.9489 4.38326C17.6361 3.07051 15.8557 2.33301 13.9991 2.33301C12.1426 2.33301 10.3622 3.07051 9.0494 4.38326C7.73665 5.69601 6.99915 7.47649 6.99915 9.33301C6.99915 14.5818 5.35298 16.2817 3.80481 17.88Z" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Peringatan</span>
                <span class="stat-value">{{ $totalPeringatan }}</span>
            </div>
            <div class="stat-icon yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Kritis</span>
                <span class="stat-value">{{ $totalKritis }}</span>
            </div>
            <div class="stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Akses</span>
                <span class="stat-value">{{ $totalAkses }}</span>
            </div>
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
            <input type="text" id="searchInput" placeholder="Cari History" oninput="filterNotif()">
        </div>

        {{-- Date picker — icon custom, input tersembunyi --}}
        <div class="date-picker" onclick="openDatePicker()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <input type="date" id="dateFilter" onchange="filterByDate()">
            <span id="dateLabel" style="font-size:13.5px;color:var(--text-muted);">mm/dd/yyy</span>
        </div>

    </div>

    {{-- FILTER TABS + LEGEND --}}
    <div class="filter-row">
        <div class="filter-tabs">
            <button class="tab-btn active" onclick="filterTab('semua', this)">Semua</button>
            <button class="tab-btn" onclick="filterTab('belum', this)">Belum dibaca</button>
        </div>
        <div class="legend">
            <div class="legend-item"><span class="dot red"></span> Kritis</div>
            <div class="legend-item"><span class="dot yellow"></span> Peringatan</div>
            <div class="legend-item"><span class="dot green"></span> Akses</div>
        </div>
    </div>

    {{-- NOTIF LIST --}}
    <div class="notif-list" id="notifList">

        @forelse($notifikasi as $notif)

        <div class="notif-card {{ $notif['tipe'] }}" data-tipe="{{ $notif['tipe'] }}" data-dibaca="{{ $notif['dibaca'] ? 'ya' : 'tidak' }}">
            <div class="notif-header">
                <div class="notif-left">
                    <div class="notif-icon {{ $notif['tipe'] }}">
                        @if($notif['tipe'] === 'peringatan')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        @elseif($notif['tipe'] === 'kritis')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="notif-body">
                        <div class="notif-title">{{ $notif['judul'] }}</div>
                        <div class="notif-desc">{{ $notif['deskripsi'] }}</div>
                    </div>
                </div>
                <span class="badge-type {{ $notif['tipe'] }}">{{ ucfirst($notif['tipe']) }}</span>
            </div>

            @if(!empty($notif['meta']))
            <div class="notif-meta">
                @foreach($notif['meta'] as $i => $m)
                    @if($i > 0)<span class="meta-dot"></span>@endif
                    <span class="meta-detail">{{ $m }}</span>
                @endforeach
            </div>
            @endif

            <div class="notif-meta">
                <div class="notif-time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $notif['waktu'] }}
                </div>
            </div>
        </div>

        @empty
        <div style="text-align:center;padding:48px;color:var(--text-muted);">Tidak ada notifikasi.</div>
        @endforelse

    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/notifikasi-keamanan.js') }}"></script>
@endpush