@props(['desktopOnly' => false])

@if($desktopOnly)
    <div id="desktopBanner" class="desktop-only-banner">
        ⚠️ <b>Fitur Desktop .</b>
        Halaman ini optimal jika diakses melalui Desktop / Laptop.
    </div>

    <style>
        .desktop-only-banner {
            display: block;
            position: fixed;
            top: 57px;
            left: 0;
            right: 0;
            z-index: 20;
            background: #FEF3C7;
            border-bottom: 1px solid #FCD34D;
            padding: 12px 16px;
            font-size: 14px;
            color: #92400E;
            pointer-events: none;
        }

        /* Pada layar sm+ (640px), sidebar muncul, jadi beri offset */
        @media (min-width: 640px) {
            .desktop-only-banner {
                left: 60px;
            }
        }

        /* Pada layar lg+ (1024px), sembunyikan karena sudah desktop */
        @media (min-width: 1024px) {
            .desktop-only-banner {
                display: none !important;
            }
        }
    </style>
@endif