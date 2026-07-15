<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import { showAlert, showSuccess, showError } from '@/lib/alert';

const pencairans = ref<any[]>([]);
const programs = ref<any[]>([]);
const loading = ref(true);
const submitProcessing = ref(false);

const limit = ref(10);
const page = ref(1);
const pagination = ref<any>({});

const showModal = ref(false);
const showPreview = ref(false);
const activePencairan = ref<any>(null);

const openPreviewModal = (p: any) => {
    activePencairan.value = p;
    showPreview.value = true;
};
const form = ref({
    id_program: '',
    jumlah_dana: 0,
    keterangan: ''
});

// Selection helper details
const selectedProgram = ref<any>(null);
const availableFunds = ref(0);

const fetchPencairans = async () => {
    try {
        const response = await axios.get(`/api/nazhir/pencairan?limit=${limit.value}&page=${page.value}`);

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
    }
};

const fetchPrograms = async () => {
    try {
        // Fetch all programs for select box
        const response = await axios.get('/api/nazhir/program?all=true');

        if (response.data.success) {
            programs.value = response.data.data;
        }
    } catch (e) {
        console.error(e);
    }
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

onMounted(async () => {
    loading.value = true;
    await Promise.all([fetchPencairans(), fetchPrograms()]);
    loading.value = false;
});

// Watch limit
watch(limit, () => {
    page.value = 1;
    fetchPencairans();
});

const onProgramSelect = async () => {
    selectedProgram.value = null;
    availableFunds.value = 0;
    
    if (!form.value.id_program) {
        return;
    }
    
    const prog = programs.value.find(p => p.id_program === parseInt(form.value.id_program));

    if (prog) {
        selectedProgram.value = prog;
        
        // Calculate already approved funds for this program via database API
        try {
            const res = await axios.get(`/api/nazhir/pencairan/available-funds?id_program=${prog.id_program}`);
            if (res.data && res.data.success) {
                availableFunds.value = res.data.available_funds;
            } else {
                availableFunds.value = parseFloat(prog.dana_terkumpul);
            }
        } catch {
            availableFunds.value = parseFloat(prog.dana_terkumpul);
        }
    }
};

const openRequestModal = () => {
    form.value = {
        id_program: '',
        jumlah_dana: 0,
        keterangan: ''
    };
    selectedProgram.value = null;
    availableFunds.value = 0;
    showModal.value = true;
};

const submitRequest = async () => {
    if (!form.value.id_program) {
        await showAlert('Silakan pilih program wakaf.', 'Validasi');

        return;
    }

    if (form.value.jumlah_dana <= 0) {
        await showAlert('Jumlah dana harus lebih besar dari 0.', 'Validasi');

        return;
    }

    if (form.value.jumlah_dana > availableFunds.value) {
        await showAlert('Jumlah dana melebihi sisa dana terkumpul yang tersedia.', 'Validasi');

        return;
    }

    submitProcessing.value = true;

    try {
        const response = await axios.post('/api/nazhir/pencairan', form.value);

        if (response.data.success) {
            await showSuccess(response.data.message);
            showModal.value = false;
            fetchPencairans();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal mengajukan pencairan dana.');
    } finally {
        submitProcessing.value = false;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const canDisburse = (prog: any) => {
    if (parseInt(prog.status) !== 1) {
return false;
} // Must be active

    const minTarget = parseFloat(prog.target_dana) * 0.1;

    return parseFloat(prog.dana_terkumpul) >= minTarget;
};
</script>

<template>
    <Head title="Pencairan Dana - Nazhir" />

    <NazhirLayout>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#143E2C]">Pencairan Dana</h1>
                    <p class="text-xs text-gray-500 mt-1">
                        Ajukan pencairan dana dari program wakaf Anda untuk keperluan penyaluran.
                    </p>
                </div>
                <div class="flex items-center gap-4 self-stretch sm:self-auto justify-between sm:justify-start">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Limit:</span>
                        <select v-model="limit" class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer shadow-xs">
                            <option :value="10">10 Data</option>
                            <option :value="25">25 Data</option>
                            <option :value="50">50 Data</option>
                        </select>
                    </div>
                    <button @click="openRequestModal" class="px-4 py-2.5 bg-[#143E2C] hover:bg-[#1e5842] text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Ajukan Pencairan Baru
                    </button>
                </div>
            </div>

            <!-- Request History Table -->
            <div class="bg-white border border-gray-200 rounded-[20px] overflow-hidden shadow-sm">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#143E2C]"></div>
                </div>

                <div v-else-if="pencairans.length === 0" class="p-8 text-center text-gray-500 text-xs font-semibold">
                    Belum ada riwayat pengajuan pencairan dana.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[10px] font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-6 py-4">Program Wakaf</th>
                                <th class="px-6 py-4">Jumlah Dana</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4">Tanggal Pengajuan</th>
                                <th class="px-6 py-4">Tanggal Persetujuan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr v-for="p in pencairans" :key="p.id_pencairan" class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-800 max-w-xs truncate">{{ p.nama_program }}</td>
                                <td class="px-6 py-4 text-emerald-800 font-bold">{{ formatCurrency(p.jumlah_dana) }}</td>
                                <td class="px-6 py-4 text-gray-550 max-w-xs truncate" :title="p.keterangan">{{ p.keterangan || '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ p.created_at }}</td>
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
                                        <!-- Show surat link if approved and surat exists -->
                                        <a v-if="p.status_pencairan === 1 && p.surat_approval_url" :href="p.surat_approval_url" target="_blank"
                                            class="px-2.5 py-1.5 bg-green-50 hover:bg-green-600 text-green-700 hover:text-white border border-green-200 hover:border-green-600 font-bold rounded-lg text-[10px] transition-all whitespace-nowrap">
                                            ↓ Lihat Surat
                                        </a>
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

            <!-- Modal Ajukan Pencairan -->
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 backdrop-blur-xs p-4">
                <div class="bg-white border border-gray-200 rounded-[24px] max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
                    <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    
                    <h3 class="text-lg font-bold text-[#143E2C] mb-6">Ajukan Pencairan Dana</h3>
                    
                    <form @submit.prevent="submitRequest" class="space-y-4">
                        <!-- Select Program -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Pilih Program Wakaf</label>
                            <select v-model="form.id_program" @change="onProgramSelect" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                                <option value="">Pilih Program</option>
                                <option v-for="prog in programs" :key="prog.id_program" :value="prog.id_program">
                                    {{ prog.nama_program }}
                                </option>
                            </select>
                        </div>

                        <!-- Rule Validation Messages -->
                        <div v-if="selectedProgram">
                            <div v-if="!canDisburse(selectedProgram)" class="p-3 text-xs bg-red-50 border border-red-200 text-red-700 rounded-xl">
                                <span class="font-bold">Peringatan:</span> Program ini tidak memenuhi syarat untuk pencairan dana. Status program harus <span class="font-bold">Aktif/Disetujui</span> dan dana terkumpul minimal <span class="font-bold">10%</span> dari target.
                            </div>
                            
                            <div class="mt-3 bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Status Program:</span>
                                    <span class="font-bold uppercase" :class="parseInt(selectedProgram.status) === 1 ? 'text-green-700' : 'text-gray-650'">
                                        {{ parseInt(selectedProgram.status) === 1 ? 'Aktif/Disetujui' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Target Dana:</span>
                                    <span class="font-bold text-gray-800">{{ formatCurrency(selectedProgram.target_dana) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Dana Terkumpul:</span>
                                    <span class="font-bold text-gray-800">{{ formatCurrency(selectedProgram.dana_terkumpul) }} ({{ selectedProgram.progress }}%)</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-2 font-semibold">
                                    <span class="text-[#143E2C]">Sisa Dana Tersedia:</span>
                                    <span class="text-emerald-800 font-extrabold">{{ formatCurrency(availableFunds) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Input -->
                        <div v-if="selectedProgram && canDisburse(selectedProgram)">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Jumlah Dana yang Diajukan (Rupiah)</label>
                            <input type="number" v-model="form.jumlah_dana" required min="1" :max="availableFunds"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]" />
                        </div>

                        <!-- Usage Description -->
                        <div v-if="selectedProgram && canDisburse(selectedProgram)">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Rencana Penggunaan Dana</label>
                            <textarea v-model="form.keterangan" rows="3" required placeholder="Cth: Pengeboran air sumur tahap 1..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]"></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700">
                                Batal
                            </button>
                            <button v-if="selectedProgram && canDisburse(selectedProgram)" type="submit" :disabled="submitProcessing"
                                class="px-5 py-2.5 bg-[#143E2C] hover:bg-[#1f4e3c] disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all shadow-md">
                                {{ submitProcessing ? 'Memproses...' : 'Kirim Pengajuan' }}
                            </button>
                        </div>
                    </form>
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
                                    <span class="text-gray-400 font-bold uppercase text-[9px]">Jumlah Dana</span>
                                    <span class="font-extrabold text-[#143E2C] text-sm">{{ formatCurrency(activePencairan.jumlah_dana) }}</span>
                                </div>
                                <div class="flex flex-col">
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
                        </div>

                        <div class="flex flex-col gap-1.5 bg-gray-55 border border-gray-200 rounded-xl p-4">
                            <span class="text-gray-400 font-bold uppercase text-[10px]">Rencana Penggunaan Dana</span>
                            <p class="text-gray-700 whitespace-pre-wrap break-words leading-relaxed text-justify">{{ activePencairan.keterangan || '-' }}</p>
                        </div>

                        <!-- Approved Letter Display in Nazhir view -->
                        <div v-if="activePencairan.status_pencairan === 1" class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <span class="text-gray-400 font-bold uppercase text-[10px] block mb-2">Surat Persetujuan Resmi</span>
                            <div v-if="activePencairan.surat_approval_url" class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-50 text-green-700 border border-green-200">✓ Tersedia</span>
                                <a :href="activePencairan.surat_approval_url" target="_blank"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    Unduh Surat Persetujuan (PDF) →
                                </a>
                            </div>
                            <div v-else>
                                <span class="text-gray-400 font-italic">Belum ada file surat persetujuan yang diunggah.</span>
                            </div>
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
    </NazhirLayout>
</template>
