<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

const search = ref('');
const sort = ref('desc'); // 'asc' or 'desc'
const limit = ref(10);
const page = ref(1);
const programs = ref<any[]>([]);
const pagination = ref<any>({});
const isLoading = ref(false);

const selectedProgram = ref<any>(null);
const showPreviewModal = ref(false);

const openPreview = (program: any) => {
    selectedProgram.value = program;
    showPreviewModal.value = true;
};

const formatRupiah = (num: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
};

const fetchPrograms = async () => {
    isLoading.value = true;

    try {
        let url = `/api/nazhir/program?limit=${limit.value}&page=${page.value}&sort=${sort.value}`;

        if (search.value) {
            url += `&search=${encodeURIComponent(search.value)}`;
        }

        const res = await axios.get(url);

        if (res.data && res.data.data) {
            programs.value = res.data.data.data || [];
            pagination.value = {
                current_page: res.data.data.current_page,
                last_page: res.data.data.last_page,
                total: res.data.data.total,
                from: res.data.data.from,
                to: res.data.data.to
            };
        }
    } catch (e) {
        console.error('Failed to fetch programs:', e);
    } finally {
        isLoading.value = false;
    }
};

const handleDeleteProgram = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menghapus program ini? Semua data transaksi dan laporan terkait juga akan terhapus secara permanen.'))) {
return;
}

    try {
        await axios.delete(`/api/nazhir/program/${id}`);
        await showSuccess('Program berhasil dihapus!');
        fetchPrograms();
    } catch (e) {
        console.error('Failed to delete program:', e);
        await showError('Gagal menghapus program.');
    }
};

const handleDeleteLaporan = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menghapus laporan penyaluran ini secara permanen?'))) {
return;
}

    try {
        await axios.delete(`/api/nazhir/laporan/${id}`);
        await showSuccess('Laporan berhasil dihapus!');
        fetchPrograms();
    } catch (e) {
        console.error('Failed to delete report:', e);
        await showError('Gagal menghapus laporan.');
    }
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
    fetchPrograms();
});

let timeout: any = null;
watch(search, () => {
    if (timeout) {
clearTimeout(timeout);
}

    timeout = setTimeout(() => {
        page.value = 1;
        fetchPrograms();
    }, 300);
});

watch(limit, () => {
    page.value = 1;
    fetchPrograms();
});

watch(sort, () => {
    page.value = 1;
    fetchPrograms();
});
</script>

<template>
  <Head title="Manajemen Program" />

  <NazhirLayout>
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-2xl font-black text-gray-900 leading-tight">Manajemen Program Wakaf</h1>
          <p class="text-xs text-gray-500">Kelola daftar program wakaf, monitoring capaian target, serta isi laporan penyaluran.</p>
        </div>
        <Link 
          href="/manajemen-program/tambah"
          class="px-5 py-2.5 bg-[#143E2C] hover:bg-[#1a4f38] text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs hover:shadow-md transition duration-200"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
          Tambah Program Baru
        </Link>
      </div>

      <!-- Filters & Search Bar -->
      <section class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row justify-between gap-4">
        <div class="relative w-full max-w-md">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="h-4.5 w-4.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            type="text" 
            v-model="search"
            placeholder="Cari program wakaf berdasarkan nama..."
            class="w-full bg-gray-50 border border-gray-250 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C] transition-all"
          />
        </div>

        <div class="flex items-center gap-2">
          <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-500">Urutkan:</span>
            <select 
              v-model="sort"
              class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-1.5 text-xs text-gray-750 font-bold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
            >
              <option value="desc">Z - A</option>
              <option value="asc">A - Z</option>
            </select>
          </div>
          <div class="flex items-center gap-2 ml-4">
            <span class="text-xs font-bold text-gray-500">Limit:</span>
            <select 
              v-model="limit"
              class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-1.5 text-xs text-gray-750 font-bold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
            >
              <option :value="10">10 Data</option>
              <option :value="25">25 Data</option>
              <option :value="50">50 Data</option>
            </select>
          </div>
        </div>
      </section>

      <!-- Programs Table -->
      <section class="bg-white border border-gray-150 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                <th class="px-6 py-4">ID</th>
                <th class="px-6 py-4">Program Wakaf</th>
                <th class="px-6 py-4">Target Dana</th>
                <th class="px-6 py-4">Dana Terkumpul</th>
                <th class="px-6 py-4">Progress (%)</th>
                <th class="px-6 py-4">Tenggat</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
              <tr v-if="isLoading">
                <td colspan="8" class="text-center py-10 text-gray-400">
                  <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#143E2C] mx-auto"></div>
                </td>
              </tr>
              <tr v-else-if="programs.length === 0">
                <td colspan="8" class="text-center py-10 text-gray-400">Tidak ada program wakaf ditemukan.</td>
              </tr>
              <tr v-for="p in programs" :key="p.id_program" class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-mono text-gray-500 font-bold">#{{ p.id_program }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img 
                      :src="p.gambar_thumbnail || '/images/default_program.jpg'" 
                      class="w-10 h-10 rounded-lg object-cover border border-gray-200"
                      alt="Thumbnail"
                    />
                    <span class="text-gray-900 font-bold" :title="p.nama_program">
                      {{ p.nama_program }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-950">{{ formatRupiah(p.target_dana) }}</td>
                <td class="px-6 py-4 text-green-700 font-bold">{{ formatRupiah(p.dana_terkumpul) }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-900">{{ p.progress }}%</span>
                    <div class="w-16 bg-gray-100 h-2 rounded-full overflow-hidden">
                      <div class="h-full bg-[#143E2C] rounded-full" :style="{ width: `${Math.min(p.progress, 100)}%` }"></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-550">
                  {{ p.due_date ? new Date(p.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span 
                    :class="[
                      'inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border',
                      p.status === 1 ? 'bg-green-50 text-green-700 border-green-200' : 
                      p.status === 2 ? 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse' : 
                      p.status === 3 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                    ]"
                  >
                    {{ 
                      p.status === 1 ? 'Aktif' : 
                      p.status === 2 ? 'Pending Review' : 
                      p.status === 3 ? 'Ditolak' : 'Selesai' 
                    }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end items-center gap-2">
                    
                    <!-- Pratinjau Program -->
                    <button 
                      @click="openPreview(p)"
                      class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-650 rounded-lg border border-blue-200 transition"
                      title="Pratinjau Program"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    
                    <!-- Laporan Button -->
                    <template v-if="p.t05_laporan_penyalurans && p.t05_laporan_penyalurans.length > 0">
                      <div class="flex items-center gap-1.5">
                        <Link 
                          :href="`/laporan/${p.t05_laporan_penyalurans[0].id_laporan}/edit`"
                          class="px-2.5 py-1 text-[10px] font-black text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-300 transition duration-150 uppercase animate-all"
                        >
                          Edit Laporan
                        </Link>
                        <button 
                          @click="handleDeleteLaporan(p.t05_laporan_penyalurans[0].id_laporan)"
                          class="px-2.5 py-1 text-[10px] font-black text-red-700 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition duration-150 uppercase cursor-pointer"
                          title="Hapus Laporan"
                        >
                          Hapus
                        </button>
                      </div>
                    </template>
                    <template v-else>
                      <Link 
                        :href="`/laporan/tambah/${p.id_program}`"
                        class="px-2.5 py-1 text-[10px] font-black text-blue-750 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-300 transition duration-150 uppercase"
                      >
                        Tulis Laporan
                      </Link>
                    </template>

                    <!-- Edit Program -->
                    <Link 
                      :href="`/program/${p.id_program}/edit`"
                      class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border border-gray-300 transition"
                      title="Edit Program"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </Link>

                    <!-- Delete Program -->
                    <button 
                      @click="handleDeleteProgram(p.id_program)"
                      class="p-1.5 bg-red-50 hover:bg-red-100 text-red-650 rounded-lg border border-red-200 transition"
                      title="Hapus Program"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>

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
      </section>

    </div>
  </NazhirLayout>

  <!-- Preview Modal -->
  <div v-if="showPreviewModal && selectedProgram" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300">
    <div class="bg-white rounded-[24px] border border-gray-150 shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col transform scale-100 transition-all duration-300">
      
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
        <div>
          <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-650 border border-blue-200">
            Pratinjau Program
          </span>
          <h3 class="text-sm font-bold text-gray-905 mt-1">Detail Program Wakaf</h3>
        </div>
        <button @click="showPreviewModal = false" class="p-1.5 rounded-xl hover:bg-gray-200 text-gray-400 hover:text-gray-700 transition">
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
            <h2 class="text-base font-black text-gray-905 leading-snug">{{ selectedProgram.nama_program }}</h2>
            
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
                  <span class="font-black text-green-750 text-xs">{{ formatRupiah(selectedProgram.dana_terkumpul) }}</span>
                </div>
                <div>
                  <span class="block text-gray-400 font-semibold">Target Dana</span>
                  <span class="font-black text-gray-900 text-xs">{{ formatRupiah(selectedProgram.target_dana) }}</span>
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
                  selectedProgram.status === 1 ? 'bg-green-50 text-green-700 border-green-200' : 
                  selectedProgram.status === 2 ? 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse' : 
                  selectedProgram.status === 3 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                ]">
                  {{ 
                    selectedProgram.status === 1 ? 'Aktif / Dibuka' : 
                    selectedProgram.status === 2 ? 'Menunggu Verifikasi' : 
                    selectedProgram.status === 3 ? 'Ditolak' : 'Ditutup / Selesai' 
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
      </div>

    </div>
  </div>
</template>

<style scoped>
select {
    cursor: pointer;
}
</style>
