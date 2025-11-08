<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIPERDIN - Manajemen Pegawai</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body{font-family:'Poppins',sans-serif}
    .navbar-gradient{background:linear-gradient(135deg,#1e5bb8 0%,#2d74da 100%)}
    .sidebar-gradient{background:linear-gradient(180deg,#2d5ba8 0%,#1e4b98 100%)}
    .bg-pattern{
      background-color:#e8e9f0;
      background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="10" cy="10" r="2" fill="%23d0d0dc" opacity="0.3"/></svg>');
      background-repeat:repeat;
    }
    .sidebar{transition:width .3s ease}
    .sidebar-text{opacity:0;transition:opacity .3s}
    .sidebar.active .sidebar-text{opacity:1}
  </style>
</head>
<body class="bg-pattern pt-20">

  <!-- Navbar -->
  <nav class="navbar-gradient fixed top-0 left-0 right-0 z-50 px-8 py-4 rounded-b-[30px] shadow-lg flex items-center justify-between">
    <div class="flex items-center gap-4">
      <div class="w-6 h-5 flex flex-col justify-between cursor-pointer" onclick="toggleSidebar()">
        <div class="w-full h-0.5 bg-white rounded"></div>
        <div class="w-full h-0.5 bg-white rounded"></div>
        <div class="w-full h-0.5 bg-white rounded"></div>
      </div>
      <div class="text-white text-3xl font-bold italic tracking-wide">SIPERDIN</div>
    </div>
    <div class="flex items-center gap-4 cursor-pointer px-3 py-1.5 rounded-full hover:bg-white/10 active:bg-black/20 text-white"
         onclick="toggleProfileDropdown()">
      <span class="text-sm hidden md:block">Reza Anu</span>
      <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-700">
        <i class="fas fa-user text-sm"></i>
      </div>
    </div>
  </nav>

  <!-- Overlays -->
  <div id="dropdownOverlay" class="fixed inset-0 z-40 hidden" onclick="toggleProfileDropdown()"></div>
  <div id="overlay" class="fixed inset-0 bg-black/50 opacity-0 invisible transition-all z-30" onclick="toggleSidebar()"></div>

  <!-- Profile Dropdown -->
  <div id="profileDropdown" class="fixed top-[70px] right-8 bg-white rounded-xl shadow-lg min-w-[220px] opacity-0 invisible transform -translate-y-2 transition-all z-50">
    <div class="p-5 border-b border-gray-200 flex items-center gap-3">
      <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-400 rounded-full flex items-center justify-center text-white text-xl">
        <i class="fas fa-user"></i>
      </div>
      <div>
        <div class="text-gray-800 text-sm font-semibold">Reza Anu</div>
        <div class="text-gray-500 text-xs">PIC</div>
      </div>
    </div>
    <ul class="py-2">
      <li><a class="flex items-center gap-3 px-5 py-3 text-gray-800 hover:bg-gray-100 text-sm" href="#"><i class="fas fa-user-circle w-5 text-gray-500"></i>Profil</a></li>
      <li><a class="flex items-center gap-3 px-5 py-3 text-gray-800 hover:bg-gray-100 text-sm" href="#"><i class="fas fa-cog w-5 text-gray-500"></i>Pengaturan</a></li>
      <li class="h-px bg-gray-200 my-2"></li>
      <li><a class="flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-gray-100 text-sm" href="#"><i class="fas fa-sign-out-alt w-5"></i>Logout</a></li>
    </ul>
  </div>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar-gradient fixed left-0 top-[100px] w-20 h-[calc(100vh-80px)] z-40 pt-5 rounded-r-[30px] shadow-xl overflow-hidden sidebar">
    <ul class="py-5">
      <li class="my-2">
        <a class="flex items-center px-8 py-4 text-white hover:bg-white/10 gap-4" href="#"><span class="w-6 h-6 flex items-center justify-center text-xl"><i class="fas fa-home"></i></span><span class="sidebar-text whitespace-nowrap">Beranda</span></a>
      </li>
      <li class="my-2">
        <a class="flex items-center px-8 py-4 text-white hover:bg-white/10 gap-4" href="#"><span class="w-6 h-6 flex items-center justify-center text-xl"><i class="fa-solid fa-briefcase"></i></span><span class="sidebar-text whitespace-nowrap">Penugasan</span></a>
      </li>
      <li class="my-2">
        <a class="flex items-center px-8 py-4 text-white hover:bg-white/10 gap-4" href="#"><span class="w-6 h-6 flex items-center justify-center text-xl"><i class="fas fa-users"></i></span><span class="sidebar-text whitespace-nowrap">Manajemen Pegawai</span></a>
      </li>
      <li class="my-2">
        <a class="flex items-center px-8 py-4 text-white hover:bg-white/10 gap-4" href="#"><span class="w-6 h-6 flex items-center justify-center text-xl"><i class="fas fa-history"></i></span><span class="sidebar-text whitespace-nowrap">Riwayat</span></a>
      </li>
    </ul>

    <!-- Profile bawah -->
    <div class="absolute bottom-8 left-0 right-0 px-4 pt-5 border-t border-white/20 flex flex-col items-center">
      <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl mb-2">
        <i class="fas fa-user"></i>
      </div>
      <div class="sidebar-text text-white text-sm font-bold mb-1 whitespace-nowrap text-center">Fernanda Aditia Putra</div>
      <div class="sidebar-text text-white/70 text-xs mb-4 whitespace-nowrap">NIP: 2021011</div>
      <button class="sidebar-text flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white text-sm w-full transition">
        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
      </button>
    </div>
  </aside>

  <!-- Main -->
  <main class="max-w-5xl mx-auto px-5 py-8">
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
        <input id="keyword" type="text" class="flex-1 outline-none text-sm" placeholder="Cari nama / NIP / jabatan..." oninput="renderList()">
      </div>
    </div>

    <!-- Daftar Pegawai -->
    <section id="list-pegawai" class="space-y-4"></section>
  </main>

  <!-- Modal Tambah -->
  <div id="modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="relative max-w-lg mx-auto mt-20 bg-white rounded-xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800">Tambah Pegawai</h3>
        <button class="text-gray-400 hover:text-gray-600" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form onsubmit="tambahPegawai(event)" class="space-y-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Nama Pegawai</label>
          <input id="f_nama" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">NIP</label>
          <input id="f_nip" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Jabatan</label>
          <input id="f_jabatan" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-600" placeholder="Pegawai PIC / Pegawai PPK">
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
          <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg" onclick="closeModal()">Batal</button>
          <button class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // --- Data dummy (ganti dengan data backend jika perlu) ---
    let dataPegawai = [
      { id: 1, nama: "Amanda Atika Putri", nip: "000000000000000", jabatan: "Pegawai PPK" },
      { id: 2, nama: "Rani Dwi Cahyani", nip: "000000000000001", jabatan: "Pegawai PIC" },
      { id: 3, nama: "Indra Yudha Saputra", nip: "000000000000002", jabatan: "Pegawai PPK" },
    ];
    let selectedIds = new Set();

    // --- Render list ---
    function renderList(){
      const q = (document.getElementById('keyword').value || '').toLowerCase();
      const wrap = document.getElementById('list-pegawai');
      const rows = dataPegawai
        .filter(p => [p.nama,p.nip,p.jabatan].join(' ').toLowerCase().includes(q))
        .map(p => `
          <div class="bg-white rounded-3xl border border-blue-300 shadow px-6 py-4 flex justify-between items-center">
            <div class="flex items-start gap-4">
              <input type="checkbox" class="mt-2" onchange="toggleSelect(${p.id}, this.checked)" ${selectedIds.has(p.id) ? 'checked':''}>
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

    function toggleSelect(id, state){ state ? selectedIds.add(id) : selectedIds.delete(id); }
    function hapusTerpilih(){
      if(!selectedIds.size) return alert('Pilih minimal satu pegawai.');
      if(confirm('Hapus pegawai terpilih?')){
        dataPegawai = dataPegawai.filter(p => !selectedIds.has(p.id));
        selectedIds.clear();
        renderList();
      }
    }
    function lihatDetail(id){
      const p = dataPegawai.find(x => x.id===id);
      if(!p) return;
      alert(`${p.nama}\nNIP: ${p.nip}\nJabatan: ${p.jabatan}`);
    }

    // --- Modal Tambah ---
    function openModal(){ document.getElementById('modal').classList.remove('hidden'); }
    function closeModal(){ document.getElementById('modal').classList.add('hidden'); }
    function tambahPegawai(e){
      e.preventDefault();
      const nama = document.getElementById('f_nama').value.trim();
      const nip = document.getElementById('f_nip').value.trim();
      const jabatan = document.getElementById('f_jabatan').value.trim() || 'Pegawai';
      if(!nama || !nip) return alert('Nama & NIP wajib diisi.');
      const id = Math.max(0,...dataPegawai.map(p=>p.id))+1;
      dataPegawai.unshift({ id, nama, nip, jabatan });
      closeModal();
      e.target.reset();
      renderList();
    }

    // --- Navbar/Sidebar dropdown ---
    function toggleSidebar(){
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const dropdown = document.getElementById('profileDropdown');
      const dropdownOverlay = document.getElementById('dropdownOverlay');
      dropdown.classList.remove('opacity-100','visible','translate-y-0');
      dropdown.classList.add('opacity-0','invisible','-translate-y-2');
      dropdownOverlay.classList.add('hidden');

      if(sidebar.classList.contains('active')){
        sidebar.classList.remove('active','w-64'); sidebar.classList.add('w-20');
        overlay.classList.remove('opacity-100','visible'); overlay.classList.add('opacity-0','invisible');
      } else {
        sidebar.classList.add('active','w-64'); sidebar.classList.remove('w-20');
        overlay.classList.remove('opacity-0','invisible'); overlay.classList.add('opacity-100','visible');
      }
    }
    function toggleProfileDropdown(){
      const dropdown = document.getElementById('profileDropdown');
      const overlay = document.getElementById('dropdownOverlay');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('overlay');
      sidebar.classList.remove('active','w-64'); sidebar.classList.add('w-20');
      sidebarOverlay.classList.remove('opacity-100','visible'); sidebarOverlay.classList.add('opacity-0','invisible');

      if(dropdown.classList.contains('opacity-100')){
        dropdown.classList.remove('opacity-100','visible','translate-y-0');
        dropdown.classList.add('opacity-0','invisible','-translate-y-2'); overlay.classList.add('hidden');
      } else {
        dropdown.classList.remove('opacity-0','invisible','-translate-y-2');
        dropdown.classList.add('opacity-100','visible','translate-y-0'); overlay.classList.remove('hidden');
      }
    }
    document.addEventListener('click', (e)=>{
      const trigger = e.target.closest('[onclick="toggleProfileDropdown()"]');
      const dd = document.getElementById('profileDropdown');
      if(!trigger && !dd.contains(e.target)){
        dd.classList.remove('opacity-100','visible','translate-y-0');
        dd.classList.add('opacity-0','invisible','-translate-y-2');
        document.getElementById('dropdownOverlay').classList.add('hidden');
      }
    });

    // start
    renderList();
  </script>
</body>
</html>
