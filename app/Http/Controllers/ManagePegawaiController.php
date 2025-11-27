<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PangkatGolongan;
use App\Models\UnitKerja;

class ManagePegawaiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = User::select('nip','nama','email','no_telp','is_aktif')
            ->orderBy('nama');

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // paginate, 10 per halaman
        $users = $query->paginate(10)->appends($request->only('q'));

        // optional: roles for create/edit form
        $roles = Role::orderBy('nama')->get();

        return view('pic.managePegawai', compact('users','roles','q'));
    }

    public function create()
    {
        // Ambil daftar UKE-1 (id_induk = null)
        $uke1s = UnitKerja::whereNull('id_induk')->orderBy('nama_uke')->get();

        // Ambil daftar pangkat jika ada
        $pangkats = PangkatGolongan::orderBy('nama_pangkat')->get();

        // Mapping parent -> anak untuk dynamic UKE-2
        $allUkes = UnitKerja::orderBy('nama_uke')->get();
        $unitKerjaByParent = $allUkes->whereNotNull('id_induk')
            ->groupBy('id_induk')
            ->map(function($group) {
                return $group->map(function($u) {
                    return ['id' => $u->id, 'nama_uke' => $u->nama_uke];
                })->values();
            })
            ->mapWithKeys(function($value, $key) {
                return [(string)$key => $value];
            });

        // Roles
        $roles = Role::orderBy('nama')->get();
        $defaultRole = Role::whereRaw('LOWER(kode) = ?', ['pegawai'])
            ->orWhereRaw('LOWER(nama) = ?', ['pegawai'])
            ->first();

        return view('pic.tambahPegawai', compact('uke1s', 'unitKerjaByParent', 'roles', 'pangkats'));
    }

    public function store(Request $request)
    {
        // VALIDASI: gunakan nama tabel sesuai DB
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'nip'      => 'required|string|max:50|unique:users,nip',
            'email'    => 'required|email|max:255|unique:users,email',
            'no_telp'  => 'nullable|string|max:20',
            'role'     => 'required|integer|exists:roles,id',
            // pake nama tabel unitkerja (FK)
            'uke1'     => 'required|integer|exists:unitkerja,id',
            'uke2'     => 'required|integer|exists:unitkerja,id',
            'pangkat'  => 'required|integer|exists:pangkatgolongan,id',
            'pangkat_input' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($validated, $request) {
            $user = new User();
            $user->nama = $validated['nama'];
            $user->nip = $validated['nip'];
            $user->email = $validated['email'] ?? null;
            $user->no_telp = $validated['no_telp'] ?? null;

            // set id_uke sesuai konvensi project Anda (prioritaskan uke2 jika ada)
            $user->id_uke = $validated['uke2'] ?? $validated['uke1'] ?? null;

            // pangkat: bisa ID atau manual
            $user->pangkat_gol_id = $validated['pangkat'] ?? null;

            // password default — contoh: nip
            $user->password_hash = Hash::make($validated['nip']);

            $user->save();

            $user->roles()->sync([(int)$validated['role']]);
        });

        return redirect()->route('pic.pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }


    // show edit form
    public function edit($nip)
    {
        // cari berdasarkan kolom nip agar aman
        $user = User::with('roles')->where('nip', $nip)->firstOrFail();

        $roles = Role::orderBy('nama')->get();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        $uke1s = UnitKerja::whereNull('id_induk')->orderBy('nama_uke')->get();
        $selectedUke2 = null;
        $selectedUke1Id = null;
        if ($user->id_uke) {
            $ukeRec = UnitKerja::find($user->id_uke);
            if ($ukeRec) {
                if ($ukeRec->id_induk) {
                    $selectedUke2 = $ukeRec->id;
                    $selectedUke1Id = $ukeRec->id_induk;
                } else {
                    $selectedUke1Id = $ukeRec->id;
                }
            }
        }
        $uke2s = $selectedUke1Id ? UnitKerja::where('id_induk', $selectedUke1Id)->orderBy('nama_uke')->get() : collect();
        $allUkes = UnitKerja::orderBy('nama_uke')->get();
        $unitKerjaByParent = $allUkes->whereNotNull('id_induk')
            ->groupBy('id_induk')
            ->map(function($group) {
                return $group->map(function($u) {
                    return ['id' => $u->id, 'nama_uke' => $u->nama_uke];
                })->values();
            })
            ->mapWithKeys(function($value, $key) {
                return [(string)$key => $value];
            });

        $pangkats = PangkatGolongan::orderBy('nama_pangkat')->get();

        return view('pic.editPegawai', compact(
            'user', 'pangkats', 'roles', 'userRoleIds', 'uke1s', 'uke2s', 'selectedUke1Id', 'selectedUke2', 'unitKerjaByParent'
        ));
    }

    // proses update
    public function update(Request $request, $nip)
    {
        // cari berdasar nip (lebih robust)
        $user = User::where('nip', $nip)->firstOrFail();

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['nullable','email', Rule::unique('users', 'email')->ignore($user->getKey(), $user->getKeyName()),],
            'no_telp' => 'nullable|string|max:20',
            'uke1' => 'nullable|string|max:255',
            'uke2' => 'nullable|string|max:255',
            'pangkat' => 'nullable|integer|exists:pangkatgolongan,id',
            'role' => 'required|integer|exists:roles,id',
        ]);

        DB::transaction(function() use ($user, $data) {
            // assign fields — gunakan null coalescing untuk mempertahankan value jika tidak di-submit
            $user->nama = $data['nama'];
            $user->email = $data['email'] ?? $user->email;
            $user->no_telp = $data['no_telp'] ?? $user->no_telp;

            // unit kerja: prioritas uke2, lalu uke1, lalu tetapkan existing jika tidak diisi
            $user->id_uke = $data['uke2'] ?? $data['uke1'] ?? $user->id_uke;

            if (!empty($data['pangkat'])) {
                $user->pangkat_gol_id = $data['pangkat'];
            }

            $user->save();

            // roles: sinkronkan (jika array kosong akan mengosongkan relasi)
            $user->roles()->sync([(int)$data['role']]);
        });

        return redirect()->route('pic.pegawai.index')->with('success','Data pegawai berhasil diperbarui.');
    }

    public function destroy($nip)
    {
        $user = User::findOrFail($nip);
        DB::transaction(function() use ($user) {
            $user->roles()->detach();
            $user->delete();
        });
        return redirect()->route('pic.pegawai.index')->with('success','Pegawai dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $nips = $request->input('nips', []);
        if (!is_array($nips) || empty($nips)) {
            return redirect()->back()->with('error','Tidak ada pegawai terpilih.');
        }

        DB::transaction(function() use ($nips) {
            $users = User::whereIn('nip', $nips)->get();
            foreach($users as $u) {
                $u->roles()->detach();
                $u->delete();
            }
        });

        return redirect()->route('pic.pegawai.index')->with('success','Pegawai terpilih dihapus.');
    }
}
