<?php
// Langsung pakai autoload composer saja, tidak perlu bootstrap Laravel
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(12);

$sectionStyle = [
    'marginLeft'   => 1701,
    'marginRight'  => 1134,
    'marginTop'    => 1134,
    'marginBottom' => 1134,
    'paperSize'    => 'A4',
];
$section = $phpWord->addSection($sectionStyle);

// Styles
$H1  = ['bold'=>true,'size'=>16,'name'=>'Times New Roman'];
$H2  = ['bold'=>true,'size'=>13,'name'=>'Times New Roman','color'=>'1F3864'];
$H3  = ['bold'=>true,'size'=>12,'name'=>'Times New Roman','color'=>'2E74B5'];
$TH  = ['bold'=>true,'size'=>10,'name'=>'Times New Roman','color'=>'FFFFFF'];
$TD  = ['size'=>10,'name'=>'Times New Roman'];
$TDB = ['bold'=>true,'size'=>10,'name'=>'Times New Roman'];
$TDS = ['size'=>9,'name'=>'Times New Roman'];
$TDI = ['size'=>9,'name'=>'Times New Roman','italic'=>true];
$BODY = ['size'=>12,'name'=>'Times New Roman'];
$SMALL = ['size'=>9,'name'=>'Times New Roman','color'=>'666666'];
$CAP  = ['bold'=>true,'size'=>11,'name'=>'Times New Roman','color'=>'1F3864'];

$pCenter = ['alignment'=>Jc::CENTER,'spaceAfter'=>100];
$pLeft   = ['alignment'=>Jc::START, 'spaceAfter'=>100];
$pBoth   = ['alignment'=>Jc::BOTH,  'spaceAfter'=>160];
$pCap    = ['alignment'=>Jc::CENTER,'spaceAfter'=>80,'spaceBefore'=>200];

$tableStyle = ['borderSize'=>6,'borderColor'=>'B8C4CC','cellMargin'=>60];
$phpWord->addTableStyle('T', $tableStyle);
$hCell  = ['bgColor'=>'2E74B5','valign'=>'center'];
$aCell  = ['bgColor'=>'EBF3FB','valign'=>'top'];
$wCell  = ['valign'=>'top'];

function hRow($table, $cols, $widths, $hCell, $TH) {
    $r = $table->addRow(350);
    foreach($cols as $i=>$c) {
        $cell = $r->addCell($widths[$i], $hCell);
        $cell->addText($c, $TH, ['alignment'=>Jc::CENTER]);
    }
}
function dRow($table, $data, $widths, $isAlt, $aCell, $wCell, $TDS) {
    $st = $isAlt ? $aCell : $wCell;
    $r = $table->addRow();
    foreach($data as $i=>$v) {
        $cell = $r->addCell($widths[$i], $st);
        $cell->addText($v ?? '-', $TDS);
    }
}

// =========================================================
// COVER
// =========================================================
$section->addText('LAPORAN NORMALISASI DATABASE', $H1, $pCenter);
$section->addText('Sistem Informasi Pengajuan Surat — KERNAS', $H2, $pCenter);
$section->addTextBreak(1);
$section->addText('Laporan ini mendokumentasikan proses normalisasi database sistem KERNAS mulai dari Un-Normalized Form (UNF) hingga Boyce-Codd Normal Form (BCNF).', $BODY, $pBoth);
$section->addText('Tanggal: '.date('d F Y'), $BODY, $pCenter);
$section->addText('Total Tabel Final: 15 Tabel', $BODY, $pCenter);
$section->addPageBreak();

// =========================================================
// BAB 1 PENDAHULUAN
// =========================================================
$section->addText('BAB 1 — PENDAHULUAN', $H1, $pLeft);
$section->addText(
    'Normalisasi basis data adalah proses sistematis pengorganisasian atribut-atribut dalam tabel relasional untuk meminimalkan redundansi data dan memastikan integritas data. Laporan ini mendokumentasikan setiap tahap normalisasi yang diterapkan pada sistem informasi KERNAS.',
    $BODY, $pBoth
);
$section->addText(
    'Tujuan normalisasi: (1) Menghilangkan redundansi data; (2) Memastikan dependensi logis antar data; (3) Mencegah anomali pada operasi INSERT, UPDATE, dan DELETE; (4) Mempermudah pemeliharaan data.',
    $BODY, $pBoth
);
$section->addTextBreak(1);
$section->addText('Tabel 1.1 — Tahapan Normalisasi', $CAP, $pCap);
$t = $section->addTable('T');
hRow($t, ['Tahap','Syarat Utama'], [2800,6500], $hCell, $TH);
$steps = [
    ['Un-Normalized Form (UNF)', 'Semua atribut dalam satu tabel, belum ada aturan normalisasi'],
    ['First Normal Form (1NF)',   'Setiap sel bernilai atomik, tidak ada kelompok berulang, ada Primary Key'],
    ['Second Normal Form (2NF)',  'Sudah 1NF + tidak ada dependensi parsial terhadap PK komposit'],
    ['Third Normal Form (3NF)',   'Sudah 2NF + tidak ada dependensi transitif antar atribut non-kunci'],
    ['BCNF',                      'Sudah 3NF + setiap determinan adalah kunci kandidat (superkey)'],
];
foreach($steps as $i=>$s) {
    dRow($t, $s, [2800,6500], $i%2===0, $aCell, $wCell, $TDS);
}
$section->addPageBreak();

// =========================================================
// BAB 2 — UNF
// =========================================================
$section->addText('BAB 2 — UN-NORMALIZED FORM (UNF)', $H1, $pLeft);
$section->addText(
    'Pada tahap ini, seluruh atribut dari semua entitas sistem digabungkan ke dalam satu tabel besar tanpa aturan normalisasi. Kondisi ini menyebabkan banyak redundansi: jika satu pengajuan melibatkan 3 anggota, maka data pengajuan akan tertulis ulang 3 kali.',
    $BODY, $pBoth
);
$section->addTextBreak(1);
$section->addText('Tabel 2.1 — Semua Atribut Sistem dalam Satu Tabel (UNF)', $CAP, $pCap);
$t2 = $section->addTable('T');
hRow($t2, ['No','Nama Atribut','Tipe Data','Sumber Entitas'], [400,2800,1800,4300], $hCell, $TH);
$unvData = [
    ['1','id_pengajuan','INT UNSIGNED','Pengajuan'],
    ['2','judul_pengajuan','VARCHAR(200)','Pengajuan'],
    ['3','tipe_pengajuan','ENUM(SK,ST)','Pengajuan'],
    ['4','status_pengajuan','VARCHAR(50)','Pengajuan'],
    ['5','daftar_menimbang','TEXT','Pengajuan'],
    ['6','daftar_memperhatikan','TEXT','Pengajuan'],
    ['7','daftar_memutuskan','TEXT','Pengajuan'],
    ['8','ada_lampiran','TINYINT(1)','Pengajuan'],
    ['9','filepath_pengajuan','VARCHAR(255)','Pengajuan'],
    ['10','rencana_pengambilan','DATE','Pengajuan'],
    ['11','tgl_terbit_pengajuan','DATE','Pengajuan'],
    ['12','catatan_pengajuan','TEXT','Pengajuan'],
    ['13','nomor_diusulkan','VARCHAR(30)','Pengajuan'],
    ['14','id_pengguna','INT UNSIGNED','Pengguna'],
    ['15','nip_pengguna','VARCHAR(20)','Pengguna'],
    ['16','username_pengguna','VARCHAR(100)','Pengguna'],
    ['17','password_pengguna','VARCHAR(255)','Pengguna'],
    ['18','nama_pengguna','VARCHAR(100)','Pengguna'],
    ['19','unit_pengguna','VARCHAR(100)','Pengguna'],
    ['20','jabatan_pengguna','VARCHAR(100)','Pengguna'],
    ['21','is_admin','TINYINT(1)','Pengguna'],
    ['22','id_anggota_pengajuan','INT UNSIGNED','Anggota Pengajuan (Repeating Group)'],
    ['23','nama_anggota','VARCHAR(100)','Anggota Pengajuan (Repeating Group)'],
    ['24','id_template_surat','INT UNSIGNED','Template Surat'],
    ['25','tipe_template','ENUM(SK,ST)','Template Surat'],
    ['26','nama_template','VARCHAR(100)','Template Surat'],
    ['27','filepath_template','VARCHAR(255)','Template Surat'],
    ['28','versi_template','INT UNSIGNED','Template Surat'],
    ['29','is_aktif_template','TINYINT(1)','Template Surat'],
    ['30','id_grup_verifikasi','INT UNSIGNED','Grup Verifikasi'],
    ['31','nama_grup','VARCHAR(100)','Grup Verifikasi'],
    ['32','tingkat_verifikasi','ENUM(1,2,3)','Grup Verifikasi'],
    ['33','id_anggota_grup','INT UNSIGNED','Anggota Grup (Repeating Group)'],
    ['34','nama_anggota_grup','VARCHAR(100)','Anggota Grup (Repeating Group)'],
    ['35','id_dokumen','INT UNSIGNED','Dokumen'],
    ['36','nama_dokumen','VARCHAR(200)','Dokumen'],
    ['37','tgl_dokumen','DATE','Dokumen'],
    ['38','filepath_dokumen','VARCHAR(255)','Dokumen'],
    ['39','kode_unik_dokumen','VARCHAR(20)','Dokumen'],
    ['40','dari_pengajuan_flag','TINYINT(1)','Dokumen'],
    ['41','id_nomor_dokumen','INT UNSIGNED','Nomor Dokumen'],
    ['42','tipe_nomor','ENUM(SK,ST)','Nomor Dokumen'],
    ['43','tahun_nomor','YEAR','Nomor Dokumen'],
    ['44','urutan_nomor','INT UNSIGNED','Nomor Dokumen'],
    ['45','nomor_terformat','VARCHAR(20)','Nomor Dokumen'],
    ['46','id_peraturan','INT UNSIGNED','Peraturan'],
    ['47','kode_peraturan','VARCHAR(100)','Peraturan'],
    ['48','judul_peraturan','VARCHAR(255)','Peraturan'],
    ['49','jenis_peraturan','VARCHAR(50)','Peraturan'],
    ['50','tahun_peraturan','YEAR','Peraturan'],
    ['51','keterangan_peraturan','TEXT','Peraturan'],
    ['52','id_lampiran','BIGINT UNSIGNED','Lampiran Pengajuan'],
    ['53','nama_file_lampiran','VARCHAR(255)','Lampiran Pengajuan'],
    ['54','filepath_lampiran_file','VARCHAR(255)','Lampiran Pengajuan'],
    ['55','id_riwayat','INT UNSIGNED','Riwayat Pengajuan'],
    ['56','aksi_riwayat','VARCHAR(50)','Riwayat Pengajuan'],
    ['57','catatan_aksi','TEXT','Riwayat Pengajuan'],
    ['58','versi_riwayat','INT UNSIGNED','Riwayat Pengajuan'],
    ['59','snapshot_konten','JSON','Riwayat Pengajuan'],
    ['60','created_at','DATETIME','Semua Entitas (Audit)'],
    ['61','updated_at','DATETIME','Semua Entitas (Audit)'],
];
foreach($unvData as $i=>$r) {
    dRow($t2, $r, [400,2800,1800,4300], $i%2===0, $aCell, $wCell, $TDS);
}
$section->addTextBreak(1);
$section->addText('Contoh Baris Data Dummy (UNF) — Semua data dalam 1 baris:', $CAP, $pCap);
$tex = $section->addTable('T');
hRow($tex, ['id_pengajuan','judul','nama_pengguna','nama_anggota','nama_grup','kode_peraturan','nama_dokumen'], [1200,1800,1800,1700,1800,1800,1900], $hCell, $TH);
$rr = $tex->addRow();
foreach(['1','SK Kenaikan Jabatan','Budi Santoso','Andi Pratama (Repeating)','Tim Verifikasi A','UU No.12/2012','SK-001/2025'] as $i=>$v) {
    $w=[1200,1800,1800,1700,1800,1800,1900];
    $st=$i%2===0?$aCell:$wCell;
    $rr->addCell($w[$i],$aCell)->addText($v,$TDS);
}
$section->addPageBreak();

// =========================================================
// BAB 3 — 1NF
// =========================================================
$section->addText('BAB 3 — FIRST NORMAL FORM (1NF)', $H1, $pLeft);
$section->addText(
    'Pada 1NF, aturan yang harus dipenuhi: (1) Setiap sel hanya menyimpan satu nilai atomik. (2) Tidak ada kelompok berulang (repeating groups). (3) Setiap baris diidentifikasi secara unik oleh Primary Key.',
    $BODY, $pBoth
);
$section->addText('Tabel 3.1 — Masalah UNF yang Diselesaikan di 1NF', $CAP, $pCap);
$t3 = $section->addTable('T');
hRow($t3, ['Masalah pada UNF','Penyelesaian pada 1NF'], [4600,4700], $hCell, $TH);
$problems1nf = [
    ['"nama_anggota" berisi banyak nilai (Andi, Rudi, Sari) dalam satu sel','Setiap anggota dipisah menjadi satu baris di tabel anggota_pengajuan tersendiri'],
    ['"nama_anggota_grup" berisi banyak nilai sekaligus','Setiap anggota grup dipisah menjadi satu baris di tabel anggota_grup_verifikasi'],
    ['Satu baris berisi atribut dari semua entitas secara bersamaan','Setiap entitas mendapat tabel mandiri dengan PK yang jelas'],
    ['Tidak ada identitas unik untuk setiap baris','Setiap tabel diberikan kolom id INT UNSIGNED AUTO_INCREMENT sebagai Primary Key'],
];
foreach($problems1nf as $i=>$r) dRow($t3,$r,[4600,4700],$i%2===0,$aCell,$wCell,$TDS);
$section->addTextBreak(1);

// Show 1NF tables
$section->addText('Tabel 3.2 — Struktur Tabel Hasil 1NF', $CAP, $pCap);
$nf1Tables = [
    ['tbl_pengajuan', [
        ['id_pengajuan (PK)','INT UNSIGNED','Kunci utama','1'],
        ['id_pengguna (FK)','INT UNSIGNED','→ pengguna.id','1'],
        ['judul','VARCHAR(200)','','SK Kenaikan Jabatan'],
        ['tipe','ENUM(SK,ST)','','SK'],
        ['status','VARCHAR(50)','Default: Draf','Draf'],
        ['daftar_menimbang','TEXT','Opsional','Bahwa perlu ada SK...'],
        ['filepath','VARCHAR(255)','','pengajuan/sk_1.docx'],
        ['created_at','DATETIME','','2025-03-10 09:00:00'],
    ]],
    ['tbl_pengguna', [
        ['id_pengguna (PK)','INT UNSIGNED','Kunci utama','1'],
        ['nip','VARCHAR(20)','UNIQUE','197801012005011001'],
        ['username','VARCHAR(100)','UNIQUE','budi.santoso'],
        ['password','VARCHAR(255)','Terenkripsi','$2y$10$...'],
        ['nama','VARCHAR(100)','','Budi Santoso, S.T., M.T.'],
        ['unit','VARCHAR(100)','','Teknik Informatika'],
        ['jabatan','VARCHAR(100)','Opsional','Ketua Jurusan'],
        ['is_admin','TINYINT(1)','Default: 0','0'],
    ]],
    ['tbl_anggota_pengajuan (Dipisah dari Repeating Group)', [
        ['id (PK)','INT UNSIGNED','Kunci utama','1'],
        ['id_pengajuan (FK)','INT UNSIGNED','→ pengajuan.id','1'],
        ['id_pengguna (FK)','INT UNSIGNED','→ pengguna.id','3'],
        ['created_at','DATETIME','','2025-03-10 09:05:00'],
    ]],
    ['tbl_grup_verifikasi', [
        ['id_grup (PK)','INT UNSIGNED','Kunci utama','1'],
        ['nama_grup','VARCHAR(100)','','Tim Verifikasi Tingkat 1'],
        ['id_pengguna (FK)','INT UNSIGNED','→ pengguna.id','2'],
        ['tingkat','ENUM(1,2,3)','','1'],
    ]],
    ['tbl_anggota_grup_verifikasi (Dipisah dari Repeating Group)', [
        ['id (PK)','INT UNSIGNED','Kunci utama','1'],
        ['id_grup_verifikasi (FK)','INT UNSIGNED','→ grup_verifikasi.id','1'],
        ['id_pengguna (FK)','INT UNSIGNED','→ pengguna.id','5'],
        ['created_at','DATETIME','','2025-02-05 09:00:00'],
    ]],
];
foreach($nf1Tables as $tDef) {
    $section->addText($tDef[0], $H3, ['spaceAfter'=>80,'spaceBefore'=>200]);
    $ti = $section->addTable('T');
    hRow($ti,['Kolom','Tipe Data','Keterangan','Contoh Data'],[2500,1800,2400,2600],$hCell,$TH);
    foreach($tDef[1] as $i=>$c) dRow($ti,$c,[2500,1800,2400,2600],$i%2===0,$aCell,$wCell,$TDS);
    $section->addTextBreak(1);
}
$section->addPageBreak();

// =========================================================
// BAB 4 — 2NF
// =========================================================
$section->addText('BAB 4 — SECOND NORMAL FORM (2NF)', $H1, $pLeft);
$section->addText(
    'Suatu tabel berada di 2NF jika: (1) Telah memenuhi 1NF; dan (2) Tidak ada dependensi parsial — setiap atribut non-kunci harus bergantung sepenuhnya pada seluruh Primary Key, bukan hanya sebagian darinya (penting untuk PK komposit).',
    $BODY, $pBoth
);
$section->addText(
    'Pada sistem KERNAS, semua tabel menggunakan surrogate key (kolom id tunggal dengan AUTO_INCREMENT) sebagai PK, sehingga secara struktural tidak dimungkinkan adanya dependensi parsial. Analisis terhadap tabel-tabel junction memastikan hal ini.',
    $BODY, $pBoth
);
$section->addTextBreak(1);
$section->addText('Tabel 4.1 — Analisis Dependensi Parsial (Tabel Junction)', $CAP, $pCap);
$t4 = $section->addTable('T');
hRow($t4,['Tabel Junction','Kolom FK 1','Kolom FK 2','Atribut Non-Kunci','Bergantung Pada','Status 2NF'],[2000,1500,1500,1800,1800,800],$hCell,$TH);
$d4 = [
    ['anggota_pengajuan','id_pengajuan','id_pengguna','created_at','Seluruh baris (auto)','LULUS'],
    ['anggota_grup_verifikasi','id_grup_verifikasi','id_pengguna','created_at','Seluruh baris (auto)','LULUS'],
    ['grup_verifikasi_pengajuan','id_pengajuan','id_grup_verifikasi','created_at','Seluruh baris (auto)','LULUS'],
    ['grup_verifikasi_dokumen','id_dokumen','id_grup_verifikasi','created_at','Seluruh baris (auto)','LULUS'],
    ['pengajuan_peraturan','id_pengajuan','id_peraturan','created_at','Seluruh baris (auto)','LULUS'],
    ['anggota_dokumen','id_dokumen','id_pengguna','created_at','Seluruh baris (auto)','LULUS'],
];
foreach($d4 as $i=>$r) dRow($t4,$r,[2000,1500,1500,1800,1800,800],$i%2===0,$aCell,$wCell,$TDS);
$section->addTextBreak(1);
$section->addText('Kesimpulan 2NF: Seluruh tabel memenuhi 2NF. Tidak ada dependensi parsial ditemukan karena penggunaan surrogate key tunggal.', $BODY, $pBoth);
$section->addPageBreak();

// =========================================================
// BAB 5 — 3NF
// =========================================================
$section->addText('BAB 5 — THIRD NORMAL FORM (3NF)', $H1, $pLeft);
$section->addText(
    'Suatu tabel berada di 3NF jika: (1) Telah memenuhi 2NF; dan (2) Tidak ada dependensi transitif — atribut non-kunci tidak boleh bergantung pada atribut non-kunci lainnya. Dengan kata lain, setiap atribut non-kunci hanya boleh bergantung langsung pada PK.',
    $BODY, $pBoth
);
$section->addTextBreak(1);
$section->addText('Tabel 5.1 — Dependensi Transitif yang Ditemukan dan Diselesaikan', $CAP, $pCap);
$t5 = $section->addTable('T');
hRow($t5,['Dependensi Transitif yang Ditemukan','Penyelesaian pada 3NF'],[5200,4100],$hCell,$TH);
$d5 = [
    ['id_pengajuan → id_pengguna → nama, unit, jabatan (data pengguna bukan milik pengajuan)','Data pengguna dipisah ke tabel pengguna. Pengajuan hanya menyimpan FK id_pengguna.'],
    ['id_dokumen → id_nomor_dokumen → tipe, tahun, urutan, nomor_terformat (logika penomoran terpisah)','Dipisah ke tabel nomor_dokumen. Dokumen hanya menyimpan FK id_nomor_dokumen.'],
    ['id_pengajuan → id_grup_verifikasi → nama_grup, tingkat (data grup bukan milik pengajuan)','Data grup dipisah ke tabel grup_verifikasi. Relasi dijembatani tabel grup_verifikasi_pengajuan.'],
    ['id_pengajuan → id_peraturan → kode, judul, jenis, tahun (peraturan adalah entitas mandiri)','Dipisah ke tabel peraturan. Relasi dijembatani tabel pengajuan_peraturan (Many-to-Many).'],
    ['id_riwayat → id_pengajuan → judul_pengajuan (judul bukan milik riwayat)','Tabel riwayat_pengajuan hanya menyimpan FK id_pengajuan. Judul diambil via JOIN bila diperlukan.'],
    ['id_dokumen → id_pengguna_pengusul → nama, unit (pengguna dokumen adalah entitas mandiri)','Relasi dijembatani tabel anggota_dokumen. Data pengguna tetap di tabel pengguna.'],
];
foreach($d5 as $i=>$r) dRow($t5,$r,[5200,4100],$i%2===0,$aCell,$wCell,$TDS);
$section->addTextBreak(1);
$section->addText('Kesimpulan 3NF: Setelah pemisahan entitas di atas, seluruh tabel bebas dari dependensi transitif. Setiap atribut non-kunci hanya bergantung langsung pada Primary Key tabelnya.', $BODY, $pBoth);
$section->addPageBreak();

// =========================================================
// BAB 6 — BCNF (FINAL DB)
// =========================================================
$section->addText('BAB 6 — BOYCE-CODD NORMAL FORM (BCNF) — STRUKTUR DATABASE FINAL', $H1, $pLeft);
$section->addText(
    'BCNF adalah bentuk yang lebih ketat dari 3NF. Syarat BCNF: Untuk setiap dependensi fungsional X → Y, X harus merupakan superkey. Semua tabel KERNAS telah memenuhi BCNF. Berikut adalah dokumentasi lengkap 15 tabel final beserta tipe data dan contoh data dummy.',
    $BODY, $pBoth
);
$section->addTextBreak(1);

$finalTables = [
    ['num'=>'6.1','name'=>'pengguna','desc'=>'Master data seluruh pengguna/pegawai sistem. Setiap atribut bergantung langsung pada PK id.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['nip','VARCHAR(20)','UNIQUE, NOT NULL','197801012005011001'],
        ['username','VARCHAR(100)','UNIQUE, NOT NULL','budi.santoso'],
        ['password','VARCHAR(255)','NOT NULL, Terenkripsi bcrypt','$2y$10$abc...hash'],
        ['nama','VARCHAR(100)','NOT NULL','Budi Santoso, S.T., M.T.'],
        ['unit','VARCHAR(100)','NOT NULL','Teknik Informatika'],
        ['jabatan','VARCHAR(100)','NULL — Opsional','Ketua Jurusan'],
        ['is_admin','TINYINT(1)','NOT NULL, Default: 0','0'],
        ['is_deleted','TINYINT(1)','NOT NULL, Default: 0 (Soft Delete)','0'],
        ['created_at','DATETIME','NULL','2025-01-15 08:30:00'],
        ['updated_at','DATETIME','NULL','2025-06-20 09:00:00'],
    ]],
    ['num'=>'6.2','name'=>'template_surat','desc'=>'Master data template dokumen Word (.docx) yang digunakan sebagai cetakan pembuatan surat.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id (Pembuat)','1'],
        ['tipe','ENUM(SK,ST)','NOT NULL','SK'],
        ['nama_template','VARCHAR(100)','NOT NULL','Template SK Kenaikan Jabatan'],
        ['filepath','VARCHAR(255)','NOT NULL','template_surat/sk_jabatan.docx'],
        ['versi','INT UNSIGNED','NOT NULL, Default: 1','1'],
        ['is_aktif','TINYINT(1)','NOT NULL, Default: 1','1'],
        ['created_at','DATETIME','NULL','2025-01-10 10:00:00'],
        ['updated_at','DATETIME','NULL','2025-06-01 14:00:00'],
    ]],
    ['num'=>'6.3','name'=>'peraturan','desc'=>'Master data peraturan/landasan hukum yang dapat dipilih saat membuat dokumen (dimasukkan ke bagian Mengingat).',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id (Yang menambahkan)','1'],
        ['kode','VARCHAR(100)','UNIQUE, NOT NULL','UU No. 12 Tahun 2012'],
        ['judul','VARCHAR(255)','NOT NULL','Pendidikan Tinggi'],
        ['jenis','VARCHAR(50)','NOT NULL','UU'],
        ['tahun','YEAR','NOT NULL','2012'],
        ['keterangan','TEXT','NULL — Opsional','Dasar hukum pendidikan tinggi di Indonesia'],
        ['created_at','DATETIME','NULL','2025-02-01 09:00:00'],
        ['updated_at','DATETIME','NULL','2025-06-15 11:00:00'],
    ]],
    ['num'=>'6.4','name'=>'grup_verifikasi','desc'=>'Master data grup/tim yang bertugas melakukan verifikasi surat berdasarkan hierarki tingkat.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['nama_grup','VARCHAR(100)','NOT NULL','Tim Verifikasi Tingkat 1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id (Ketua grup)','2'],
        ['tingkat','ENUM(1,2,3)','NULL','1'],
        ['is_deleted','TINYINT(1)','NOT NULL, Default: 0','0'],
        ['created_at','DATETIME','NULL','2025-01-20 08:00:00'],
        ['updated_at','DATETIME','NULL','2025-06-10 10:00:00'],
    ]],
    ['num'=>'6.5','name'=>'anggota_grup_verifikasi','desc'=>'Tabel junction Many-to-Many antara grup_verifikasi dan pengguna. Mencatat keanggotaan setiap grup.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_grup_verifikasi','INT UNSIGNED','FK → grup_verifikasi.id','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id','3'],
        ['created_at','DATETIME','NULL','2025-02-05 09:00:00'],
    ]],
    ['num'=>'6.6','name'=>'nomor_dokumen','desc'=>'Menyimpan nomor surat yang sudah terformat. Logika penomoran dipisah dari tabel dokumen untuk menghindari dependensi transitif.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['tipe','ENUM(SK,ST)','NOT NULL, INDEX','SK'],
        ['tahun','YEAR','NOT NULL','2025'],
        ['urutan','INT UNSIGNED','NOT NULL','1'],
        ['nomor_terformat','VARCHAR(20)','UNIQUE, NOT NULL','SK.001/2025'],
        ['created_at','DATETIME','NULL','2025-03-01 10:00:00'],
    ]],
    ['num'=>'6.7','name'=>'pengajuan','desc'=>'Tabel inti sistem. Menyimpan setiap pengajuan surat oleh pegawai. Relasi ke entitas lain hanya melalui FK.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id (Pengusul)','1'],
        ['id_grup_verifikasi_verifikator','INT UNSIGNED','FK → grup_verifikasi.id, NULL','1'],
        ['judul','VARCHAR(200)','NOT NULL','SK Kenaikan Jabatan Andi'],
        ['tipe','ENUM(SK,ST)','NOT NULL','SK'],
        ['status','VARCHAR(50)','NOT NULL, Default: Draf','Draf'],
        ['daftar_menimbang','TEXT','NULL','Bahwa perlu ada...'],
        ['daftar_memperhatikan','TEXT','NULL','Surat permohonan No...'],
        ['daftar_memutuskan','TEXT','NULL','Memutuskan untuk...'],
        ['ada_lampiran','TINYINT(1)','NOT NULL, Default: 0','0'],
        ['filepath','VARCHAR(255)','NULL','pengajuan/pengajuan_1.docx'],
        ['filepath_lampiran','VARCHAR(255)','NULL','-'],
        ['rencana_pengambilan','DATE','NULL','2025-04-01'],
        ['tgl_terbit','DATE','NULL','2025-03-20'],
        ['catatan','TEXT','NULL','-'],
        ['urutan_antrian','INT UNSIGNED','NULL','1'],
        ['id_verifikator_sekarang','BIGINT UNSIGNED','NULL','2'],
        ['nomor_diusulkan','VARCHAR(30)','NULL','SK.001/2025'],
        ['is_deleted','TINYINT(1)','NOT NULL, Default: 0','0'],
        ['created_at','DATETIME','NULL','2025-03-10 09:00:00'],
        ['updated_at','DATETIME','NULL','2025-03-20 14:00:00'],
    ]],
    ['num'=>'6.8','name'=>'anggota_pengajuan','desc'=>'Tabel junction Many-to-Many antara pengajuan dan pengguna. Mencatat pegawai yang diusulkan/terlibat dalam pengajuan.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengajuan','INT UNSIGNED','FK → pengajuan.id','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id','3'],
        ['created_at','DATETIME','NULL','2025-03-10 09:05:00'],
    ]],
    ['num'=>'6.9','name'=>'grup_verifikasi_pengajuan','desc'=>'Tabel junction Many-to-Many antara pengajuan dan grup_verifikasi. Mencatat penugasan grup untuk memverifikasi pengajuan.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengajuan','INT UNSIGNED','FK → pengajuan.id','1'],
        ['id_grup_verifikasi','INT UNSIGNED','FK → grup_verifikasi.id','1'],
        ['created_at','DATETIME','NULL','2025-03-15 11:00:00'],
    ]],
    ['num'=>'6.10','name'=>'pengajuan_peraturan','desc'=>'Tabel junction Many-to-Many antara pengajuan dan peraturan. Mencatat peraturan yang dipilih sebagai dasar hukum surat.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengajuan','INT UNSIGNED','FK → pengajuan.id','1'],
        ['id_peraturan','INT UNSIGNED','FK → peraturan.id','1'],
        ['created_at','DATETIME','NULL','2025-03-10 09:10:00'],
    ]],
    ['num'=>'6.11','name'=>'lampiran_pengajuan','desc'=>'Menyimpan metadata file lampiran yang diunggah untuk mendukung sebuah pengajuan surat.',
     'cols'=>[
        ['id','BIGINT UNSIGNED','PK — AUTO_INCREMENT (BIGINT untuk skala besar)','1'],
        ['id_pengajuan','INT UNSIGNED','FK → pengajuan.id','1'],
        ['nama_file','VARCHAR(255)','NOT NULL','surat_pengantar.pdf'],
        ['filepath','VARCHAR(255)','NOT NULL','lampiran/pengajuan_1/surat.pdf'],
        ['created_at','TIMESTAMP','Default: CURRENT_TIMESTAMP','2025-03-10 09:12:00'],
        ['updated_at','TIMESTAMP','Default: CURRENT_TIMESTAMP','2025-03-10 09:12:00'],
    ]],
    ['num'=>'6.12','name'=>'riwayat_pengajuan','desc'=>'Log audit trail setiap aksi yang terjadi pada pengajuan (dibuat, dikirim, disetujui, ditolak, revisi, dll.).',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_pengajuan','INT UNSIGNED','FK → pengajuan.id','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id (Aktor aksi)','1'],
        ['aksi','VARCHAR(50)','NOT NULL','DIKIRIM_VERIFIKATOR'],
        ['catatan_aksi','TEXT','NULL','Dikirim ke Tim Verifikasi A'],
        ['versi','INT UNSIGNED','NOT NULL, Default: 1','2'],
        ['snapshot_konten','JSON','NULL','{"judul":"SK Kenaikan..."}'],
        ['created_at','DATETIME','NULL','2025-03-15 10:30:00'],
    ]],
    ['num'=>'6.13','name'=>'dokumen','desc'=>'Dokumen yang telah selesai diproses dan diterbitkan secara resmi. Entitas ini terpisah dari pengajuan karena bersifat final.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_nomor_dokumen','INT UNSIGNED','FK → nomor_dokumen.id','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id (Penerbit)','1'],
        ['tipe','ENUM(SK,ST)','NOT NULL','SK'],
        ['nama_dokumen','VARCHAR(200)','NOT NULL','SK Kenaikan Jabatan Andi'],
        ['tgl_dokumen','DATE','NOT NULL','2025-03-20'],
        ['filepath','VARCHAR(255)','NOT NULL','dokumen/SK-001-2025.pdf'],
        ['catatan','TEXT','NULL','-'],
        ['dari_pengajuan','TINYINT(1)','NOT NULL, Default: 0','1'],
        ['kode_unik','VARCHAR(20)','UNIQUE, NULL','K2025ABC12'],
        ['rendered_body','LONGTEXT','NULL','(HTML/DOCX konten final)'],
        ['verified_at','DATETIME','NULL','2025-03-20 14:00:00'],
        ['is_deleted','TINYINT(1)','NOT NULL, Default: 0','0'],
        ['created_at','DATETIME','NULL','2025-03-20 14:00:00'],
        ['updated_at','DATETIME','NULL','2025-03-20 14:00:00'],
    ]],
    ['num'=>'6.14','name'=>'anggota_dokumen','desc'=>'Tabel junction Many-to-Many antara dokumen dan pengguna. Mencatat pegawai yang terlibat dalam dokumen yang telah terbit.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_dokumen','INT UNSIGNED','FK → dokumen.id','1'],
        ['id_pengguna','INT UNSIGNED','FK → pengguna.id','3'],
        ['created_at','DATETIME','NULL','2025-03-20 14:05:00'],
    ]],
    ['num'=>'6.15','name'=>'grup_verifikasi_dokumen','desc'=>'Tabel junction Many-to-Many antara dokumen dan grup_verifikasi. Mencatat grup yang mengesahkan dokumen yang telah terbit.',
     'cols'=>[
        ['id','INT UNSIGNED','PK — AUTO_INCREMENT','1'],
        ['id_dokumen','INT UNSIGNED','FK → dokumen.id','1'],
        ['id_grup_verifikasi','INT UNSIGNED','FK → grup_verifikasi.id','1'],
        ['created_at','DATETIME','NULL','2025-03-20 14:10:00'],
    ]],
];

foreach($finalTables as $tDef) {
    $section->addText('Tabel '.$tDef['num'].' — '.$tDef['name'], $CAP, $pCap);
    $section->addText($tDef['desc'], $BODY, ['alignment'=>Jc::BOTH,'spaceAfter'=>80]);
    $ti = $section->addTable('T');
    hRow($ti,['Kolom','Tipe Data','Kunci / Constraint','Contoh Data'],[2300,1700,2700,2600],$hCell,$TH);
    foreach($tDef['cols'] as $i=>$c) {
        $st=$i%2===0?$aCell:$wCell;
        $r=$ti->addRow();
        $isKey=strpos($c[0],'PK')!==false||strpos($c[0],'FK')!==false;
        $r->addCell(2300,$st)->addText($c[0],['size'=>9,'bold'=>$isKey,'name'=>'Times New Roman','color'=>$isKey?'1F3864':'000000']);
        $r->addCell(1700,$st)->addText($c[1],['size'=>9,'name'=>'Times New Roman','color'=>'555555']);
        $r->addCell(2700,$st)->addText($c[2],['size'=>8,'name'=>'Times New Roman','italic'=>true]);
        $r->addCell(2600,$st)->addText($c[3],['size'=>9,'name'=>'Times New Roman','color'=>'333333']);
    }
    $section->addTextBreak(1);
}
$section->addPageBreak();

// =========================================================
// BAB 7 — RELASI
// =========================================================
$section->addText('BAB 7 — RELASI ANTAR TABEL', $H1, $pLeft);
$section->addText('Tabel 7.1 — Daftar Seluruh Relasi Antar Tabel', $CAP, $pCap);
$t7 = $section->addTable('T');
hRow($t7,['Tabel Induk','Jenis Relasi','Tabel Anak / Terkait','Via / Keterangan'],[2200,1600,2500,3000],$hCell,$TH);
$rels = [
    ['pengguna','One-to-Many','template_surat','pengguna.id → template_surat.id_pengguna'],
    ['pengguna','One-to-Many','peraturan','pengguna.id → peraturan.id_pengguna'],
    ['pengguna','One-to-Many','pengajuan','pengguna.id → pengajuan.id_pengguna'],
    ['pengguna','One-to-Many','grup_verifikasi','pengguna.id → grup_verifikasi.id_pengguna (Ketua)'],
    ['pengguna','Many-to-Many','grup_verifikasi','Via: anggota_grup_verifikasi'],
    ['pengguna','Many-to-Many','pengajuan','Via: anggota_pengajuan (sebagai peserta/diusulkan)'],
    ['pengguna','Many-to-Many','dokumen','Via: anggota_dokumen'],
    ['pengajuan','Many-to-Many','grup_verifikasi','Via: grup_verifikasi_pengajuan'],
    ['pengajuan','Many-to-Many','peraturan','Via: pengajuan_peraturan'],
    ['pengajuan','One-to-Many','lampiran_pengajuan','pengajuan.id → lampiran_pengajuan.id_pengajuan'],
    ['pengajuan','One-to-Many','riwayat_pengajuan','pengajuan.id → riwayat_pengajuan.id_pengajuan'],
    ['dokumen','One-to-One','nomor_dokumen','dokumen.id_nomor_dokumen → nomor_dokumen.id'],
    ['dokumen','Many-to-Many','grup_verifikasi','Via: grup_verifikasi_dokumen'],
    ['grup_verifikasi','One-to-Many','pengajuan','grup_verifikasi.id → pengajuan.id_grup_verifikasi_verifikator'],
];
foreach($rels as $i=>$r) dRow($t7,$r,[2200,1600,2500,3000],$i%2===0,$aCell,$wCell,$TDS);
$section->addPageBreak();

// =========================================================
// BAB 8 — KESIMPULAN
// =========================================================
$section->addText('BAB 8 — KESIMPULAN', $H1, $pLeft);
$section->addText(
    'Berdasarkan analisis normalisasi dari UNF hingga BCNF, basis data KERNAS telah dirancang dengan baik dan memenuhi standar normalisasi tertinggi. Berikut ringkasan pencapaian setiap tahap:',
    $BODY, $pBoth
);
$t8 = $section->addTable('T');
hRow($t8,['Tahap','Status','Hasil Utama'],[1600,1200,6500],$hCell,$TH);
$conc = [
    ['UNF → 1NF','✓ LULUS','Data atomik, kelompok berulang dipisah. 61 atribut dipecah menjadi 5+ entitas awal.'],
    ['1NF → 2NF','✓ LULUS','Tidak ada dependensi parsial. Surrogate PK (id auto-increment) pada semua tabel mengeliminasi masalah ini.'],
    ['2NF → 3NF','✓ LULUS','6 dependensi transitif ditemukan dan diselesaikan. Entitas pengguna, nomor dokumen, peraturan, grup dipisah mandiri.'],
    ['3NF → BCNF','✓ LULUS','Semua determinan adalah superkey. Tidak ada anomali INSERT/UPDATE/DELETE. Integritas referensial dijaga via Foreign Key.'],
];
foreach($conc as $i=>$r) dRow($t8,$r,[1600,1200,6500],$i%2===0,$aCell,$wCell,$TDS);
$section->addTextBreak(1);
$section->addText(
    'Hasil akhir: Basis data KERNAS terdiri dari 15 tabel yang saling terhubung melalui Foreign Key dengan total 13 relasi (10 One-to-Many, 3 Many-to-Many via tabel junction). Desain ini menjamin tidak ada redundansi data yang tidak perlu, setiap atribut berada pada tempatnya yang benar, dan sistem dapat berkembang dengan baik seiring pertumbuhan data.',
    $BODY, $pBoth
);
$section->addTextBreak(2);
$section->addText('--- Laporan dibuat secara otomatis dari sistem KERNAS —— Tanggal: '.date('d F Y, H:i').' WIB ---', $SMALL, $pCenter);

// =========================================================
// SAVE
// =========================================================
$out = __DIR__.'/storage/app/public/Laporan_Normalisasi_Database_KERNAS.docx';
$phpWord->save($out, 'Word2007');
echo "SELESAI: " . $out . "\n";
