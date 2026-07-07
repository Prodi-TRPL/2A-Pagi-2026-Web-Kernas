@extends('layouts.app')

@section('title', 'Template Surat — Polibatam')

@section('head')
    <style>
        [x-cloak] { display: none !important; }
        .template-item { transition: all 0.15s ease; }
        .template-item.active { background: #eff6ff; border-color: #3b82f6 !important; }
        .template-item:hover:not(.active) { background: #f9fafb; }
    </style>
@endsection

@section('content')
    <div x-data="templateManajemen(@js($templates))" class="h-[calc(100vh-64px)] flex overflow-hidden">
        {{-- Sidebar Kiri --}}
        <div class="w-72 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">
            <div class="px-4 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-800">Template Surat</h2>
                    <button @click="alert('Fitur tambah sementara dinonaktifkan di V4')"
                            class="flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors">
                        Versi Baru
                    </button>
                </div>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                    <button @click="filterJenis = 'SK'"
                            :class="filterJenis === 'SK' ? 'bg-white text-sky-600 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-1.5 text-xs rounded-md transition-all">SK</button>
                    <button @click="filterJenis = 'ST'"
                            :class="filterJenis === 'ST' ? 'bg-white text-sky-600 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-1.5 text-xs rounded-md transition-all">ST</button>
                    <button @click="filterJenis = 'semua'"
                            :class="filterJenis === 'semua' ? 'bg-white text-sky-600 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-1.5 text-xs rounded-md transition-all">Semua</button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto py-2">
                <template x-for="tmpl in templates" :key="tmpl.id">
                    <div x-show="filterJenis === 'semua' || filterJenis === tmpl.jenis" @click="selectTemplate(tmpl)"
                         class="template-item mx-2 mb-1 rounded-lg border cursor-pointer"
                         :class="selectedTemplate?.id === tmpl.id ? 'active border-blue-300' : 'border-transparent'">
                        <div class="px-3 py-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold px-1.5 py-0.5 rounded" :class="tmpl.jenis === 'SK' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700'" x-text="tmpl.jenis + ' v' + tmpl.versi"></span>
                                        <span x-show="tmpl.is_aktif" class="text-xs px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Aktif</span>
                                    </div>
                                    <p class="text-xs font-medium text-gray-700 truncate" x-text="tmpl.nama"></p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="tmpl.dibuat_oleh + ' · ' + tmpl.tgl_dibuat"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Area Kanan: Editor --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white border-b border-gray-200 px-5 py-3 flex items-center justify-between flex-shrink-0">
                <div x-show="selectedTemplate">
                    <div class="flex items-center gap-2">
                        <span :class="selectedTemplate?.jenis === 'SK' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700'"
                              class="text-xs font-bold px-2 py-0.5 rounded"
                              x-text="selectedTemplate ? selectedTemplate.jenis + ' v' + selectedTemplate.versi : ''"></span>
                        <h2 class="text-sm font-bold text-gray-800" x-text="selectedTemplate?.nama"></h2>
                        <span x-show="selectedTemplate?.is_aktif" class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">● Aktif</span>
                        <button @click="openEditModal()" class="ml-2 p-1 text-gray-400 hover:text-sky-600 transition-colors" title="Edit Info Template">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </div>
                </div>
                <div x-show="!selectedTemplate" class="text-sm text-gray-400">
                    Pilih template dari daftar kiri untuk mulai mengedit
                </div>
                <div class="flex items-center gap-2" x-show="selectedTemplate">
                    <span class="text-xs text-gray-400 font-medium bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                        Penyimpanan otomatis via ONLYOFFICE
                    </span>
                </div>
            </div>

            <div class="flex-1 bg-gray-50 overflow-hidden relative" x-show="selectedTemplate">
                <div id="editor-container" class="h-full bg-white relative z-10 w-full" wire:ignore></div>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center text-gray-400" x-show="!selectedTemplate">
                <p class="text-sm font-medium">Belum ada template dipilih</p>
                <p class="text-xs mt-1">Pilih template dari panel kiri</p>
            </div>
        </div>

        {{-- Modal Edit Template --}}
        <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="isEditModalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Edit Info Template</h3>
                    <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Template</label>
                        <input type="text" x-model="editForm.nama_template" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Surat</label>
                        <select x-model="editForm.tipe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                            <option value="SK">SK (Surat Keputusan)</option>
                            <option value="ST">ST (Surat Tugas)</option>
                            <option value="Arahan">Arahan</option>
                            <option value="SE">SE (Surat Edaran)</option>
                            <option value="Instruksi">Instruksi</option>
                            <option value="SOP">SOP</option>
                            <option value="Korespondensi">Korespondensi (Umum)</option>
                            <option value="Surat Dinas">Surat Dinas</option>
                            <option value="Nota Dinas">Nota Dinas</option>
                            <option value="Surat Undangan">Surat Undangan</option>
                            <option value="Khusus">Khusus</option>
                            <option value="MoU">MoU</option>
                            <option value="Perjanjian">Perjanjian</option>
                            <option value="Surat Kuasa">Surat Kuasa</option>
                            <option value="Berita Acara">Berita Acara</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-2">
                    <button @click="isEditModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                    <button @click="simpanEditTemplate()" :disabled="isSaving" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 disabled:opacity-50 transition-colors flex items-center gap-2">
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function templateManajemen(initialTemplates) {
            return {
                templates: initialTemplates,
                filterJenis: 'semua',
                selectedTemplate: null,
                isEditModalOpen: false,
                isSaving: false,
                editForm: {
                    nama_template: '',
                    tipe: ''
                },

                init() {
                    if (this.templates && this.templates.length > 0) {
                        this.selectTemplate(this.templates[0]);
                    }
                },

                selectTemplate(tmpl) {
                    this.selectedTemplate = tmpl;
                    
                    this.$nextTick(() => {
                        if (window.docEditor) {
                            window.docEditor.destroyEditor();
                        }

                        const onlyOfficeUrl = "{{ env('ONLYOFFICE_URL', 'http://localhost:8080') }}"; 
                        
                        if (typeof DocsAPI === 'undefined') {
                            let script = document.createElement('script');
                            script.src = onlyOfficeUrl + "/web-apps/apps/api/documents/api.js";
                            script.onload = () => this.initDocumentEditor(tmpl, onlyOfficeUrl);
                            script.onerror = () => alert("Gagal memuat Document Server. Pastikan Docker ONLYOFFICE sedang berjalan di port 8080.");
                            document.head.appendChild(script);
                        } else {
                            this.initDocumentEditor(tmpl, onlyOfficeUrl);
                        }
                    });
                },

                initDocumentEditor(template, serverUrl) {
                    window.docEditor = new DocsAPI.DocEditor("editor-container", template.onlyoffice_config);
                },

                saveTemplate() {
                    alert('Proses penyimpanan via ONLYOFFICE akan diproses secara asynchronous (Callback API). Pastikan container docker berjalan.');
                },

                openEditModal() {
                    if (!this.selectedTemplate) return;
                    this.editForm.nama_template = this.selectedTemplate.nama;
                    this.editForm.tipe = this.selectedTemplate.jenis;
                    this.isEditModalOpen = true;
                },

                async simpanEditTemplate() {
                    if (!this.selectedTemplate || !this.editForm.nama_template || !this.editForm.tipe) {
                        alert('Pastikan semua form terisi');
                        return;
                    }
                    
                    this.isSaving = true;
                    try {
                        const res = await fetch('/setup/template-surat/' + this.selectedTemplate.id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.editForm)
                        });
                        
                        const result = await res.json();
                        
                        if (res.ok && result.success) {
                            // Update lokal state UI
                            this.selectedTemplate.nama = result.data.nama;
                            this.selectedTemplate.jenis = result.data.jenis;
                            
                            // Update item di array templates
                            const index = this.templates.findIndex(t => t.id === this.selectedTemplate.id);
                            if (index !== -1) {
                                this.templates[index].nama = result.data.nama;
                                this.templates[index].jenis = result.data.jenis;
                            }
                            
                            this.isEditModalOpen = false;
                        } else {
                            alert(result.message || 'Gagal menyimpan perubahan');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.isSaving = false;
                    }
                }
            };
        }
    </script>
@endsection
