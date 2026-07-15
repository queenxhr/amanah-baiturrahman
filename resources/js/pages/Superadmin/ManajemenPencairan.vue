<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import SuperadminLayout from '@/layouts/SuperadminLayout.vue';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

const pencairans = ref<any[]>([]);
const status = ref('');
const limit = ref(10);
const page = ref(1);
const pagination = ref<any>({});
const loading = ref(true);
const processingId = ref<number | null>(null);

const showPreview = ref(false);
const activePencairan = ref<any>(null);

// Upload surat state
const showUploadModal = ref(false);
const uploadPencairan = ref<any>(null);
const suratFile = ref<File | null>(null);
const suratFileName = ref('');
const uploadProcessing = ref(false);
const suratFileError = ref('');

const openPreviewModal = (p: any) => {
    activePencairan.value = p;
    showPreview.value = true;
};

const openUploadModal = (p: any) => {
    uploadPencairan.value = p;
    suratFile.value = null;
    suratFileName.value = '';
    suratFileError.value = '';
    showUploadModal.value = true;
};

const fetchPencairans = async () => {
    loading.value = true;

    try {
        const params: any = {
            limit: limit.value,
            page: page.value
        };

        if (status.value !== '') {
            params.status = status.value;
        }

        const response = await axios.get('/api/superadmin/pencairan', { params });

        if (response.data.success) {
            pencairans.value = response.data.data.data || [];
            pagination.value = {
                current_page: response.data.data.current_page,
                last_page: response.data.data.last_page,
                total: response.data.data.total,
                from: response.data.data.from,
                to: response.data.data.to
            };
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const onFilterChange = () => {
    page.value = 1;
    fetchPencairans();
};

const handlePrevPage = () => {
    if (page.value > 1) {
        page.value--;
        fetchPencairans();
    }
};

const handleNextPage = () => {
    if (page.value < pagination.value.last_page) {
        page.value++;
        fetchPencairans();
    }
};

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('status')) {
        status.value = urlParams.get('status') || '';
    }

    fetchPencairans();
});

const downloadSurat = (id: number) => {
    window.open(`/api/superadmin/pencairan/${id}/download-surat`, '_blank');
};

const handleSuratFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        if (file.size > 10 * 1024 * 1024) {
            suratFileError.value = 'Ukuran file maksimal 10MB.';
            suratFile.value = null;
            suratFileName.value = '';
            return;
        }
        suratFile.value = file;
        suratFileName.value = file.name;
        suratFileError.value = '';
    }
};

const submitUploadSurat = async () => {
    if (!suratFile.value) {
        suratFileError.value = 'Pilih file surat terlebih dahulu.';
        return;
    }

    uploadProcessing.value = true;
    try {
        const formData = new FormData();
        formData.append('surat_approval', suratFile.value);

        const response = await axios.post(
            `/api/superadmin/pencairan/${uploadPencairan.value.id_pencairan}/upload-surat`,
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        if (response.data.success) {
            await showSuccess('Surat persetujuan berhasil diupload. Sekarang Anda dapat menyetujui pengajuan ini.');
            showUploadModal.value = false;
            fetchPencairans();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal mengupload surat.');
    } finally {
        uploadProcessing.value = false;
    }
};

const approvePencairan = async (p: any) => {
    if (!p.surat_approval) {
        await showError('Anda harus mengupload surat persetujuan terlebih dahulu sebelum menyetujui pengajuan ini.');
        return;
    }

    if (!(await showConfirm('Apakah Anda yakin ingin menyetujui pengajuan pencairan dana ini? Pastikan surat persetujuan sudah ditandatangani.'))) {
return;
}

    processingId.value = p.id_pencairan;

    try {
        const response = await axios.put(`/api/superadmin/pencairan/${p.id_pencairan}/approve`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchPencairans();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menyetujui pengajuan.');
    } finally {
        processingId.value = null;
    }
};

const rejectPencairan = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menolak pengajuan pencairan dana ini?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/pencairan/${id}/reject`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchPencairans();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menolak pengajuan.');
    } finally {
        processingId.value = null;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

</script>

<template>
    <Head title="Verifikasi Pencairan - Superadmin" />

    <SuperadminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Verifikasi Pencairan Dana</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Tinjau pengajuan pencairan dana program wakaf yang diajukan oleh Nazhir. Download format surat, tanda tangani, upload, lalu setujui.
                </p>
            </div>

            <!-- Workflow Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs text-blue-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <p class="font-bold mb-1">Alur Persetujuan Pencairan Dana:</p>
                        <ol class="list-decimal list-inside space-y-0.5 text-blue-700">
                            <li>Klik <strong>Download Surat</strong> untuk mendapatkan format surat permohonan pencairan dalam PDF</li>
                            <li>Cetak dan tanda tangani surat oleh Ketua Wadiah & Bendahara</li>
                            <li>Scan/foto surat yang sudah ditandatangani, lalu klik <strong>Upload Surat</strong></li>
                            <li>Setelah upload, klik <strong>Setujui</strong> untuk mengkonfirmasi pencairan</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-150 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-4 justify-between shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4 items-center w-full sm:w-auto">
                    <div class="w-full sm:w-64">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Pengajuan</label>
                        <select v-model="status" @change="onFilterChange"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                            <option value="">Semua Status</option>
                            <option value="0">Menunggu Persetujuan (Pending)</option>
                            <option value="1">Disetujui (Approved)</option>
                            <option value="2">Ditolak (Rejected)</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-40">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Limit</label>
                        <select v-model="limit" @change="onFilterChange"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                            <option :value="10">10 Data</option>
                            <option :value="25">25 Data</option>
                            <option :value="50">50 Data</option>
                        </select>
                    </div>
                </div>
                <button @click="onFilterChange" class="px-4 py-2.5 bg-[#143E2C] hover:bg-[#1e5842] text-white rounded-xl text-xs font-bold transition-all w-full sm:w-auto self-end">
                    Refresh Data
                </button>
            </div>

            <!-- Pencairan Table -->
            <div class="bg-white border border-gray-150 rounded-2xl overflow-hidden shadow-sm">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#143E2C]"></div>
                </div>

                <div v-else-if="pencairans.length === 0" class="p-8 text-center text-gray-400 text-xs font-semibold">
                    Tidak ada pengajuan pencairan dana ditemukan.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Program Wakaf</th>
                                <th class="px-6 py-4">Nazhir Pengaju</th>
                                <th class="px-6 py-4">Jumlah Dana</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Surat</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr v-for="p in pencairans" :key="p.id_pencairan" class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900 max-w-[200px] truncate" :title="p.nama_program">{{ p.nama_program }}</td>
                                <td class="px-6 py-4 text-gray-550">{{ p.nama_nazhir }}</td>
                                <td class="px-6 py-4 text-[#143E2C] font-bold whitespace-nowrap">{{ formatCurrency(p.jumlah_dana) }}</td>
                                <td class="px-6 py-4 text-gray-400 whitespace-nowrap">{{ p.created_at?.slice(0, 10) }}</td>
                                <td class="px-6 py-4">
                                    <!-- Surat status chip -->
                                    <div class="flex flex-col gap-1.5">
                                        <span v-if="p.surat_approval" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-50 text-green-700 border border-green-200">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                            Terupload
                                        </span>
                                        <span v-else-if="p.status_pencairan === 0" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                            Belum Upload
                                        </span>
                                        <a v-if="p.surat_approval_url" :href="p.surat_approval_url" target="_blank"
                                            class="text-[9px] font-bold text-blue-600 hover:underline truncate max-w-[100px]">
                                            Lihat Surat →
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border',
                                        p.status_pencairan === 1 ? 'bg-green-50 text-green-700 border-green-200' : 
                                        p.status_pencairan === 0 ? 'bg-amber-50 text-amber-700 border-amber-200 animate-pulse' : 
                                        'bg-red-50 text-red-700 border-red-200'
                                    ]">
                                        {{ 
                                            p.status_pencairan === 1 ? 'Disetujui' : 
                                            p.status_pencairan === 0 ? 'Pending' : 
                                            'Ditolak' 
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <button @click="openPreviewModal(p)"
                                            class="px-2 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 font-bold rounded-lg text-[10px] transition-all">
                                            Detail
                                        </button>
                                        <!-- Download surat (all pending) -->
                                        <button v-if="p.status_pencairan === 0" @click="downloadSurat(p.id_pencairan)"
                                            class="px-2 py-1.5 bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white border border-purple-200 hover:border-purple-600 font-bold rounded-lg text-[10px] transition-all whitespace-nowrap">
                                            ↓ Surat
                                        </button>
                                        <!-- Upload surat (pending only) -->
                                        <button v-if="p.status_pencairan === 0" @click="openUploadModal(p)"
                                            :class="[
                                                'px-2 py-1.5 font-bold rounded-lg text-[10px] transition-all whitespace-nowrap border',
                                                p.surat_approval
                                                    ? 'bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border-emerald-200 hover:border-emerald-600'
                                                    : 'bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white border-orange-200 hover:border-orange-600'
                                            ]">
                                            ↑ Upload
                                        </button>
                                        <span v-if="p.status_pencairan === 0" class="inline-flex gap-1.5">
                                            <!-- Approve (requires surat uploaded) -->
                                            <button @click="approvePencairan(p)"
                                                :disabled="processingId !== null"
                                                :title="!p.surat_approval ? 'Upload surat terlebih dahulu' : ''"
                                                :class="[
                                                    'px-2 py-1.5 font-bold rounded-lg text-[10px] transition-all disabled:opacity-50 whitespace-nowrap',
                                                    p.surat_approval
                                                        ? 'bg-[#143E2C] hover:bg-[#1e5842] text-white'
                                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                                                ]">
                                                ✓ Setujui
                                            </button>
                                            <!-- Reject -->
                                            <button @click="rejectPencairan(p.id_pencairan)"
                                                :disabled="processingId !== null"
                                                class="px-2 py-1.5 bg-red-50 hover:bg-red-600 text-red-650 hover:text-white border border-red-200 hover:border-red-600 disabled:opacity-50 font-bold rounded-lg text-[10px] transition-all">
                                                Tolak
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar -->
                <div class="bg-gray-50 border-t border-gray-150 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-gray-500">
                    <span>
                        Menampilkan {{ pagination.from || 0 }} - {{ pagination.to || 0 }} dari {{ pagination.total || 0 }} data
                    </span>
                    
                    <div class="flex items-center gap-2">
                        <button 
                            @click="handlePrevPage" 
                            :disabled="page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-250 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white font-black"
                        >
                            &larr;
                        </button>
                        <span class="px-2">Halaman {{ page }} dari {{ pagination.last_page || 1 }}</span>
                        <button 
                            @click="handleNextPage" 
                            :disabled="page >= pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-250 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white font-black"
                        >
                            &rarr;
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Modal Pratinjau Pencairan -->
            <div v-if="showPreview && activePencairan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 backdrop-blur-xs p-4">
                <div class="bg-white border border-gray-200 rounded-[24px] max-w-lg w-full p-6 sm:p-8 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
                    <button @click="showPreview = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    
                    <h3 class="text-lg font-bold text-[#143E2C] mb-6">Detail Pengajuan Pencairan</h3>
                    
                    <div class="space-y-4 text-xs">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Program Wakaf</span>
                                <span class="font-bold text-gray-800 text-sm whitespace-normal">{{ activePencairan.nama_program }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 border-t border-gray-150 pt-2.5 mt-2">
                                <div class="flex flex-col">
                                    <span class="text-gray-400 font-bold uppercase text-[9px]">Nazhir Pengaju</span>
                                    <span class="font-bold text-gray-800">{{ activePencairan.nama_nazhir }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-gray-400 font-bold uppercase text-[9px]">Jumlah Dana</span>
                                    <span class="font-extrabold text-[#143E2C] text-sm">{{ formatCurrency(activePencairan.jumlah_dana) }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 border-t border-gray-150 pt-2.5 mt-2">
                                <div class="flex flex-col">
                                    <span class="text-gray-400 font-bold uppercase text-[9px]">Tanggal Pengajuan</span>
                                    <span class="font-semibold text-gray-700">{{ activePencairan.created_at }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-gray-400 font-bold uppercase text-[9px]">Tanggal Persetujuan</span>
                                    <span class="font-semibold text-gray-700">{{ activePencairan.status_pencairan !== 0 && activePencairan.updated_at ? activePencairan.updated_at : '-' }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col border-t border-gray-150 pt-2.5">
                                <span class="text-gray-400 font-bold uppercase text-[9px]">Status</span>
                                <div>
                                    <span :class="[
                                        'inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border mt-0.5',
                                        activePencairan.status_pencairan === 1 ? 'bg-green-50 text-green-700 border-green-200' : 
                                        activePencairan.status_pencairan === 0 ? 'bg-amber-50 text-amber-700 border-amber-200 animate-pulse' : 
                                        'bg-red-50 text-red-700 border-red-200'
                                    ]">
                                        {{ 
                                            activePencairan.status_pencairan === 1 ? 'Disetujui' : 
                                            activePencairan.status_pencairan === 0 ? 'Pending' : 
                                            'Ditolak' 
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5 bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Rencana Penggunaan Dana</span>
                            <p class="text-gray-700 whitespace-pre-wrap break-words leading-relaxed text-justify">{{ activePencairan.keterangan || '-' }}</p>
                        </div>

                        <!-- Surat Status in Preview -->
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <span class="text-gray-400 font-bold uppercase text-[10px] block mb-2">Surat Persetujuan</span>
                            <div v-if="activePencairan.surat_approval_url" class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-50 text-green-700 border border-green-200">✓ Sudah Diupload</span>
                                <a :href="activePencairan.surat_approval_url" target="_blank"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    Lihat / Download Surat →
                                </a>
                            </div>
                            <div v-else>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-200">Belum Diupload</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 gap-3 flex-wrap">
                            <div class="flex gap-2 flex-wrap">
                                <button v-if="activePencairan.status_pencairan === 0" @click="downloadSurat(activePencairan.id_pencairan)" type="button"
                                    class="px-4 py-2 bg-purple-50 hover:bg-purple-700 text-purple-700 hover:text-white border border-purple-300 text-xs font-bold rounded-xl transition-all">
                                    ↓ Download Surat
                                </button>
                                <button v-if="activePencairan.status_pencairan === 0" @click="showPreview = false; openUploadModal(activePencairan)" type="button"
                                    class="px-4 py-2 bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white border border-orange-300 text-xs font-bold rounded-xl transition-all">
                                    ↑ Upload Surat
                                </button>
                            </div>
                            <button type="button" @click="showPreview = false" class="px-5 py-2.5 bg-[#143E2C] hover:bg-[#1f4e3c] text-white text-xs font-bold rounded-xl transition-all shadow-md">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Upload Surat -->
            <div v-if="showUploadModal && uploadPencairan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 backdrop-blur-xs p-4">
                <div class="bg-white border border-gray-200 rounded-[24px] max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
                    <button @click="showUploadModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    
                    <h3 class="text-lg font-bold text-[#143E2C] mb-1">Upload Surat Persetujuan</h3>
                    <p class="text-xs text-gray-500 mb-6">Upload surat yang telah ditandatangani oleh Ketua Wadiah & Bendahara untuk <strong>{{ uploadPencairan.nama_program }}</strong>.</p>
                    
                    <div class="space-y-4 text-xs">
                        <!-- Download reminder -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div>
                                <p class="text-blue-700 text-[11px]">Belum download format surat?</p>
                                <button @click="downloadSurat(uploadPencairan.id_pencairan)" type="button"
                                    class="text-blue-700 font-bold hover:underline text-[11px]">
                                    Klik di sini untuk download ↓
                                </button>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">File Surat (PDF/JPG/PNG, maks 10MB)</label>
                            <label class="block border-2 border-dashed rounded-xl p-5 text-center cursor-pointer transition-colors hover:border-[#143E2C] bg-gray-50"
                                :class="suratFileName ? 'border-[#143E2C] bg-green-50/30' : 'border-gray-300'">
                                <input type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png" @change="handleSuratFileChange" />
                                <div v-if="!suratFileName">
                                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    <p class="text-gray-500 text-[11px]">Klik atau drag file surat ke sini</p>
                                    <p class="text-gray-400 text-[10px] mt-1">PDF, JPG, PNG</p>
                                </div>
                                <div v-else class="flex items-center justify-center gap-2 text-[11px] text-[#143E2C] font-bold">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    {{ suratFileName }}
                                </div>
                            </label>
                            <p v-if="suratFileError" class="text-red-500 text-[10px] mt-1.5 font-semibold">{{ suratFileError }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showUploadModal = false" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700">
                                Batal
                            </button>
                            <button @click="submitUploadSurat" :disabled="uploadProcessing || !suratFile"
                                class="px-5 py-2.5 bg-[#143E2C] hover:bg-[#1f4e3c] disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all shadow-md">
                                {{ uploadProcessing ? 'Mengupload...' : 'Upload Surat' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperadminLayout>
</template>
