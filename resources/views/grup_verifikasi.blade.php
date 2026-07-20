@extends('layouts.app')

@section('title', 'Manajemen Grup Verifikasi — KERNAS')

@section('head')
    <style>
        [x-cloak] { display: none !important; }
        .group-item { transition: all 0.15s ease; }
        .group-item.active { background: #eff6ff; border-color: #3b82f6 !important; }
        .group-item:hover:not(.active) { background: #f9fafb; }
    </style>
@endsection

@section('content')
    <div x-data="grupManajemen(@js($grups), @js($karyawan))" class="h-[calc(100vh-64px)] flex overflow-hidden">
        {{-- Sidebar Kiri: Daftar Grup --}}
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 z-10">
            <div class="px-4 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-800">Grup Verifikasi</h2>
                <button @click="openGrupModal()" class="flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Grup
                </button>
            </div>
            
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" x-model="searchGrup" placeholder="Cari grup..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto py-2">
                <template x-for="grup in filteredGrups" :key="grup.id">
                    <div @click="selectGrup(grup)"
                         class="group-item mx-2 mb-1 rounded-lg border border-transparent cursor-pointer"
                         :class="selectedGrup?.id === grup.id ? 'active' : ''">
                        <div class="px-3 py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate" x-text="grup.nama_grup"></p>
                                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-400 font-medium">
                                        <span class="px-1.5 py-0.5 rounded bg-sky-100 text-sky-700" x-text="'Tahap ' + (grup.tingkat || '?')"></span>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                            <span x-text="grup.pengguna ? grup.pengguna.length + ' Anggota' : '0 Anggota'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="filteredGrups.length === 0" class="text-center py-6 text-sm text-gray-500">
                    Tidak ada grup ditemukan.
                </div>
            </div>
        </div>

        {{-- Area Kanan atau side bar: Detail Grup & Anggota --}}
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            <template x-if="selectedGrup">
                <div class="flex-1 flex flex-col h-full">
                    {{-- Header Detail Grup --}}
                    <div class="bg-white border-b border-gray-200 px-6 py-5 flex items-start justify-between flex-shrink-0">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl font-bold text-gray-900" x-text="selectedGrup.nama_grup"></h2>
                                <span class="px-2 py-1 text-xs font-semibold rounded-md bg-sky-100 text-sky-700" x-text="'Tahap ' + (selectedGrup.tingkat || '?')"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openGrupModal(selectedGrup)" class="p-2 text-gray-500 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors border border-gray-200 bg-white" title="Edit Grup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                            </button>
                            <button @click="deleteGrup(selectedGrup.id)" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-gray-200 bg-white" title="Hapus Grup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Daftar Anggota --}}
                    <div class="p-6 flex-1 overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-800">Daftar Anggota (<span x-text="selectedGrup.pengguna ? selectedGrup.pengguna.length : 0"></span>)</h3>
                            <button @click="openAnggotaModal()" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-100 rounded-lg hover:bg-sky-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                                Tambah Anggota
                            </button>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3 font-semibold">Nama / NIP</th>
                                        <th class="px-4 py-3 font-semibold">Jabatan</th>
                                        <th class="px-4 py-3 font-semibold">Unit</th>
                                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="anggota in selectedGrup.pengguna" :key="anggota.id">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3">
                                                <p class="text-sm font-bold text-gray-900" x-text="anggota.nama"></p>
                                                <p class="text-xs text-gray-500" x-text="anggota.nip"></p>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700" x-text="anggota.jabatan || '-'"></td>
                                            <td class="px-4 py-3 text-sm text-gray-700" x-text="anggota.unit || '-'"></td>
                                            <td class="px-4 py-3 text-right">
                                                <button @click="removeAnggota(anggota.id)" class="text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded text-xs font-semibold transition-colors">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="!selectedGrup.pengguna || selectedGrup.pengguna.length === 0">
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                            Belum ada anggota di grup ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400" x-show="!selectedGrup">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                <p class="text-base font-medium text-gray-500">Pilih Grup Verifikasi</p>
                <p class="text-sm mt-1">Pilih grup dari panel kiri untuk melihat dan mengelola anggotanya.</p>
            </div>
        </div>

        {{-- Modal Tambah/Edit Grup --}}
        <div x-show="grupModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="grupModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10" x-show="grupModalOpen" x-transition>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900" x-text="formGrup.id ? 'Edit Grup Verifikasi' : 'Tambah Grup Baru'"></h2>
                    <button @click="grupModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Grup <span class="text-red-500">*</span></label>
                        <input type="text" x-model="formGrup.nama_grup" placeholder="Contoh: Wadir 1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tingkat/Tahapan Grup <span class="text-red-500">*</span></label>
                        <select x-model="formGrup.tingkat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-sm">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="1">Tahap 1</option>
                            <option value="2">Tahap 2</option>
                            <option value="3">Tahap 3</option>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1">Grup ini akan tampil sebagai pilihan di form pengajuan berdasarkan tingkat yang dipilih.</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button @click="grupModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button @click="saveGrup" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700">Simpan</button>
                </div>
            </div>
        </div>

        {{-- Modal Tambah Anggota --}}
        <div x-show="anggotaModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="anggotaModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg z-10 flex flex-col max-h-[80vh]" x-show="anggotaModalOpen" x-transition>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h2 class="text-lg font-bold text-gray-900">Tambah Anggota ke <span x-text="selectedGrup?.nama_grup" class="text-sky-600"></span></h2>
                    <button @click="anggotaModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-4 border-b border-gray-100 shrink-0">
                    <input type="text" x-model="searchKaryawan" placeholder="Cari nama atau NIP karyawan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-sm">
                </div>
                <div class="flex-1 overflow-y-auto p-2">
                    <template x-for="k in availableKaryawan" :key="k.id">
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-bold text-gray-900" x-text="k.nama"></p>
                                <p class="text-xs text-gray-500" x-text="k.nip + ' · ' + (k.jabatan || 'Tanpa Jabatan')"></p>
                            </div>
                            <button @click="addAnggota(k.id)" class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-500 rounded-md hover:bg-emerald-600 transition-colors">
                                Tambah
                            </button>
                        </div>
                    </template>
                    <div x-show="availableKaryawan.length === 0" class="text-center py-8 text-sm text-gray-500">
                        Tidak ada karyawan yang tersedia untuk ditambahkan.
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast Notifikasi --}}
        <div x-show="toast.show" x-cloak x-transition.opacity class="fixed bottom-4 right-4 z-50 flex items-center p-4 space-x-3 bg-gray-900 text-white rounded-xl shadow-lg">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div class="text-sm font-medium" x-text="toast.message"></div>
        </div>
    </div>

    <script>
        function grupManajemen(initialGrups, allKaryawan) {
            return {
                grups: initialGrups,
                karyawan: allKaryawan,
                searchGrup: '',
                searchKaryawan: '',
                selectedGrup: null,
                
                grupModalOpen: false,
                formGrup: { id: null, nama_grup: '', tingkat: '' },

                anggotaModalOpen: false,

                toast: { show: false, message: '' },

                init() {
                    if (this.grups.length > 0) {
                        this.selectedGrup = this.grups[0];
                    }
                },

                get filteredGrups() {
                    if (!this.searchGrup) return this.grups;
                    const q = this.searchGrup.toLowerCase();
                    return this.grups.filter(g => g.nama_grup.toLowerCase().includes(q));
                },

                get availableKaryawan() {
                    if (!this.selectedGrup) return [];
                    // Karyawan yang belum ada di grup ini
                    const existingIds = (this.selectedGrup.pengguna || []).map(p => p.id);
                    let available = this.karyawan.filter(k => !existingIds.includes(k.id));
                    
                    if (this.searchKaryawan) {
                        const q = this.searchKaryawan.toLowerCase();
                        available = available.filter(k => k.nama.toLowerCase().includes(q) || k.nip.toLowerCase().includes(q));
                    }
                    return available;
                },

                selectGrup(grup) {
                    this.selectedGrup = grup;
                },

                openGrupModal(grup = null) {
                    if (grup) {
                        this.formGrup = { id: grup.id, nama_grup: grup.nama_grup, tingkat: grup.tingkat || '' };
                    } else {
                        this.formGrup = { id: null, nama_grup: '', tingkat: '' };
                    }
                    this.grupModalOpen = true;
                },

                async saveGrup() {
                    if (!this.formGrup.nama_grup || !this.formGrup.tingkat) { alert('Nama grup dan tingkat wajib diisi!'); return; }
                    
                    const isEdit = !!this.formGrup.id;
                    const url = isEdit ? `/setup/grup-verifikasi/${this.formGrup.id}` : `/setup/grup-verifikasi`;
                    const method = isEdit ? 'PUT' : 'POST';

                    try {
                        const res = await fetch(url, {
                            method: method,
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.formGrup)
                        });
                        const data = await res.json();
                        if (res.ok) {
                            if (isEdit) {
                                const index = this.grups.findIndex(g => g.id === this.formGrup.id);
                                if (index !== -1) {
                                    // Preserve users array while updating info
                                    const users = this.grups[index].pengguna;
                                    this.grups[index] = data.grup;
                                    if(!data.grup.pengguna) this.grups[index].pengguna = users;
                                    
                                    if(this.selectedGrup && this.selectedGrup.id === this.formGrup.id) {
                                        this.selectedGrup = this.grups[index];
                                    }
                                }
                            } else {
                                if(!data.grup.pengguna) data.grup.pengguna = [];
                                this.grups.unshift(data.grup);
                                this.selectGrup(this.grups[0]);
                            }
                            this.grupModalOpen = false;
                            this.showToast(data.message);
                        } else {
                            alert(data.message || 'Terjadi kesalahan.');
                        }
                    } catch (e) { alert('Server error.'); }
                },

                async deleteGrup(id) {
                    const konfirmasi = await appConfirm('Yakin ingin menghapus grup ini beserta relasi anggotanya?', true);
                    if(!konfirmasi.isConfirmed) return;
                    try {
                        const res = await fetch(`/setup/grup-verifikasi/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        if (res.ok) {
                            this.grups = this.grups.filter(g => g.id !== id);
                            if (this.selectedGrup && this.selectedGrup.id === id) {
                                this.selectedGrup = this.grups.length > 0 ? this.grups[0] : null;
                            }
                            this.showToast('Grup dihapus.');
                        }
                    } catch (e) {}
                },

                openAnggotaModal() {
                    this.searchKaryawan = '';
                    this.anggotaModalOpen = true;
                },

                async addAnggota(id_pengguna) {
                    if (!this.selectedGrup) return;
                    try {
                        const res = await fetch(`/setup/grup-verifikasi/${this.selectedGrup.id}/anggota`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id_pengguna })
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if(!this.selectedGrup.pengguna) this.selectedGrup.pengguna = [];
                            this.selectedGrup.pengguna.push(data.pengguna);
                            this.showToast(data.message);
                            
                            // Close modal if no more available users
                            if(this.availableKaryawan.length === 0) this.anggotaModalOpen = false;
                        }
                    } catch (e) {}
                },

                async removeAnggota(id_pengguna) {
                    if (!this.selectedGrup) return;
                    const konfirmasi = await appConfirm('Keluarkan anggota ini dari grup?', true);
                    if (!konfirmasi.isConfirmed) return;
                    try {
                        const res = await fetch(`/setup/grup-verifikasi/${this.selectedGrup.id}/anggota/${id_pengguna}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        if (res.ok) {
                            this.selectedGrup.pengguna = this.selectedGrup.pengguna.filter(p => p.id !== id_pengguna);
                            this.showToast('Anggota dikeluarkan.');
                        }
                    } catch (e) {}
                },

                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            }
        }
    </script>
@endsection