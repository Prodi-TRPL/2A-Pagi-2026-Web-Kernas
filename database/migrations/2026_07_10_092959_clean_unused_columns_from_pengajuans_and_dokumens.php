<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengajuan')) {
            Schema::table('pengajuan', function (Blueprint $table) {
                if (Schema::hasColumn('pengajuan', 'id_grup_verifikasi_verifikator')) {
                    // Try dropping foreign key first if it exists
                    try {
                        $table->dropForeign('fk_pgj_grup_verifikasi');
                    } catch (\Exception $e) {
                        // ignore if FK doesn't exist or is named differently
                    }
                    $table->dropColumn('id_grup_verifikasi_verifikator');
                }
                if (Schema::hasColumn('pengajuan', 'rencana_pengambilan')) {
                    $table->dropColumn('rencana_pengambilan');
                }
                if (Schema::hasColumn('pengajuan', 'ada_lampiran')) {
                    $table->dropColumn('ada_lampiran');
                }
                if (Schema::hasColumn('pengajuan', 'filepath_lampiran')) {
                    $table->dropColumn('filepath_lampiran');
                }
                if (Schema::hasColumn('pengajuan', 'tgl_terbit')) {
                    $table->dropColumn('tgl_terbit');
                }
            });
        }

        if (Schema::hasTable('dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                if (Schema::hasColumn('dokumen', 'kode_unik')) {
                    $table->dropColumn('kode_unik');
                }
                if (Schema::hasColumn('dokumen', 'rendered_body')) {
                    $table->dropColumn('rendered_body');
                }
                if (Schema::hasColumn('dokumen', 'verified_at')) {
                    $table->dropColumn('verified_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengajuan')) {
            Schema::table('pengajuan', function (Blueprint $table) {
                $table->integer('id_grup_verifikasi_verifikator')->nullable();
                $table->string('rencana_pengambilan')->nullable();
                $table->boolean('ada_lampiran')->default(false);
                $table->string('filepath_lampiran')->nullable();
                $table->timestamp('tgl_terbit')->nullable();
            });
        }

        if (Schema::hasTable('dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->string('kode_unik', 50)->nullable();
                $table->text('rendered_body')->nullable();
                $table->timestamp('verified_at')->nullable();
            });
        }
    }
};
