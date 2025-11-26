@extends('layouts.appPIC')

@section('title', 'Manajemen Pegawai')

@section('content')

<div class="max-w-5xl mx-auto px-5 py-8">

    {{-- Judul + Action Button --}}
    <div class="flex justify-between items-center mb-6 pb-4">
        <div>
            <h2 class="text-gray-700 text-2xl font-bold relative">
                Manajemen Pegawai
                <span class="absolute bottom-0 left-0 w-56 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
            </h2>
            <p class="text-gray-500 text-sm mt-2">Kelola data pegawai: tambah, lihat detail, atau hapus.</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openModal()"
                class="px-6 py-2 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                + Tambah
            </button>

            <button onclick="hapusTerpilih()"
                class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition flex items-center gap-2">
                <i class="fa-regular fa-trash-can"></i> Hapus
            </button>
        </div>
    </div>

    {{-- Pencarian --}}
    <div class="mb-5">
        <div class="bg-white rounded-xl p-3 shadow flex items-center gap-3">
            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
            <input id="keyword" type="text" class="flex-1 outline-none text-sm"
                placeholder="Cari nama / NIP / jabatan..." oninput="renderList()">
        </div>
    </div>

    {{-- Daftar Pegawai --}}
    <div id="list-pegawai" class="space-y-4"></div>

</div>


{{-- MODAL TAMBAH --}}
<div id="modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>

    <div class="relative max-w-lg mx-auto mt-24 bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Tambah Pegawai</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form onsubmit="tambahPegawai(event)" class="space-y-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nama Pegawai</label>
                <input id="f_nama"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">NIP</label>
                <input id="f_nip"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Jabatan</label>
                <input id="f_jabatan"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-600 focus:outline-none"
                    placeholder="Pegawai PIC / Pegawai PPK">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>

                <button
                    class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection



{{-- SCRIPT --}}
<script>
    let dataPegawai = [
        { id: 1, nama: "Amanda Atika Putri", nip: "000000000000000", jabatan: "Pegawai PPK" },
        { id: 2, nama: "Rani Dwi Cahyani", nip: "000000000000001", jabatan: "Pegawai PIC" },
        { id: 3, nama: "Indra Yudha Saputra", nip: "000000000000002", jabatan: "Pegawai PPK" },
    ];

    let selectedIds = new Set();

    function renderList() {
        const q = (document.getElementById('keyword')?.value || "").toLowerCase();
        const wrap = document.getElementById('list-pegawai');
        if (!wrap) return;

        const rows = dataPegawai
            .filter(p => [p.nama, p.nip, p.jabatan].join(' ').toLowerCase().includes(q))
            .map(p => `
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex justify-between items-center">

                    <div class="flex items-start gap-4">
                        <input type="checkbox" class="mt-2"
                            onchange="toggleSelect(${p.id}, this.checked)"
                            ${selectedIds.has(p.id) ? 'checked' : ''}>

                        <div>
                            <div class="text-blue-700 font-semibold text-lg">${p.nama}</div>
                            <div class="h-0.5 bg-blue-300 my-2 w-48"></div>
                            <div class="text-gray-600 text-sm">NIP ${p.nip}</div>
                            <div class="text-gray-600 text-sm">${p.jabatan}</div>
                        </div>
                    </div>

                    <button onclick="lihatDetail(${p.id})"
                        class="text-blue-700 font-medium hover:underline text-sm">
                        Lihat Detail
                    </button>
                </div>
            `).join('');

        wrap.innerHTML = rows || `<div class="text-gray-500 text-sm text-center py-4">Tidak ada data.</div>`;
    }

    function toggleSelect(id, state) {
        state ? selectedIds.add(id) : selectedIds.delete(id);
    }

    function hapusTerpilih() {
        if (!selectedIds.size) return alert("Pilih minimal satu pegawai.");
        if (confirm("Hapus pegawai terpilih?")) {
            dataPegawai = dataPegawai.filter(p => !selectedIds.has(p.id));
            selectedIds.clear();
            renderList();
        }
    }

    function lihatDetail(id) {
        const p = dataPegawai.find(x => x.id === id);
        if (p) alert(`${p.nama}\nNIP: ${p.nip}\nJabatan: ${p.jabatan}`);
    }

    function openModal() {
        document.getElementById('modal')?.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal')?.classList.add('hidden');
    }

    function tambahPegawai(e) {
        e.preventDefault();
        let nama = document.getElementById('f_nama')?.value.trim();
        let nip = document.getElementById('f_nip')?.value.trim();
        let jabatan = document.getElementById('f_jabatan')?.value.trim();

        if (!nama || !nip) return alert("Nama & NIP wajib diisi.");

        const id = Math.max(0, ...dataPegawai.map(p => p.id)) + 1;
        dataPegawai.unshift({ id, nama, nip, jabatan: jabatan || 'Pegawai' });

        closeModal();
        e.target.reset();
        renderList();
    }

    document.addEventListener('DOMContentLoaded', renderList);
</script>
