<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SuperadminLayout from '@/layouts/SuperadminLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

const pencairans = ref<any[]>([]);
const status = ref('');
const loading = ref(true);
const processingId = ref<number | null>(null);

const showPreview = ref(false);
const activePencairan = ref<any>(null);

const openPreviewModal = (p: any) => {
    activePencairan.value = p;
    showPreview.value = true;
};

const fetchPencairans = async () => {
    loading.value = true;
    try {
        const params: any = {};
        if (status.value !== '') params.status = status.value;

        const response = await axios.get('/api/superadmin/pencairan', { params });
        if (response.data.success) {
            pencairans.value = response.data.data;
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        status.value = urlParams.get('status') || '';
    }
    fetchPencairans();
});

const approvePencairan = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menyetujui pengajuan pencairan dana ini?'))) return;
    processingId.value = id;
    try {
        const response = await axios.put(`/api/superadmin/pencairan/${id}/approve`);
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
    if (!(await showConfirm('Apakah Anda yakin ingin menolak pengajuan pencairan dana ini?'))) return;
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
                    Tinjau pengajuan pencairan dana program wakaf yang diajukan oleh Nazhir.
                </p>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-150 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="w-full sm:w-1/3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Pengajuan</label>
                    <select v-model="status" @change="fetchPencairans"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                        <option value="">Semua Status</option>
                        <option value="0">Menunggu Persetujuan (Pending)</option>
                        <option value="1">Disetujui (Approved)</option>
                        <option value="2">Ditolak (Rejected)</option>
                    </select>
                </div>
                <button @click="fetchPencairans" class="px-4 py-2.5 bg-[#143E2C] hover:bg-[#1e5842] text-white rounded-xl text-xs font-bold transition-all w-full sm:w-auto self-end">
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
                                <th class="px-6 py-4">Keterangan Penggunaan</th>
                                <th class="px-6 py-4">Tanggal Pengajuan</th>
                                <th class="px-6 py-4">Tanggal Persetujuan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr v-for="p in pencairans" :key="p.id_pencairan" class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900 max-w-xs truncate">{{ p.nama_program }}</td>
                                <td class="px-6 py-4 text-gray-550">{{ p.nama_nazhir }}</td>
                                <td class="px-6 py-4 text-[#143E2C] font-bold">{{ formatCurrency(p.jumlah_dana) }}</td>
                                <td class="px-6 py-4 text-gray-550 max-w-xs truncate" :title="p.keterangan">{{ p.keterangan || '-' }}</td>
                                <td class="px-6 py-4 text-gray-400">{{ p.created_at }}</td>
                                <td class="px-6 py-4 text-gray-550">
                                    {{ p.status_pencairan !== 0 && p.updated_at ? p.updated_at : '-' }}
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
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openPreviewModal(p)"
                                            class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 font-bold rounded-lg text-[10px] transition-all">
                                            Pratinjau
                                        </button>
                                        <span v-if="p.status_pencairan === 0" class="inline-flex gap-2">
                                            <!-- Approve -->
                                            <button @click="approvePencairan(p.id_pencairan)"
                                                :disabled="processingId !== null"
                                                class="px-2.5 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-bold rounded-lg text-[10px] transition-all">
                                                Setujui
                                            </button>
                                            <!-- Reject -->
                                            <button @click="rejectPencairan(p.id_pencairan)"
                                                :disabled="processingId !== null"
                                                class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-650 hover:text-white border border-red-200 hover:border-red-600 disabled:opacity-50 font-bold rounded-lg text-[10px] transition-all">
                                                Tolak
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                            <button type="button" @click="showPreview = false" class="px-5 py-2.5 bg-[#143E2C] hover:bg-[#1f4e3c] text-white text-xs font-bold rounded-xl transition-all shadow-md">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperadminLayout>
</template>
