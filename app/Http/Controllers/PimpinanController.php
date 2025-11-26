    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    class PimpinanController extends Controller
    {
        /**
         * Method untuk halaman beranda/dashboard
         * Menampilkan monitoring pegawai yang sedang perjalanan dinas
         * dan statistik perjalanan dinas
         */
        public function index()
        {
            // Ambil ID status "Sedang Berlangsung"
            $statusOnProgress = DB::table('statusperjadin')
                ->where('nama_status', 'Sedang Berlangsung')
                ->value('id');
            
            // Hitung pegawai yang sedang dalam perjalanan dinas
            $pegawaiOnProgress = DB::table('perjalanandinas')
                ->where('id_status', $statusOnProgress)
                ->count();
            
            // Ambil data perjalanan dinas yang sedang berlangsung dengan detail
            $perjalanandinas = DB::table('perjalanandinas')
                ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
                ->leftJoin('users as pembuat', 'perjalanandinas.id_pembuat', '=', 'pembuat.nip')
                ->where('perjalanandinas.id_status', $statusOnProgress)
                ->select(
                    'perjalanandinas.*', 
                    'statusperjadin.nama_status',
                    'pembuat.nama as nama_pembuat',
                    'pembuat.email as email_pembuat'
                )
                ->orderBy('perjalanandinas.tgl_mulai', 'desc')
                ->get();
            
            // ===== DATA UNTUK BAR CHART (Jumlah Perjalanan Dinas per Bulan) =====
            $barChartData = [];
            $tahunSekarang = Carbon::now()->year;
            
            // Loop dari Januari (bulan 1) sampai Desember (bulan 12)
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                // Hitung jumlah perjalanan dinas per bulan di tahun ini
                $count = DB::table('perjalanandinas')
                    ->whereYear('tgl_mulai', $tahunSekarang)
                    ->whereMonth('tgl_mulai', $bulan)
                    ->count();
                
                $barChartData[] = $count;
            }
            
            // Hitung total 30 hari terakhir untuk perjalanan dinas
            $totalSebulanTerakhir = DB::table('perjalanandinas')
                ->where('tgl_mulai', '>=', Carbon::now()->subDays(30))
                ->where('tgl_mulai', '<=', Carbon::now())
                ->count();
            
            // ===== DATA UNTUK LINE CHART (Total Anggaran per Bulan) =====
            $lineChartData = [];
            
            // Loop dari Januari (bulan 1) sampai Desember (bulan 12)
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                // Hitung total anggaran dari laporan keuangan per bulan
                $totalAnggaran = DB::table('laporankeuangan')
                    ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
                    ->whereYear('perjalanandinas.tgl_mulai', $tahunSekarang)
                    ->whereMonth('perjalanandinas.tgl_mulai', $bulan)
                    ->whereNotNull('laporankeuangan.biaya_rampung')
                    ->sum('laporankeuangan.biaya_rampung');
                
                $lineChartData[] = (int)$totalAnggaran;
            }
            
            // Hitung anggaran 30 hari terakhir
            $anggaranSebulanTerakhir = (int)DB::table('laporankeuangan')
                ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
                ->where('perjalanandinas.tgl_mulai', '>=', Carbon::now()->subDays(30))
                ->where('perjalanandinas.tgl_mulai', '<=', Carbon::now())
                ->whereNotNull('laporankeuangan.biaya_rampung')
                ->sum('laporankeuangan.biaya_rampung');
            
            // Return ke view 'pimpinan.monitoringPegawai'
            return view('pimpinan.monitoringPegawai', compact(
                'pegawaiOnProgress',
                'perjalanandinas',
                'barChartData',
                'lineChartData',
                'totalSebulanTerakhir',
                'anggaranSebulanTerakhir'
            ));
        }
        
        /**
         * Method untuk halaman detail perjalanan dinas
         * Menampilkan detail lengkap dari suatu perjalanan dinas
         * 
         * @param int $id ID perjalanan dinas
         */
        public function detail($id)
        {
            // Ambil detail perjalanan dinas dengan join ke tabel status
            $perjadin = DB::table('perjalanandinas')
                ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
                ->where('perjalanandinas.id', $id)
                ->select('perjalanandinas.*', 'statusperjadin.nama_status')
                ->first();
            
            // Jika data tidak ditemukan, tampilkan error 404
            if (!$perjadin) {
                abort(404, 'Data perjalanan dinas tidak ditemukan');
            }
            
            // Ambil data pembuat perjalanan dinas
            $pembuat = DB::table('users')
                ->where('nip', $perjadin->id_pembuat)
                ->first();
            
            // Ambil data yang menyetujui perjalanan dinas
            $approver = null;
            if ($perjadin->approved_by) {
                $approver = DB::table('users')
                    ->where('nip', $perjadin->approved_by)
                    ->first();
            }
            
            // Ambil pegawai yang terlibat dalam perjalanan dinas
            $pegawai = DB::table('pegawaiperjadin')
                ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
                ->where('pegawaiperjadin.id_perjadin', $id)
                ->select(
                    'pegawaiperjadin.*', 
                    'users.nama', 
                    'users.email',
                    'users.nip'
                )
                ->get();
            
            // Ambil laporan keuangan jika ada
            $laporan = DB::table('laporankeuangan')
                ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
                ->where('laporankeuangan.id_perjadin', $id)
                ->select('laporankeuangan.*', 'statuslaporan.nama_status as status_laporan')
                ->first();
            
            // Ambil rincian anggaran jika ada laporan
            $rincian = [];
            if ($laporan) {
                $rincian = DB::table('rinciananggaran')
                    ->join('kategoribiaya', 'rinciananggaran.id_kategori', '=', 'kategoribiaya.id')
                    ->where('rinciananggaran.id_laporan', $laporan->id)
                    ->select(
                        'rinciananggaran.*', 
                        'kategoribiaya.nama_kategori',
                        'kategoribiaya.id as id_kategori_biaya'
                    )
                    ->get();
            }
            
            // Ambil data geotagging (lokasi check-in/check-out pegawai)
            $geotagging = DB::table('geotagging')
                ->join('users', 'geotagging.id_user', '=', 'users.nip')
                ->leftJoin('tipegeotagging', 'geotagging.id_tipe', '=', 'tipegeotagging.id')
                ->where('geotagging.id_perjadin', $id)
                ->select(
                    'geotagging.*', 
                    'users.nama', 
                    'users.nip',
                    'tipegeotagging.nama_tipe'
                )
                ->orderBy('geotagging.created_at', 'desc')
                ->get();
            
            // Return ke view 'pimpinan.detail'
            return view('pimpinan.detail', compact(
                'perjadin',
                'pembuat',
                'approver',
                'pegawai',
                'laporan',
                'rincian',
                'geotagging'
            ));
        }
    }