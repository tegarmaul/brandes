@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Sistem Keamanan Brankas BRANDES.')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .stat-card {
        background: #fff;
        border: 1.9px solid var(--border);
        border-radius: 12px;
        padding: 26px 26px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        animation: fadeUp 0.4s ease both;
    }

    .stat-card:nth-child(2) { 
        animation-delay: 0.08s;
    }

    .stat-card:nth-child(3) { 
        animation-delay: 0.16s; 
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-label { 
        font-size: 16px; 
        font-weight: 500; 
        color: var(--text-muted); 
    }

    .stat-icon {
        width: 52px; height: 52px;
        background: var(--green-light);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon svg { 
        width: 28px; 
        height: 28px; 
        stroke: var(--green-dark); 
    }

    .stat-value { 
        font-size: 34px; 
        font-weight: 800; 
        line-height: 1; 
    }

    .stat-footer { 
        font-size: 16px; 
        color: var(--text-muted); 
        display: flex; align-items: 
        center; gap: 6px; 
    }

    .toggle-pill {
        width: 84px; 
        height: 42px;
        background: var(--green);
        border-radius: 999px;
        position: relative;
        cursor: pointer;
        transition: background 0.2s;
        border: none;
    }

    .toggle-pill::after {
        content: '';
        position: absolute;
        top: 4px; 
        left: 46px;
        width: 34px; 
        height: 34px;
        background: white;
        border-radius: 50%;
        transition: left 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }

    .toggle-pill.off { 
        background: #D1D5DB; 
    }

    .toggle-pill.off::after { 
        left: 4px; 
    }

    /* Dashboard grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 16px;
        align-items: start;
    }

    /* Card */
    .card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        animation: fadeUp 0.4s ease both;
    }

    /* Card header base */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    }

    /* Clickable header — pakai tag <a> */
    a.card-header {
        cursor: pointer;
    }

    a.card-header:hover {
        background: #F9FAFB;
    }

    a.card-header:hover .card-arrow svg {
        stroke: var(--green);
        transform: translateX(3px);
    }

    .card-title { 
        font-size: 14px; 
        font-weight: 700; 
    }

    .card-arrow svg {
        width: 18px; height: 18px;
        stroke: var(--text-muted);
        transition: transform 0.2s, stroke 0.2s;
    }

    /* Map */
    .map-wrap {
        width: 100%;
        height: 240px;
        overflow: hidden;
        padding: 12px 16px 0;
    }

    .map-wrap iframe {
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 12px;
    }

    .brankas-info {
        margin: 12px 16px;
        padding: 16px;
        background: #F9FAFB;
        border: 1px solid var(--border);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .brankas-info-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 2px;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .info-row svg {
        width: 18px; height: 18px;
        flex-shrink: 0;
        margin-top: 1px;
        stroke: var(--text);
    }

    .info-coords {
        padding: 12px 20px 16px;
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .info-coords strong {
        color: var(--text);
        font-weight: 700;
    }

    /* History Search */
    .history-search {
        padding: 14px 16px;
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--border);
    }

    .search-box {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 7px 10px;
        transition: border-color 0.2s;
    }

    .search-box:focus-within { 
        border-color: var(--green); 
    }
    .search-box svg { 
        width: 14px; 
        height: 14px; 
        flex-shrink: 0; 
        stroke: var(--text-muted); 
    }

    .search-box input {
        border: none; outline: none;
        font-family: inherit;
        font-size: 12.5px;
        color: var(--text);
        width: 100%;
        background: transparent;
    }

    /* Date picker — icon custom, bawaan browser disembunyikan */
    .date-box {
        position: relative;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 7px 10px;
        cursor: pointer;
        white-space: nowrap;
        transition: border-color 0.2s;
        user-select: none;
    }

    .date-box:hover { 
        border-color: var(--green); 
    }

    .date-box svg { 
        width: 14px; 
        height: 14px; 
        stroke: var(--text-muted); 
        flex-shrink: 0; 
    }

    .date-box input[type="date"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    input[type="date"]::-webkit-calendar-picker-indicator { display: none; }
    input[type="date"]::-webkit-inner-spin-button          { display: none; }

    /* History Items */
    .history-item {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: background 0.15s;
    }

    .history-item:hover { 
        background: #fafafa; 
    }

    .history-item:last-child { 
        border-bottom: none; 
    }

    .history-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-user {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 600;
    }

    .history-user svg { 
        width: 18px; 
        height: 18px; 
    }

    .history-badge {
        font-size: 10px; font-weight: 700;
        padding: 3px 8px;
        border-radius: 5px;
        background: var(--green-light);
        color: var(--green-dark);
    }

    .history-badge.unknown { 
        background: #FEF2F2; 
        color: var(--red); 
    }

    .history-action { 
        font-size: 12.5px; 
        color: var(--text-muted); 
        padding-left: 26px; 
    }

    .history-time {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .history-time svg { 
        width: 13px; 
        height: 13px; 
        stroke: var(--text-muted); 
    }

    /* Empty state */
    .history-empty {
        display: none;
        padding: 28px 16px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
    }

    /* Notifikasi */
    .notif-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 16px 20px;
    }

    .notif-item {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .notif-item.red    { 
        border-color: #FECACA; 
        background: #FEF2F2; 
    }

    .notif-item.yellow { 
        border-color: #FDE68A; 
        background: #FEFCE8;
     
    }

    .notif-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
    }

    .notif-title-row {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 13.5px;
        font-weight: 700;
    }

    .notif-title-row svg { 
        width: 16px; 
        height: 16px; 
    }

    .notif-tag {
        font-size: 10px; font-weight: 800;
        padding: 3px 9px;
        border-radius: 5px;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .notif-tag.peringatan { 
        background: var(--red); 
        color: white; 
    }

    .notif-tag.waspada { 
        background: #EAB308; 
        color: white; 
    }

    .notif-desc { 
        font-size: 12.5px; 
        color: var(--text-muted); 
        line-height: 1.5; 
    }

    .notif-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--text-muted);
        flex-wrap: wrap;
    }

    .notif-time {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .notif-time svg { 
        width: 13px; 
        height: 13px; 
        stroke: var(--text-muted); 
    }

    @media (max-width: 1024px) {
        .dashboard-grid { grid-template-columns: 1fr; }
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .notif-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    {{-- STAT CARDS --}}
    <div class="stat-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Status Brankas</span>
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                        <path d="M15.2424 16.7561L26.3619 5.63672L28.365 7.63989L26.3619 9.64447L29.8667 13.1493L27.8635 15.1539L24.3573 11.6476L22.3541 13.6508L25.3589 16.6556L23.3557 18.6601L20.3509 15.654L17.2456 18.7593C18.2081 20.2242 18.5936 21.9938 18.3275 23.7263C18.0615 25.4588 17.1627 27.0312 15.8049 28.1397C14.4472 29.2483 12.7267 29.8142 10.976 29.7283C9.22532 29.6423 7.5686 28.9106 6.32594 27.6744C5.08245 26.4335 4.34446 24.7747 4.25524 23.0203C4.16602 21.2658 4.73195 19.5408 5.84315 18.1801C6.95434 16.8195 8.53151 15.9203 10.2684 15.6571C12.0053 15.394 13.7781 15.7857 15.2424 16.7561ZM14.34 25.6712C14.7527 25.2809 15.083 24.8117 15.3113 24.2916C15.5395 23.7714 15.6612 23.2107 15.6691 22.6427C15.677 22.0747 15.571 21.5109 15.3572 20.9845C15.1435 20.4582 14.8264 19.9801 14.4247 19.5784C14.0231 19.1767 13.5449 18.8596 13.0186 18.6459C12.4923 18.4322 11.9285 18.3261 11.3604 18.334C10.7924 18.3419 10.2318 18.4636 9.7116 18.6919C9.19142 18.9202 8.72229 19.2504 8.33194 19.6631C7.55776 20.4647 7.12939 21.5383 7.13907 22.6526C7.14875 23.7669 7.59572 24.8329 8.38371 25.6209C9.17169 26.4088 10.2376 26.8558 11.352 26.8655C12.4663 26.8752 13.5399 26.4468 14.3414 25.6726" fill="#00A63E"/>
                    </svg>
                </div>
            </div>
            <button class="toggle-pill" id="statusToggle" onclick="toggleStatus()"></button>
            <div class="stat-footer">Sistem IOT Brankas</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Total Akses Hari ini</span>
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M14 7V14L18.6667 16.3333" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.9987 25.6663C20.442 25.6663 25.6654 20.443 25.6654 13.9997C25.6654 7.55635 20.442 2.33301 13.9987 2.33301C7.55538 2.33301 2.33203 7.55635 2.33203 13.9997C2.33203 20.443 7.55538 25.6663 13.9987 25.6663Z" stroke="#00A63E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">24 ×</div>
            <div class="stat-footer">
                <svg viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" style="width:14px;height:14px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                </svg>
                <span style="color:#22C55E;font-weight:600;">+8%</span>&nbsp;Naik aksesnya
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Akses Terakhir</span>
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M3.5 14C3.5 16.0767 4.11581 18.1068 5.26957 19.8335C6.42332 21.5602 8.0632 22.906 9.98182 23.7007C11.9004 24.4955 14.0116 24.7034 16.0484 24.2982C18.0852 23.8931 19.9562 22.8931 21.4246 21.4246C22.8931 19.9562 23.8931 18.0852 24.2982 16.0484C24.7034 14.0116 24.4955 11.9004 23.7007 9.98182C22.906 8.0632 21.5602 6.42332 19.8335 5.26957C18.1068 4.11581 16.0767 3.5 14 3.5C11.0646 3.51104 8.24713 4.65643 6.13667 6.69667L3.5 9.33333" stroke="#00A63E" stroke-width="2.24" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 4V9.83333H8.83333" stroke="#00A63E" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 8.16699V14.0003L18.6667 16.3337" stroke="#00A63E" stroke-width="2.24" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" style="font-size:22px;">Sodikin</div>
            <div class="stat-footer">5 Menit yang lalu</div>
        </div>

    </div>

    {{-- DASHBOARD GRID --}}
    <div class="dashboard-grid">

        {{-- MAP CARD --}}
        <div class="card">

            {{-- Header → klik ke Lokasi Brankas --}}
            <a href="{{ route('lokasi.brankas') }}" class="card-header">
                <span class="card-title">Lokasi Brankas</span>
                <span class="card-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </span>
            </a>

            <div class="map-wrap">
                <iframe
                    src="https://maps.google.com/maps?q=Balai+Desa+Bengle+Kecamatan+Talang+Kabupaten+Tegal+Jawa+Tengah&t=&z=17&ie=UTF8&iwloc=&output=embed"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="brankas-info">
                <div class="brankas-info-title">Brankas</div>
                <div class="info-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                    Balai Desa Bengle
                </div>
                <div class="info-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z"/>
                    </svg>
                    Jl. Projosumarto II No. 16, Bengledukuh, Desa Bengle, Kecamatan Talang, Kabupaten Tegal, Kode Pos 52193
                </div>
            </div>

            <div class="info-coords">
                <span>Latitude &nbsp;<strong>- 6.9105</strong></span>
                <span>●</span>
                <span>Longitude &nbsp;<strong>109.1479°</strong></span>
            </div>
        </div>

        {{-- HISTORY AKSES --}}
        <div class="card">

            {{-- Header → klik ke History Akses --}}
            <a href="{{ route('history.akses') }}" class="card-header">
                <span class="card-title">History Akses Terbaru</span>
                <span class="card-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </span>
            </a>

            {{-- Search + Date Filter --}}
            <div class="history-search">
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" id="historySearch" placeholder="Cari History..." oninput="filterHistory()">
                </div>

                <div class="date-box" onclick="openDatePicker()">
                    <span id="dateLabel" style="font-size:12.5px;color:var(--text-muted);">mm/dd/yyyy</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <input type="date" id="historyDate" onchange="filterHistory()">
                </div>
            </div>

            {{-- History Items --}}
            <div id="historyList">

                {{-- Item: Sodikin --}}
                <div class="history-item" data-name="sodikin" data-date="2026-01-31">
                    <div class="history-item-top">
                        <div class="history-user">
                            <div class="history-icon success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="history-user-info">
                                <span class="history-user-name">Sodikin</span>
                                <span class="history-action">Membuka Brankas</span>
                            </div>
                        </div>
                        <span class="history-badge">FP-001-A2F3</span>
                    </div>
                    <div class="history-divider"></div>
                    <div class="history-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        5 Menit yang lalu
                    </div>
                </div>

                {{-- Item: Ikhwanudin --}}
                <div class="history-item" data-name="ikhwanudin" data-date="2026-01-31">
                    <div class="history-item-top">
                        <div class="history-user">
                            <div class="history-icon success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="history-user-info">
                                <span class="history-user-name">Ikhwanudin</span>
                                <span class="history-action">Membuka Brankas</span>
                            </div>
                        </div>
                        <span class="history-badge">FP-001-A2F3</span>
                    </div>
                    <div class="history-divider"></div>
                    <div class="history-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        5 Menit yang lalu
                    </div>
                </div>

                {{-- Item: Unknown User --}}
                <div class="history-item" data-name="unknown user" data-date="2026-01-30">
                    <div class="history-item-top">
                        <div class="history-user">
                            <div class="history-icon danger">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                </svg>
                            </div>
                            <div class="history-user-info">
                                <span class="history-user-name">Unknown User</span>
                                <span class="history-action">Percobaan Akses</span>
                            </div>
                        </div>
                        <span class="history-badge unknown">FP-XXX-XXXX</span>
                    </div>
                    <div class="history-divider"></div>
                    <div class="history-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        1 Hari yang lalu
                    </div>
                </div>

                {{-- Item: Sodikin --}}
                <div class="history-item" data-name="sodikin" data-date="2026-01-30">
                    <div class="history-item-top">
                        <div class="history-user">
                            <div class="history-icon success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="history-user-info">
                                <span class="history-user-name">Sodikin</span>
                                <span class="history-action">Membuka Brankas</span>
                            </div>
                        </div>
                        <span class="history-badge">FP-001-A2F3</span>
                    </div>
                    <div class="history-divider"></div>
                    <div class="history-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        1 Hari yang lalu
                    </div>
                </div>

                {{-- Item: Ikhwanudin --}}
                <div class="history-item" data-name="ikhwanudin" data-date="2026-01-29">
                    <div class="history-item-top">
                        <div class="history-user">
                            <div class="history-icon success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="history-user-info">
                                <span class="history-user-name">Ikhwanudin</span>
                                <span class="history-action">Menutup Brankas</span>
                            </div>
                        </div>
                        <span class="history-badge">FP-002-B1C4</span>
                    </div>
                    <div class="history-divider"></div>
                    <div class="history-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        2 Hari yang lalu
                    </div>
                </div>

            </div>

            {{-- Empty state --}}
            <div class="history-empty" id="historyEmpty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;height:32px;margin:0 auto 8px;display:block;stroke:#D1D5DB">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                Tidak ada data ditemukan
            </div>

        </div>
    </div>

    {{-- NOTIFIKASI --}}
    <div class="card">

        {{-- Header → klik ke Notifikasi Keamanan --}}
        <a href="{{ route('notifikasi.keamanan') }}" class="card-header">
            <span class="card-title">Notifikasi Keamanan Terbaru</span>
            <span class="card-arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </span>
        </a>

        <div class="notif-grid">
            <div class="notif-item red">
                <div class="notif-top">
                    <div class="notif-title-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        Percobaan Akses
                    </div>
                    <span class="notif-tag peringatan">Peringatan</span>
                </div>
                <div class="notif-desc">Percobaan paksa membuka brankas terdeteksi. Sistem keamanan aktif.</div>
                <div class="notif-meta">
                    <span>FP-XXX-XXXX</span><span>●</span>
                    <span>Unknown user</span><span>●</span>
                    <span>3x percobaan</span>
                </div>
                <div class="notif-time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    2026-01-31 13:30:45
                </div>
            </div>
            <div class="notif-item yellow">
                <div class="notif-top">
                    <div class="notif-title-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#EAB308" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        Percobaan Pembobolan
                    </div>
                    <span class="notif-tag waspada">Waspada</span>
                </div>
                <div class="notif-desc">Percobaan pembobolan brankas terdeteksi oleh sensor gerak.</div>
                <div class="notif-meta">
                    <span>FP-XXX-XXXX</span><span>●</span>
                    <span>Unknown user</span><span>●</span>
                    <span>1x percobaan</span>
                </div>
                <div class="notif-time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    2026-01-31 13:30:45
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/dashboard-admin.js') }}"></script>
@endpush