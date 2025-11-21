@php
    $role = auth()->user()->role_kode ? strtoupper(auth()->user()->role_kode) : null;
@endphp

@switch($role)
    @case('PEGAWAI')
        @include('partials.sidebarPegawai')
        @break
    @case('PIC')
        @include('partials.sidebarPIC')
        @break
    @case('PIMPINAN')
        @include('partials.sidebarPimpinan')
        @break
    @case('PPK')
        @include('partials.sidebarPPK')
        @break
    @default
        <p>Role tidak terdeteksi</p>
@endswitch
