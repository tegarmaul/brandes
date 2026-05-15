/**
 * ==========================================================================
 * lokasi.js
 * Deskripsi: Mengelola fitur pelacakan GPS real-time menggunakan pustaka 
 *            Leaflet.js. Melakukan pembaruan koordinat dan marker secara 
 *            otomatis melalui polling API.
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Validasi Awal (Pastikan Leaflet dan Config Tersedia)
    if (typeof L === 'undefined' || !window.GPS_CONFIG) return;

    const config = window.GPS_CONFIG;
    const { lat, lng, gpsValid, namaBrankas, kodeBrankas } = config;

    // ==========================================================================
    // 1. INISIALISASI PETA (LEAFLET.JS)
    // ==========================================================================

    // Inisialisasi Kontainer Peta
    const map = L.map('map').setView([lat, lng], 17);

    // Memuat Layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // Konfigurasi Ikon Marker Kustom (CSS-based)
    const markerIcon = L.divIcon({
        html: `<div style="
            background: ${gpsValid ? '#22C55E' : '#EF4444'};
            width: 16px; height: 16px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        "></div>`,
        iconSize:   [16, 16],
        iconAnchor: [8, 8],
        className:  '',
    });

    let marker;

    // Tambahkan Marker & Popup jika data lokasi awal tersedia
    if (namaBrankas) {
        let popupContent = `<strong>${namaBrankas}</strong><br>
            Lat: ${lat.toFixed(6)}°<br>
            Lng: ${lng.toFixed(6)}°<br>`;

        if (config.satellites !== null) {
            popupContent += `Satelit: ${config.satellites}<br>`;
        }
        if (config.hdop !== null) {
            popupContent += `HDOP: ${config.hdop.toFixed(2)}<br>`;
        }
        popupContent += `Status: ${config.status.charAt(0).toUpperCase() + config.status.slice(1)}`;

        marker = L.marker([lat, lng], { icon: markerIcon })
            .addTo(map)
            .bindPopup(popupContent);
    }

    // ==========================================================================
    // 2. LOGIKA POLLING REALTIME (GPS UPDATE)
    // ==========================================================================

    if (kodeBrankas) {
        // Lakukan pengambilan data GPS setiap 30 detik
        setInterval(() => {
            fetch(`/api/gps/${kodeBrankas}`)
                .then(r => r.json())
                .then(data => {
                    if (data.latitude && data.longitude) {
                        const newLat = parseFloat(data.latitude);
                        const newLng = parseFloat(data.longitude);

                        // A. Perbarui Statistik Teks di UI
                        updateUIElement('val-lat', newLat.toFixed(8) + '°');
                        updateUIElement('val-lng', newLng.toFixed(8) + '°');
                        
                        if (data.satellites !== undefined) {
                            updateUIElement('val-sat', data.satellites ?? '—');
                        }
                        
                        if (data.hdop) {
                            updateUIElement('val-hdop', parseFloat(data.hdop).toFixed(2));
                        }
                        
                        if (data.speed_kmh) {
                            updateUIElement('val-speed', parseFloat(data.speed_kmh).toFixed(1) + ' km/h');
                        }

                        // B. Sinkronisasi Posisi Marker di Peta
                        if (marker) {
                            marker.setLatLng([newLat, newLng]);
                            map.panTo([newLat, newLng]); // Geser peta mengikuti koordinat baru
                        }
                    }
                })
                .catch(() => {
                    // Gagal ambil data (silent error untuk menjaga UX)
                });
        }, 30000);
    }
});


// ==========================================================================
// 3. UTILITAS UI
// ==========================================================================

/**
 * Memperbarui konten teks elemen DOM secara aman jika elemen ditemukan
 * @param {string} id    - ID elemen target
 * @param {string} value - Nilai teks baru
 */
function updateUIElement(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}