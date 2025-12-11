// ---- mulai penggantian popup ----
(function() {
    // ambil data fallback
    const nomorText    = point.nomor ?? '-';
    const tujuanText   = point.tujuan ?? '-';
    const jumlahText   = Number.isInteger(point.jumlah) ? point.jumlah : (point.jumlah ? point.jumlah : 1);
    const pegawaiCount = Array.isArray(point.pegawai) ? point.pegawai.length : (point.jumlah_pegawai ?? null);
    const waktuText    = point.waktu ? String(point.waktu) : null;
    const statusText   = point.status ?? point.status_perjadin ?? null;

    // format waktu singkat (jika ada)
    const waktuDisplay = waktuText ? waktuText.replace(' ', ' • ') : null;

    // bangun HTML popup — ringkas, mudah dibaca pimpinan
    let popupHtml = `
        <div style="min-width:180px; font-size:13px; line-height:1.25;">
            <div style="font-weight:700; margin-bottom:6px; color:#1f2937;">${nomorText}</div>
            <div style="color:#374151; margin-bottom:6px;">
                <strong>Tujuan:</strong> ${tujuanText}
            </div>
    `;

    if (statusText) {
        popupHtml += `
            <div style="margin-bottom:6px; color:#374151;">
                <strong>Status:</strong> ${statusText}
            </div>
        `;
    }

    popupHtml += `
            <div style="margin-bottom:4px; color:#374151;">
                <strong>Titik tagging:</strong> ${jumlahText}
            </div>
    `;

    if (pegawaiCount !== null) {
        popupHtml += `
            <div style="margin-bottom:6px; color:#374151;">
                <strong>Peserta:</strong> ${pegawaiCount}
            </div>
        `;
    }

    if (waktuDisplay) {
        popupHtml += `
            <div style="margin-bottom:8px; color:#6b7280;">
                <small>Terakhir: ${waktuDisplay}</small>
            </div>
        `;
    }

    // link detail (buka di tab baru). Asumsi route /pimpinan/detail/{id}
    const baseOrigin = window.location.origin || (window.location.protocol + '//' + window.location.host);
    const detailUrl = point.id_perjadin ? `${baseOrigin}/pimpinan/detail/${point.id_perjadin}` : '#';

    popupHtml += `
            <div style="margin-top:6px; text-align:right;">
                <a href="${detailUrl}" target="_blank" rel="noopener noreferrer"
                   style="display:inline-block;padding:6px 10px;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;font-size:12px;">
                   Lihat Detail
                </a>
            </div>
        </div>
    `;

    marker.bindPopup(popupHtml, { maxWidth: 260, className: 'popup-pimpinan' });
})();
 // ---- akhir penggantian popup ----
