<script setup lang="ts">
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const search = ref('');
const sort = ref('desc'); // 'asc' or 'desc'
const limit = ref(10);
const page = ref(1);
const programs = ref<any[]>([]);
const pagination = ref<any>({});
const isLoading = ref(false);

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
    if (!confirm('Apakah Anda yakin ingin menghapus program ini? Semua data transaksi dan laporan terkait juga akan terhapus secara permanen.')) return;
    try {
        await axios.delete(`/api/nazhir/program/${id}`);
        alert('Program berhasil dihapus!');
        fetchPrograms();
    } catch (e) {
        console.error('Failed to delete program:', e);
        alert('Gagal menghapus program.');
    }
};

const handleDeleteLaporan = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus laporan penyaluran ini secara permanen?')) return;
    try {
        await axios.delete(`/api/nazhir/laporan/${id}`);
        alert('Laporan berhasil dihapus!');
        fetchPrograms();
    } catch (e) {
        console.error('Failed to delete report:', e);
        alert('Gagal menghapus laporan.');
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
    if (timeout) clearTimeout(timeout);
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
                      'inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider',
                      p.status === 1 ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'
                    ]"
                  >
                    {{ p.status === 1 ? 'Dibuka' : 'Ditutup' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end items-center gap-2">
                    
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
</template>

<style scoped>
select {
    cursor: pointer;
}
</style>
