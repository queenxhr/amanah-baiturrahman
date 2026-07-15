<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import SuperadminLayout from '@/layouts/SuperadminLayout.vue';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

const programs = ref<any[]>([]);
const search = ref('');
const status = ref('');
const limit = ref(10);
const page = ref(1);
const pagination = ref<any>({});
const loading = ref(true);
const processingId = ref<number | null>(null);

const selectedProgram = ref<any>(null);
const showPreviewModal = ref(false);

const openPreview = (program: any) => {
    selectedProgram.value = program;
    showPreviewModal.value = true;
};

const fetchPrograms = async () => {
    loading.value = true;

    try {
        const params: any = {
            limit: limit.value,
            page: page.value
        };

        if (search.value) {
            params.search = search.value;
        }

        if (status.value !== '') {
            params.status = status.value;
        }

        const response = await axios.get('/api/superadmin/programs', { params });

        if (response.data.success) {
            programs.value = response.data.data.data || [];
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
    fetchPrograms();
};

const handlePrevPage = () => {
    if (page.value > 1) {
        page.value--;
        fetchPrograms();
    }
};

const handleNextPage = () => {
    if (page.value < pagination.value.last_page) {
        page.value++;
        fetchPrograms();
    }
};

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('status')) {
        status.value = urlParams.get('status') || '';
    }

    fetchPrograms();
});

const approveProgram = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menyetujui program wakaf ini untuk tayang secara publik?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/programs/${id}/approve`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchPrograms();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menyetujui program.');
    } finally {
        processingId.value = null;
    }
};

const rejectProgram = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menolak program wakaf ini?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/programs/${id}/reject`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchPrograms();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menolak program.');
    } finally {
        processingId.value = null;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

</script>

<template>
    <Head title="Verifikasi Program - Superadmin" />

    <SuperadminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Verifikasi Konten Program</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Tinjau pengajuan program wakaf baru oleh Nazhir sebelum dirilis agar bisa diakses oleh publik.
                </p>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-150 rounded-2xl p-5 grid grid-cols-1 sm:grid-cols-4 gap-4 shadow-sm">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Cari Program</label>
                    <input type="text" v-model="search" @input="onFilterChange" placeholder="Nama program..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Verifikasi</label>
                    <select v-model="status" @change="onFilterChange"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                        <option value="">Semua Status</option>
                        <option value="2">Menunggu Verifikasi (Pending)</option>
                        <option value="1">Aktif/Disetujui</option>
                        <option value="3">Ditolak</option>
                        <option value="0">Selesai/Tercapai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Limit</label>
                    <select v-model="limit" @change="onFilterChange"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                        <option :value="10">10 Data</option>
                        <option :value="25">25 Data</option>
                        <option :value="50">50 Data</option>
                    </select>
                </div>
                <div class="flex items-end justify-end">
                    <button @click="onFilterChange" class="px-4 py-2.5 bg-[#143E2C] hover:bg-[#1e5842] text-white rounded-xl text-xs font-bold transition-all w-full sm:w-auto">
                        Refresh Data
                    </button>
                </div>
            </div>

            <!-- Program List Table -->
            <div class="bg-white border border-gray-150 rounded-2xl overflow-hidden shadow-sm">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#143E2C]"></div>
                </div>

                <div v-else-if="programs.length === 0" class="p-8 text-center text-gray-400 text-xs font-semibold">
                    Tidak ada program wakaf ditemukan.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Program</th>
                                <th class="px-6 py-4">Target Dana</th>
                                <th class="px-6 py-4">Dana Terkumpul</th>
                                <th class="px-6 py-4">Tenggat Waktu</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr v-for="prog in programs" :key="prog.id_program" class="hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img :src="prog.gambar_thumbnail || 'https://images.unsplash.com/photo-1541944743827-e04aa6427c33?auto=format&fit=crop&q=80&w=150'" 
                                            class="w-10 h-10 rounded-lg object-cover bg-gray-100" />
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-gray-900 truncate max-w-xs sm:max-w-md">{{ prog.nama_program }}</h4>
                                            <p class="text-[10px] text-gray-400 truncate max-w-xs sm:max-w-md mt-0.5">{{ prog.deskripsi }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-semibold">{{ formatCurrency(prog.target_dana) }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ formatCurrency(prog.dana_terkumpul) }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ prog.due_date ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border',
                                        prog.status_program === 1 ? 'bg-green-50 text-green-600 border-green-200' : 
                                        prog.status_program === 2 ? 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse' : 
                                        prog.status_program === 3 ? 'bg-red-50 text-red-600 border-red-200' :
                                        'bg-blue-50 text-blue-600 border-blue-200'
                                    ]">
                                        {{ 
                                            prog.status_program === 1 ? 'Aktif' : 
                                            prog.status_program === 2 ? 'Pending Review' : 
                                            prog.status_program === 3 ? 'Ditolak' : 
                                            'Selesai/Tercapai' 
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openPreview(prog)"
                                            class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 font-bold rounded-lg text-[10px] transition-all">
                                            Pratinjau
                                        </button>
                                        <span v-if="prog.status_program === 2" class="inline-flex gap-2">
                                            <!-- Approve Program -->
                                            <button @click="approveProgram(prog.id_program)"
                                                :disabled="processingId !== null"
                                                class="px-2.5 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-bold rounded-lg text-[10px] transition-all">
                                                Terbitkan
                                            </button>
                                            <!-- Reject Program -->
                                            <button @click="rejectProgram(prog.id_program)"
                                                 :disabled="processingId !== null"
                                                 class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-650 hover:text-white border border-red-200 hover:border-red-600 disabled:opacity-50 font-bold rounded-lg text-[10px] transition-all">
                                                 Tolak
                                             </button>
                                         </span>
                                         <span v-else class="text-[10px] text-gray-400 font-semibold italic flex items-center">Sudah Ditinjau</span>
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
        </div>
    </SuperadminLayout>

    <!-- Preview Modal -->
    <div v-if="showPreviewModal && selectedProgram" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300">
      <div class="bg-white rounded-[24px] border border-gray-150 shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col transform scale-100 transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
          <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-650 border border-blue-200">
              Pratinjau Program
            </span>
            <h3 class="text-sm font-bold text-gray-900 mt-1">Detail Program Wakaf</h3>
          </div>
          <button @click="showPreviewModal = false" class="p-1.5 rounded-xl hover:bg-gray-200 text-gray-455 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1">
          <!-- Thumbnail & Quick Stats -->
          <div class="flex flex-col md:flex-row gap-5">
            <img :src="selectedProgram.gambar_thumbnail || '/images/default_program.jpg'" 
              class="w-full md:w-44 h-44 rounded-2xl object-cover border border-gray-250 bg-gray-50 shadow-sm" />
            <div class="flex-1 space-y-4">
              <h2 class="text-base font-black text-gray-900 leading-snug">{{ selectedProgram.nama_program }}</h2>
              
              <!-- Target & Progress -->
              <div class="space-y-2">
                <div class="flex justify-between text-xs">
                  <span class="text-gray-400 font-bold">Progress Capaian</span>
                  <span class="text-[#143E2C] font-black">
                    {{ selectedProgram.progress || (selectedProgram.target_dana > 0 ? ((selectedProgram.dana_terkumpul / selectedProgram.target_dana) * 100).toFixed(2) : '0') }}%
                  </span>
                </div>
                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden border border-gray-150">
                  <div class="h-full bg-gradient-to-r from-[#1e5842] to-[#143E2C] rounded-full" 
                    :style="{ width: `${Math.min(selectedProgram.progress || (selectedProgram.target_dana > 0 ? (selectedProgram.dana_terkumpul / selectedProgram.target_dana) * 100 : 0), 100)}%` }"></div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                  <div>
                    <span class="block text-gray-400 font-semibold">Terkumpul</span>
                    <span class="font-black text-green-750 text-xs">{{ formatCurrency(selectedProgram.dana_terkumpul) }}</span>
                  </div>
                  <div>
                    <span class="block text-gray-400 font-semibold">Target Dana</span>
                    <span class="font-black text-gray-900 text-xs">{{ formatCurrency(selectedProgram.target_dana) }}</span>
                  </div>
                </div>
              </div>

              <!-- Other parameters -->
              <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-3 text-[11px]">
                <div>
                  <span class="block text-gray-400 font-semibold">Tenggat Waktu</span>
                  <span class="font-bold text-gray-800">
                    {{ selectedProgram.due_date ? new Date(selectedProgram.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-' }}
                  </span>
                </div>
                <div>
                  <span class="block text-gray-400 font-semibold">Status Program</span>
                  <span :class="[
                    'inline-block px-2 py-0.5 rounded-full text-[8px] font-black uppercase border mt-0.5',
                    selectedProgram.status_program === 1 ? 'bg-green-50 text-green-700 border-green-200' : 
                    selectedProgram.status_program === 2 ? 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse' : 
                    selectedProgram.status_program === 3 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                  ]">
                    {{ 
                      selectedProgram.status_program === 1 ? 'Aktif' : 
                      selectedProgram.status_program === 2 ? 'Pending' : 
                      selectedProgram.status_program === 3 ? 'Ditolak' : 'Selesai' 
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Description Section -->
          <div class="border-t border-gray-100 pt-5 space-y-2">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider">Deskripsi Program</h4>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-250 text-xs text-gray-700 font-semibold leading-relaxed overflow-y-auto max-h-[25vh] rich-text-content"
              v-html="selectedProgram.deskripsi || '<p class=\'text-gray-400 italic\'>Tidak ada deskripsi</p>'">
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2.5">
          <!-- Close button -->
          <button @click="showPreviewModal = false" class="px-4 py-2 border border-gray-250 text-gray-705 rounded-xl text-xs font-bold hover:bg-gray-100 transition">
            Tutup
          </button>
          
          <template v-if="selectedProgram.status_program === 2">
            <button @click="approveProgram(selectedProgram.id_program); showPreviewModal = false"
              :disabled="processingId !== null"
              class="px-4 py-2 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white rounded-xl text-xs font-bold transition">
              Terbitkan
            </button>
            <button @click="rejectProgram(selectedProgram.id_program); showPreviewModal = false"
              :disabled="processingId !== null"
              class="px-4 py-2 bg-red-50 hover:bg-red-650 disabled:opacity-50 text-red-600 hover:text-white border border-red-200 hover:border-red-600 rounded-xl text-xs font-bold transition">
              Tolak
            </button>
          </template>
        </div>

      </div>
    </div>
</template>
