<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LingkupAuditSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kode UKE yang dirujuk tersedia.
        // Kalau belum ada, buat minimal record-nya.
        DB::table('unitkerja')->updateOrInsert(
            ['kode_uke' => 'IRWIL1_AUD'],
            ['nama_uke' => 'Inspektorat Wilayah I (Auditor)', 'id_induk' => null]
        );

        DB::table('unitkerja')->updateOrInsert(
            ['kode_uke' => 'SETJEN'],
            ['nama_uke' => 'Sekretariat Jenderal', 'id_induk' => null]
        );

        // Ambil ID/field dengan cara aman (value() / first()?->id)
        $idAuditor1  = DB::table('unitkerja')->where('kode_uke', 'IRWIL1_AUD')->value('id');
        $namaAuditi1 = DB::table('unitkerja')->where('kode_uke', 'SETJEN')->value('nama_uke');

        // Guard kalau masih null (misal seed UnitKerja gagal)
        if (!$idAuditor1 || !$namaAuditi1) {
            $this->command->warn('LingkupAuditSeeder: unitkerja belum lengkap, lewati insert.');
            return;
        }

        // Insert bila belum ada (hindari duplikasi)
        DB::table('lingkupaudit')->updateOrInsert(
            ['unit_kerja_id' => $idAuditor1, 'nama_auditi' => $namaAuditi1],
            []
        );

        DB::table('lingkupaudit')->updateOrInsert(
            ['unit_kerja_id' => $idAuditor1, 'nama_auditi' => 'Badan Pengembangan SDM'],
            []
        );
    }
}
