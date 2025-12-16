@props(['desktopOnly' => false])

@if($desktopOnly)
<div
    id="desktopBanner"
    class="desktop-only-banner"
    style="
            position: fixed;
            top: 57px;
            left: 0;
            right: 0;
            z-index: 50;
            background: #FEF3C7;
            border-bottom: 1px solid #FCD34D;
            padding: 12px 16px;
            font-size: 14px;
            color: #92400E;
        "
>
    ⚠️ <b>Fitur Desktop .</b>
    Halaman ini optimal jika diakses melalui Desktop / Laptop.
</div>

<style>
.desktop-only-banner {
    display: block;
}

@media (min-width: 1024px) {
    .desktop-only-banner {
        display: none !important;
    }
}
</style>
@endif
