@extends('layouts.app')

@section('title', 'Lokasi Brankas')
@section('page_title', 'Lokasi Brankas')
@section('page_subtitle', 'Monitoring lokasi brankas real-time dari GPS Neo-6M via ESP32')

@push('styles')
<style>
    /* Layout utama */
    .lokasi-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .map-card-header .gps-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .map-card-header .gps-badge.valid   { background: var(--green-light); color: var(--green-dark); }
    .map-card-header .gps-badge.invalid { background: #FEF2F2; color: var(--red); }

    #map {
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
        font-size: 20px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
        font-family: 'Courier New', monospace;
    }

    /* GPS Signal Info */
    .gps-signal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 12px;
    }

    .gps-signal-item {
        background: #F9FAFB;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 12px 14px;
    }

    .gps-signal-label {
        font-size: 11px;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .gps-signal-value {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
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
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-card.gps-ok {
        background: var(--green-light);
        border: 1px solid #BBF7D0;
        color: var(--green-dark);
    }

    .status-card.gps-warn {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        color: #92400E;
    }

    .status-card.gps-error {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: var(--red);
    }

    .status-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-dot.green {
        background: var(--green);
        box-shadow: 0 0 0 3px rgba(34,197,94,0.25);
        animation: pulse 2s infinite;
    }

    .status-dot.yellow {
        background: #F59E0B;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.25);
    }

    .status-dot.red {
        background: var(--red);
        box-shadow: 0 0 0 3px rgba(239,68,68,0.25);
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,0.25); }
        50%       { box-shadow: 0 0 0 6px rgba(34,197,94,0.1); }
    }

    /* Last update */
    .last-update {
        font-size: 12px;
        color: var(--text-muted);
        text-align: center;
        margin-top: 4px;
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

    .td-koordinat { display: flex; flex-direction: column; gap: 2px; font-size: 12.5px; font-weight: 500; font-family: 'Courier New', monospace; }

    .td-gps-info { display: flex; flex-direction: column; gap: 2px; font-size: 12px; }

    .badge-status {
        display: inline-flex; align-items: center;
        padding: 4px 12px; border-radius: 999px;
        font-size: 12px; font-weight: 600;
    }

    .badge-status.normal  { background: var(--green-light); color: var(--green-dark); }
    .badge-status.waspada { background: #FFFBEB; color: #92400E; }
    .badge-status.bahaya  { background: #FEF2F2; color: var(--red); }

    .no-data {
        text-align: center;
        padding: 48px 32px;
        color: var(--text-muted);
    }

    .no-data svg { width: 48px; height: 48px; stroke: var(--border); margin-bottom: 12px; }
    .no-data p { font-size: 14px; }

    @media (max-width: 1024px) {
        .lokasi-grid { grid-template-columns: 1fr; }
        #map { height: 360px; }
    }
</style>
@endpush

@section('content')

    @php
        // Koordinat default jika belum ada data GPS
        $defaultLat = $brankas?->latitude ?? -6.9105;
        $defaultLng = $brankas?->longitude ?? 109.1479;
        $gpsValid   = $brankas?->isGpsValid() ?? false;
        $isMoving   = $brankas?->isMoving() ?? false;
    @endphp

    {{-- MAP + INFO --}}
    <div class="lokasi-grid">

        {{-- MAP --}}
        <div class="map-card">
            <div class="map-card-header">
                <span>Lokasi Saat Ini</span>
                @if($brankas)
                    @if($gpsValid)
                        <span class="gps-badge valid">
                            GPS Fix — {{ $brankas->satellites }} satelit
                        </span>
                    @else
                        <span class="gps-badge invalid">
                            GPS Tidak Valid
                        </span>
                    @endif
                @else
                    <span class="gps-badge invalid">Belum Ada Data</span>
                @endif
            </div>
            {{-- Peta dinamis menggunakan OpenStreetMap (Leaflet) --}}
            <div id="map"></div>
        </div>

        {{-- INFO KOORDINAT --}}
        <div class="info-col">

            <div class="info-card">
                <div class="info-card-title">Koordinat GPS Neo-6M</div>

                <div class="coord-box">
                    <div class="coord-label">Latitude</div>
                    <div class="coord-value" id="val-lat">
                        {{ $brankas?->latitude ? number_format((float)$brankas->latitude, 8) . '°' : '—' }}
                    </div>
                </div>

                <div class="coord-box">
                    <div class="coord-label">Longitude</div>
                    <div class="coord-value" id="val-lng">
                        {{ $brankas?->longitude ? number_format((float)$brankas->longitude, 8) . '°' : '—' }}
                    </div>
                </div>

                @if($brankas?->altitude !== null)
                <div class="coord-box">
                    <div class="coord-label">Altitude</div>
                    <div class="coord-value">{{ number_format((float)$brankas->altitude, 1) }} m</div>
                </div>
                @endif

                {{-- Kualitas sinyal GPS --}}
                <div class="gps-signal-grid">
                    <div class="gps-signal-item">
                        <div class="gps-signal-label">Satelit</div>
                        <div class="gps-signal-value" id="val-sat">
                            {{ $brankas?->satellites ?? '—' }}
                        </div>
                    </div>
                    <div class="gps-signal-item">
                        <div class="gps-signal-label">HDOP</div>
                        <div class="gps-signal-value" id="val-hdop">
                            {{ $brankas?->hdop ? number_format((float)$brankas->hdop, 2) : '—' }}
                        </div>
                    </div>
                    <div class="gps-signal-item">
                        <div class="gps-signal-label">Akurasi</div>
                        <div class="gps-signal-value" style="font-size:13px;">
                            {{ $brankas?->hdopLabel() ?? '—' }}
                        </div>
                    </div>
                    <div class="gps-signal-item">
                        <div class="gps-signal-label">Kecepatan</div>
                        <div class="gps-signal-value" id="val-speed">
                            {{ $brankas?->speed_kmh !== null ? number_format((float)$brankas->speed_kmh, 1) . ' km/h' : '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-title">Brankas</div>
                <div class="brankas-info">
                    <div class="brankas-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                        <span>{{ $brankas?->nama_brankas ?? 'Belum ada brankas terdaftar' }}</span>
                    </div>
                    @if($brankas?->lokasi)
                    <div class="brankas-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        <span>{{ $brankas->lokasi }}</span>
                    </div>
                    @endif
                    @if($brankas?->kode_brankas)
                    <div class="brankas-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/>
                        </svg>
                        <span>Kode: <strong>{{ $brankas->kode_brankas }}</strong></span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Status GPS --}}
            @if(!$brankas || !$gpsValid)
                <div class="status-card gps-error">
                    <span class="status-dot red"></span>
                    GPS Tidak Valid — Menunggu Fix
                </div>
            @elseif($isMoving)
                <div class="status-card gps-warn">
                    <span class="status-dot yellow"></span>
                    ⚠ Brankas Bergerak — {{ number_format((float)$brankas->speed_kmh, 1) }} km/h
                </div>
            @else
                <div class="status-card gps-ok">
                    <span class="status-dot green"></span>
                    GPS Aktif — Lokasi Terdeteksi
                </div>
            @endif

            @if($brankas?->last_gps_update)
            <p class="last-update">
                Update terakhir: {{ $brankas->last_gps_update->diffForHumans() }}
            </p>
            @endif

        </div>
    </div>

    {{-- HISTORY TABLE --}}
    <div class="history-card">
        <div class="history-card-header">Riwayat Posisi GPS</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><span class="sort">WAKTU</span></th>
                        <th><span class="sort">BRANKAS</span></th>
                        <th><span class="sort">KOORDINAT</span></th>
                        <th><span class="sort">SINYAL GPS</span></th>
                        <th><span class="sort">GETARAN</span></th>
                        <th><span class="sort">KECEPATAN</span></th>
                        <th><span class="sort">STATUS</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $h)
                    <tr>
                        <td class="td-waktu">{{ $h->recorded_at->format('d/m/Y H:i:s') }}</td>
                        <td class="td-brankas">{{ $h->brankas?->nama_brankas ?? '-' }}</td>
                        <td>
                            <div class="td-koordinat">
                                <span>{{ number_format((float)$h->latitude, 6) }}°</span>
                                <span>{{ number_format((float)$h->longitude, 6) }}°</span>
                                @if($h->altitude !== null)
                                <span style="color:var(--text-muted);">Alt: {{ number_format((float)$h->altitude, 1) }} m</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="td-gps-info">
                                <span>{{ $h->satellites ?? '—' }} satelit</span>
                                @if($h->hdop !== null)
                                <span style="color:var(--text-muted);">HDOP: {{ number_format((float)$h->hdop, 2) }}</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $h->getaran !== null ? number_format((float)$h->getaran, 2) . ' G' : '—' }}</td>
                        <td>{{ $h->speed_kmh !== null ? number_format((float)$h->speed_kmh, 1) . ' km/h' : '—' }}</td>
                        <td>
                            <span class="badge-status {{ $h->status }}">
                                {{ $h->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="no-data">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                                <p>Belum ada data GPS dari perangkat ESP32.</p>
                                <p style="font-size:12px;margin-top:4px;">Data akan muncul setelah ESP32 mengirim ke <code>POST /api/gps</code></p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
{{-- Leaflet.js untuk peta OpenStreetMap (tidak memerlukan API key) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WPeE=" crossorigin=""></script>
<script>
    const lat = {{ $defaultLat }};
    const lng = {{ $defaultLng }};
    const gpsValid = {{ $gpsValid ? 'true' : 'false' }};

    // Inisialisasi peta Leaflet
    const map = L.map('map').setView([lat, lng], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // Marker brankas
    const markerIcon = L.divIcon({
        html: `<div style="
            background: ${gpsValid ? '#22C55E' : '#EF4444'};
            width: 16px; height: 16px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        "></div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 8],
        className: '',
    });

    @if($brankas)
    const marker = L.marker([lat, lng], { icon: markerIcon })
        .addTo(map)
        .bindPopup(`
            <strong>{{ $brankas->nama_brankas }}</strong><br>
            Lat: ${lat.toFixed(6)}°<br>
            Lng: ${lng.toFixed(6)}°<br>
            @if($brankas->satellites)
            Satelit: {{ $brankas->satellites }}<br>
            @endif
            @if($brankas->hdop)
            HDOP: {{ number_format((float)$brankas->hdop, 2) }}<br>
            @endif
            Status: {{ ucfirst($brankas->status) }}
        `);
    @endif

    // Auto-refresh data GPS setiap 30 detik
    @if($brankas?->kode_brankas)
    setInterval(function() {
        fetch('/api/gps/{{ $brankas->kode_brankas }}')
            .then(r => r.json())
            .then(data => {
                if (data.latitude && data.longitude) {
                    const newLat = parseFloat(data.latitude);
                    const newLng = parseFloat(data.longitude);

                    // Update koordinat di UI
                    document.getElementById('val-lat').textContent = newLat.toFixed(8) + '°';
                    document.getElementById('val-lng').textContent = newLng.toFixed(8) + '°';
                    if (document.getElementById('val-sat'))
                        document.getElementById('val-sat').textContent = data.satellites ?? '—';
                    if (document.getElementById('val-hdop'))
                        document.getElementById('val-hdop').textContent = data.hdop ? parseFloat(data.hdop).toFixed(2) : '—';
                    if (document.getElementById('val-speed'))
                        document.getElementById('val-speed').textContent = data.speed_kmh ? parseFloat(data.speed_kmh).toFixed(1) + ' km/h' : '—';

                    // Update posisi marker di peta
                    if (typeof marker !== 'undefined') {
                        marker.setLatLng([newLat, newLng]);
                        map.panTo([newLat, newLng]);
                    }
                }
            })
            .catch(() => {}); // Abaikan error jaringan
    }, 30000);
    @endif
</script>
@endpush
