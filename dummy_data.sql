-- Dummy Data for sk_polibatam

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE pengguna;
TRUNCATE TABLE grup_verifikasi;
TRUNCATE TABLE anggota_grup_verifikasi;
TRUNCATE TABLE nomor_dokumen;
TRUNCATE TABLE dokumen;
TRUNCATE TABLE anggota_dokumen;
TRUNCATE TABLE grup_verifikasi_dokumen;
TRUNCATE TABLE template_surat;
TRUNCATE TABLE pengajuan;
TRUNCATE TABLE anggota_pengajuan;
TRUNCATE TABLE grup_verifikasi_pengajuan;
TRUNCATE TABLE riwayat_pengajuan;
TRUNCATE TABLE log_audit;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Pengguna (Password is 'password')
INSERT INTO pengguna (id, nip, username, password, nama, unit, jabatan, is_admin, created_at, updated_at) VALUES
(1, '199001012020121001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'Pusat Komputer', 'Admin IT', 1, NOW(), NOW()),
(2, '198501012010121002', 'pegawai', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Pegawai', 'Teknik Informatika', 'Dosen', 0, NOW(), NOW()),
(3, '197501012005121003', 'verifikator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Verifikator', 'Teknik Informatika', 'Ketua Jurusan', 0, NOW(), NOW());

-- 2. Grup Verifikasi
INSERT INTO grup_verifikasi (id, nama_grup, id_pengguna, is_deleted, created_at, updated_at) VALUES
(1, 'Grup Verifikasi Teknik Informatika', 1, 0, NOW(), NOW());

-- 3. Anggota Grup Verifikasi (Assign Siti to the group)
INSERT INTO anggota_grup_verifikasi (id_grup_verifikasi, id_pengguna, created_at) VALUES
(1, 3, NOW());

-- 4. Template Surat
INSERT INTO template_surat (id, id_pengguna, tipe, nama_template, filepath, versi, is_aktif, created_at, updated_at) VALUES
(1, 1, 'SK', 'Template SK Default', 'templates/sk_default.docx', 1, 1, NOW(), NOW()),
(2, 1, 'ST', 'Template ST Default', 'templates/st_default.docx', 1, 1, NOW(), NOW());

-- 5. Pengajuan
INSERT INTO pengajuan (id, id_pengguna, id_grup_verifikasi_verifikator, judul, tipe, status, ada_lampiran, urutan_antrian, is_deleted, created_at, updated_at) VALUES
(1, 2, 1, 'Pengajuan SK Pembimbing Akademik', 'SK', 'POSTED', 0, 1, 0, NOW(), NOW()),
(2, 2, 1, 'Pengajuan ST Perjalanan Dinas', 'ST', 'APPROVED', 0, 2, 0, NOW(), NOW());

-- 6. Anggota Pengajuan
INSERT INTO anggota_pengajuan (id_pengajuan, id_pengguna, created_at) VALUES
(1, 2, NOW()),
(2, 2, NOW());

-- 7. Riwayat Pengajuan
INSERT INTO riwayat_pengajuan (id_pengajuan, id_pengguna, aksi, catatan_aksi, versi, created_at) VALUES
(1, 2, 'DIBUAT', 'Membuat pengajuan SK', 1, NOW()),
(2, 2, 'DIBUAT', 'Membuat pengajuan ST', 1, NOW()),
(2, 3, 'DISETUJUI', 'Disetujui oleh Verifikator', 1, NOW());

-- 8. Nomor Dokumen
INSERT INTO nomor_dokumen (id, tipe, tahun, urutan, nomor_terformat, created_at) VALUES
(1, 'ST', 2026, 1, 'ST-2026-001', NOW());

-- 9. Dokumen
INSERT INTO dokumen (id, id_nomor_dokumen, id_pengguna, tipe, nama_dokumen, tgl_dokumen, filepath, dari_pengajuan, kode_unik, is_deleted, created_at, updated_at) VALUES
(1, 1, 1, 'ST', 'ST Perjalanan Dinas', CURDATE(), 'dokumen/st_2026_001.pdf', 1, 'UNIQ12345', 0, NOW(), NOW());

-- 10. Anggota Dokumen
INSERT INTO anggota_dokumen (id_dokumen, id_pengguna, created_at) VALUES
(1, 2, NOW());

-- 11. Log Audit
INSERT INTO log_audit (id_pengguna, aksi, tipe_model, id_model, created_at) VALUES
(1, 'CREATE', 'pengguna', 2, NOW()),
(1, 'CREATE', 'pengguna', 3, NOW());
