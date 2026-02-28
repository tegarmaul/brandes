@extends('layouts.app')

@section('title', 'Lokasi Brankas')
@section('page_title', 'Lokasi Brankas')
@section('page_subtitle', 'Monitoring lokasi brankas real-time dari GPS Neo IoT')

@push('styles')
<style>
    /* Layout utama */
    .lokasi-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }

    /* Card Map */
    .map-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp 0.4s ease both;
    }

    .map-card-header {
        padding: 18px 20px 14px;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        border-bottom: 1px solid var(--border);
    }

    .map-card iframe {
        display: block;
        width: 100%;
        height: 480px;
        border: 0;
    }

    /* Card Koordinat */
    .info-col {
        display: flex;
        flex-direction: column;
        gap: 16px;
        animation: fadeUp 0.4s ease 0.08s both;
    }

    .info-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
    }

    .info-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 16px;
    }

    /* Koordinat */
    .coord-box {
        background: #F9FAFB;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }

    .coord-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 4px;
    }

    .coord-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
    }

    /* Brankas info */
    .brankas-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .brankas-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13.5px;
        color: var(--text);
        line-height: 1.5;
    }

    .brankas-row svg {
        width: 18px; height: 18px;
        stroke: var(--text-muted);
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Status card */
    .status-card {
        background: var(--green-light);
        border: 1px solid #BBF7D0;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--green-dark);
    }

    .status-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: var(--green);
        flex-shrink: 0;
        box-shadow: 0 0 0 3px rgba(34,197,94,0.25);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,0.25); }
        50%       { box-shadow: 0 0 0 6px rgba(34,197,94,0.1); }
    }

    /* History Table */
    .history-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp 0.4s ease 0.16s both;
    }

    .history-card-header {
        padding: 18px 20px 14px;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        border-bottom: 1px solid var(--border);
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

    tbody td { padding: 15px 20px; font-size: 13.5px; color: var(--text); }

    .td-waktu   { font-weight: 500; white-space: nowrap; }
    .td-brankas { font-weight: 600; }

    .td-koordinat { display: flex; flex-direction: column; gap: 2px; font-size: 12.5px; font-weight: 500; }

    .status-normal  { color: var(--green-dark); font-weight: 700; font-size: 13px; }
    .status-waspada { color: #D97706; font-weight: 700; font-size: 13px; }
    .status-bahaya  { color: var(--red); font-weight: 700; font-size: 13px; }

    @media (max-width: 1024px) {
        .lokasi-grid { grid-template-columns: 1fr; }
        .map-card iframe { height: 360px; }
    }
</style>
@endpush

@section('content')

    {{-- MAP + INFO --}}
    <div class="lokasi-grid">

        {{-- MAP --}}
        <div class="map-card">
            <div class="map-card-header">Lokasi Saat Ini</div>
            <iframe
                src="https://maps.google.com/maps?q=Balai+Desa+Bengle+Kecamatan+Talang+Kabupaten+Tegal+Jawa+Tengah&t=&z=17&ie=UTF8&iwloc=&output=embed"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        {{-- INFO KOORDINAT --}}
        <div class="info-col">

            <div class="info-card">
                <div class="info-card-title">Koordinat detail GPS</div>

                <div class="coord-box">
                    <div class="coord-label">Latitude</div>
                    <div class="coord-value">- 6.9105°</div>
                </div>

                <div class="coord-box">
                    <div class="coord-label">Longtitude</div>
                    <div class="coord-value">109.1479°</div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-title">Brankas</div>
                <div class="brankas-info">
                    <div class="brankas-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                        <span>Balai Desa Bengle</span>
                    </div>
                    <div class="brankas-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        <span>Jl. Projosumarto II No. 16, Bengledukuh, Desa Bengle, Kecamatan Talang, Kabupaten Tegal, Kode Pos 52193</span>
                    </div>
                </div>
            </div>

            <div class="status-card">
                <span class="status-dot"></span>
                GPS Aktif — Lokasi Terdeteksi
            </div>

        </div>
    </div>

    {{-- HISTORY TABLE --}}
    <div class="history-card">
        <div class="history-card-header">History Saat Ini</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><span class="sort">WAKTU <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">BRANKAS <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">KOORDINAT <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">GETARAN <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                        <th><span class="sort">STATUS <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/></svg></span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $h)
                    <tr>
                        <td class="td-waktu">{{ $h['waktu'] }}</td>
                        <td class="td-brankas">{{ $h['brankas'] }}</td>
                        <td>
                            <div class="td-koordinat">
                                <span>{{ $h['lat'] }}°</span>
                                <span>{{ $h['lng'] }}°</span>
                            </div>
                        </td>
                        <td>{{ $h['getaran'] }}</td>
                        <td>
                            <span class="status-{{ strtolower($h['status']) }}">{{ $h['status'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada history lokasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection