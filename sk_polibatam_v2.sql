-- ============================================================
-- DATABASE: sk_polibatam
-- Aplikasi Distribusi SK dan Surat Tugas — Polibatam
-- Versi 2 — Auth lokal (tanpa API Polibatam), dinormalisasi ke 3NF
-- Perubahan dari v1:
--   1. pengguna: tambah username, password, nama, unit, jabatan
--   2. dibuat_oleh VARCHAR → id_pengguna INT FK (3NF)
--   3. nip_pegawai di junction → id_pengguna INT FK (3NF)
--   4. riwayat_pengajuan: tambah id_pengguna FK
--   5. log_audit: ganti id_pengajuan → id_pengguna FK
-- ============================================================

CREATE DATABASE IF NOT EXISTS sk_polibatam
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sk_polibatam;

-- ============================================================
-- TABEL 1: pengguna
-- Sekarang menyimpan credentials lokal + data pegawai lengkap
-- ============================================================
CREATE TABLE pengguna (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    nip         VARCHAR(20)      NOT NULL,
    username    VARCHAR(100)     NOT NULL,
    password    VARCHAR(255)     NOT NULL,
    nama        VARCHAR(100)     NOT NULL,
    unit        VARCHAR(100)     NOT NULL,
    jabatan     VARCHAR(100)     NULL,
    is_admin    TINYINT(1)       NOT NULL DEFAULT 0,
    created_at  DATETIME         NULL,
    updated_at  DATETIME         NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_pengguna_nip      (nip),
    UNIQUE KEY uq_pengguna_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 2: grup_verifikasi
-- dibuat_oleh VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE grup_verifikasi (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    nama_grup   VARCHAR(100)     NOT NULL,
    id_pengguna INT UNSIGNED     NOT NULL,
    is_deleted  TINYINT(1)       NOT NULL DEFAULT 0,
    created_at  DATETIME         NULL,
    updated_at  DATETIME         NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_gv_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 3: anggota_grup_verifikasi
-- nip_pegawai VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE anggota_grup_verifikasi (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_grup_verifikasi INT UNSIGNED NOT NULL,
    id_pengguna        INT UNSIGNED NOT NULL,
    created_at         DATETIME     NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_agv_grup_pengguna (id_grup_verifikasi, id_pengguna),
    CONSTRAINT fk_agv_grup
        FOREIGN KEY (id_grup_verifikasi) REFERENCES grup_verifikasi(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_agv_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 4: nomor_dokumen
-- Tidak ada perubahan dari v1
-- ============================================================
CREATE TABLE nomor_dokumen (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    tipe            ENUM('SK','ST') NOT NULL,
    tahun           YEAR            NOT NULL,
    urutan          INT UNSIGNED    NOT NULL,
    nomor_terformat VARCHAR(20)     NOT NULL,
    created_at      DATETIME        NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nomor_terformat   (nomor_terformat),
    UNIQUE KEY uq_tipe_tahun_urutan (tipe, tahun, urutan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 5: dokumen
-- dibuat_oleh VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE dokumen (
    id               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    id_nomor_dokumen INT UNSIGNED    NOT NULL,
    id_pengguna      INT UNSIGNED    NOT NULL,
    tipe             ENUM('SK','ST') NOT NULL,
    nama_dokumen     VARCHAR(200)    NOT NULL,
    tgl_dokumen      DATE            NOT NULL,
    filepath         VARCHAR(255)    NOT NULL,
    catatan          TEXT            NULL,
    dari_pengajuan   TINYINT(1)      NOT NULL DEFAULT 0,
    kode_unik        VARCHAR(20)     NULL,
    rendered_body    LONGTEXT        NULL,
    verified_at      DATETIME        NULL,
    is_deleted       TINYINT(1)      NOT NULL DEFAULT 0,
    created_at       DATETIME        NULL,
    updated_at       DATETIME        NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_dokumen_kode_unik (kode_unik),
    CONSTRAINT fk_dok_nomor
        FOREIGN KEY (id_nomor_dokumen) REFERENCES nomor_dokumen(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_dok_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 6: anggota_dokumen
-- nip_pegawai VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE anggota_dokumen (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_dokumen  INT UNSIGNED NOT NULL,
    id_pengguna INT UNSIGNED NOT NULL,
    created_at  DATETIME     NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_adok_dok_pengguna (id_dokumen, id_pengguna),
    CONSTRAINT fk_adok_dokumen
        FOREIGN KEY (id_dokumen)  REFERENCES dokumen(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_adok_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 7: grup_verifikasi_dokumen
-- Tidak ada perubahan struktural
-- ============================================================
CREATE TABLE grup_verifikasi_dokumen (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_dokumen         INT UNSIGNED NOT NULL,
    id_grup_verifikasi INT UNSIGNED NOT NULL,
    created_at         DATETIME     NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_gvd_dokumen
        FOREIGN KEY (id_dokumen)         REFERENCES dokumen(id)         ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_gvd_grup
        FOREIGN KEY (id_grup_verifikasi) REFERENCES grup_verifikasi(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 8: template_surat
-- dibuat_oleh VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE template_surat (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    id_pengguna   INT UNSIGNED    NOT NULL,
    tipe          ENUM('SK','ST') NOT NULL,
    nama_template VARCHAR(100)    NOT NULL,
    filepath      VARCHAR(255)    NOT NULL,
    versi         INT UNSIGNED    NOT NULL DEFAULT 1,
    is_aktif      TINYINT(1)      NOT NULL DEFAULT 1,
    created_at    DATETIME        NULL,
    updated_at    DATETIME        NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_tmpl_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 9: pengajuan
-- dibuat_oleh VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE pengajuan (
    id                           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    id_pengguna                  INT UNSIGNED    NOT NULL,
    id_grup_verifikasi_verifikator INT UNSIGNED  NOT NULL,
    judul                        VARCHAR(200)    NOT NULL,
    tipe                         ENUM('SK','ST') NOT NULL,
    status                       ENUM('POSTED','APPROVED','REJECTED','PUBLISHED')
                                                 NOT NULL DEFAULT 'POSTED',
    daftar_menimbang             TEXT            NULL,
    daftar_memperhatikan         TEXT            NULL,
    daftar_memutuskan            TEXT            NULL,
    ada_lampiran                 TINYINT(1)      NOT NULL DEFAULT 0,
    filepath                     VARCHAR(255)    NULL,
    filepath_lampiran            VARCHAR(255)    NULL,
    rencana_pengambilan          DATE            NULL,
    tgl_terbit                   DATE            NULL,
    catatan                      TEXT            NULL,
    urutan_antrian               INT UNSIGNED    NULL,
    nomor_diusulkan              VARCHAR(30)     NULL,
    is_deleted                   TINYINT(1)      NOT NULL DEFAULT 0,
    created_at                   DATETIME        NULL,
    updated_at                   DATETIME        NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_pgj_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pgj_grup_verifikasi
        FOREIGN KEY (id_grup_verifikasi_verifikator) REFERENCES grup_verifikasi(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 10: anggota_pengajuan
-- nip_pegawai VARCHAR → id_pengguna FK
-- ============================================================
CREATE TABLE anggota_pengajuan (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pengajuan INT UNSIGNED NOT NULL,
    id_pengguna  INT UNSIGNED NOT NULL,
    created_at   DATETIME     NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_apgj_pgj_pengguna (id_pengajuan, id_pengguna),
    CONSTRAINT fk_apgj_pengajuan
        FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_apgj_pengguna
        FOREIGN KEY (id_pengguna)  REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 11: grup_verifikasi_pengajuan
-- Tidak ada perubahan struktural
-- ============================================================
CREATE TABLE grup_verifikasi_pengajuan (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pengajuan       INT UNSIGNED NOT NULL,
    id_grup_verifikasi INT UNSIGNED NOT NULL,
    created_at         DATETIME     NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_gvp_pengajuan
        FOREIGN KEY (id_pengajuan)       REFERENCES pengajuan(id)       ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_gvp_grup
        FOREIGN KEY (id_grup_verifikasi) REFERENCES grup_verifikasi(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 12: riwayat_pengajuan
-- Tambah id_pengguna FK — mencatat siapa yang melakukan aksi
-- ============================================================
CREATE TABLE riwayat_pengajuan (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pengajuan    INT UNSIGNED NOT NULL,
    id_pengguna     INT UNSIGNED NOT NULL,
    aksi            ENUM('DIBUAT','DIEDIT','DISETUJUI','DITOLAK','DITERBITKAN') NOT NULL,
    catatan_aksi    TEXT         NULL,
    versi           INT UNSIGNED NOT NULL DEFAULT 1,
    snapshot_konten JSON         NULL,
    created_at      DATETIME     NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_rwy_pengajuan
        FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_rwy_pengguna
        FOREIGN KEY (id_pengguna)  REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL 13: log_audit
-- id_pengajuan FK → id_pengguna FK (log untuk seluruh sistem)
-- ============================================================
CREATE TABLE log_audit (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pengguna INT UNSIGNED NOT NULL,
    aksi        VARCHAR(100) NOT NULL,
    tipe_model  VARCHAR(100) NOT NULL,
    id_model    INT UNSIGNED NOT NULL,
    nilai_lama  JSON         NULL,
    nilai_baru  JSON         NULL,
    created_at  DATETIME     NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_log_pengguna
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SELESAI — 13 tabel, Normalisasi 3NF
-- Auth lokal — tidak memerlukan API Polibatam
-- ============================================================
