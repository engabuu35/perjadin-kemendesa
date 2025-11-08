@extends('layouts.app')

@section('content')

{{-- Style kustom dari file HTML Anda --}}
{{-- Ini diletakkan di dalam @section('content') --}}
{{-- agar mengikuti pola file 'detailperjadin.blade.php' Anda --}}
<style>
  /* Menghapus style body, navbar, sidebar, dll.
    Hanya menyisakan style yang spesifik untuk halaman ini
  */
  .sidebar {
    transition: width .3s ease
  }

  .sidebar-text {
    opacity: 0;
    transition: opacity .3s
  }

  .sidebar.active .sidebar-text {
    opacity: 1
  }
</style>

{{-- 
  Konten <main> dari file HTML Anda.
  class="ml-[80px]" PENTING untuk memberi ruang bagi sidebar.
  Ini juga mengikuti pola 'detailperjadin.blade.php'
--}}
<main class="item-center max-w-5xl mx-auto px-5 py-8">
  <!-- Judul + action -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
      <h2 class="text-gray-700 text-2xl font-bold pb-3 relative">
        Manajemen Pegawai
        <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
      </h2>
      <p class="text-gray-500 text-sm">Kelola data pegawai: tambah, lihat detail, atau hapus.</p>
    </div>
    <div class="flex items-center gap-3">
      <button class="px-5 py-2 border-2 border-dashed border-blue-600 text-blue-700 rounded-2xl hover:bg-blue-50"
        onclick="openModal()">+ Tambah</button>
      <button class="px-5 py-2 border-2 border-dashed border-blue-600 text-blue-700 rounded-2xl hover:bg-blue-50"
        onclick="hapusTerpilih()">
        <i class="fa-regular fa-trash-can mr-2"></i>Hapus
      </button>
    </div>
  </div>

  <!-- Pencarian -->
  <div class="mb-5">
    <div class="bg-white rounded-xl p-3 shadow flex items-center gap-3">
      <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
      <input id="keyword" type="text" class="flex-1 outline-none text-sm"
        placeholder="Cari nama / NIP / jabatan..." oninput="renderList()">
    </div>
  </div>

  <!-- Daftar Pegawai -->
  <section id="list-pegawai" class="space-y-4"></section>
</main>

<!-- Modal Tambah (diletakkan di dalam section agar tidak error) -->
<div id="modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
  <div class="relative max-w-lg mx-auto mt-20 bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-gray-800">Tambah Pegawai</h3>
      <button class="text-gray-400 hover:text-gray-600" onclick="closeModal()"><i
          class="fa-solid fa-xmark"></i></button>
    </div>
    <form onsubmit="tambahPegawai(event)" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Nama Pegawai</label>
        <input id="f_nama"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">NIP</label>
        <input id="f_nip"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Jabatan</label>
        <input id="f_jabatan"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600"
          placeholder="Pegawai PIC / Pegawai PPK">
      </div>
      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
          onclick="closeModal()">Batal</button>
        <button class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800">Simpan</button>
      </div>
    </form>
  </div>
</div>

@endsection

{{-- 
  Script diletakkan SETELAH @endsection,
  mengikuti pola file 'detailperjadin.blade.php' Anda
--}}
<script>
  // --- Data dummy (ganti dengan data backend jika perlu) ---
  let dataPegawai = [
    { id: 1, nama: "Amanda Atika Putri", nip: "000000000000000", jabatan: "Pegawai PPK" },
    { id: 2, nama: "Rani Dwi Cahyani", nip: "000000000000001", jabatan: "Pegawai PIC" },
    { id: 3, nama: "Indra Yudha Saputra", nip: "000000000000002", jabatan: "Pegawai PPK" },
  ];
  let selectedIds = new Set();

  // --- Render list ---
  function renderList() {
    // Pastikan elemen #keyword ada sebelum mengakses .value
    const keywordEl = document.getElementById('keyword');
    const q = (keywordEl ? keywordEl.value : '').toLowerCase();
    
    const wrap = document.getElementById('list-pegawai');
    // Pastikan elemen #list-pegawai ada
    if (!wrap) return; 

    const rows = dataPegawai
      .filter(p => [p.nama, p.nip, p.jabatan].join(' ').toLowerCase().includes(q))
      .map(p => `
        <div class="bg-white rounded-3xl border border-blue-300 shadow px-6 py-4 flex justify-between items-center">
          <div class="flex items-start gap-4">
            <input type="checkbox" class="mt-2" onchange="toggleSelect(${p.id}, this.checked)" ${selectedIds.has(p.id) ? 'checked' : ''}>
            <div>
              <div class="text-blue-700 font-semibold text-lg">${p.nama}</div>
              <div class="h-0.5 bg-blue-300 my-2 w-56"></div>
              <div class="text-gray-600 text-sm">NIP ${p.nip}</div>
              <div class="text-gray-600 text-sm">${p.jabatan}</div>
            </div>
          </div>
          <a href="#" class="text-blue-600 hover:text-blue-700 font-medium text-sm" onclick="lihatDetail(${p.id});return false;">Lihat Detail</a>
        </div>
      `).join('');
    wrap.innerHTML = rows || `<div class="text-gray-500">Tidak ada data.</div>`;
  }

  function toggleSelect(id, state) { state ? selectedIds.add(id) : selectedIds.delete(id); }
  function hapusTerpilih() {
    // Ganti alert dan confirm dengan modal kustom jika perlu
    if (!selectedIds.size) return alert('Pilih minimal satu pegawai.');
    if (confirm('Hapus pegawai terpilih?')) {
      dataPegawai = dataPegawai.filter(p => !selectedIds.has(p.id));
      selectedIds.clear();
      renderList();
    }
  }
  function lihatDetail(id) {
    const p = dataPegawai.find(x => x.id === id);
    if (!p) return;
    // Ganti alert dengan modal kustom jika perlu
    alert(`${p.nama}\nNIP: ${p.nip}\nJabatan: ${p.jabatan}`);
  }

  // --- Modal Tambah ---
  function openModal() { 
    const modal = document.getElementById('modal');
    if (modal) modal.classList.remove('hidden'); 
  }
  function closeModal() { 
    const modal = document.getElementById('modal');
    if (modal) modal.classList.add('hidden'); 
  }
  function tambahPegawai(e) {
    e.preventDefault();
    const namaEl = document.getElementById('f_nama');
    const nipEl = document.getElementById('f_nip');
    const jabatanEl = document.getElementById('f_jabatan');

    const nama = namaEl ? namaEl.value.trim() : '';
    const nip = nipEl ? nipEl.value.trim() : '';
    const jabatan = jabatanEl ? jabatanEl.value.trim() : 'Pegawai';

    if (!nama || !nip) return alert('Nama & NIP wajib diisi.');
    const id = Math.max(0, ...dataPegawai.map(p => p.id)) + 1;
    dataPegawai.unshift({ id, nama, nip, jabatan });
    closeModal();
    if (e.target) e.target.reset();
    renderList();
  }

  // --- Navbar/Sidebar dropdown DIHAPUS ---
  // Script ini sudah di-handle oleh app.js dari layout Anda

  // start
  // Pastikan DOM sudah siap sebelum memanggil renderList
  document.addEventListener('DOMContentLoaded', function() {
      renderList();
  });
</script>