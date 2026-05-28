<script setup lang="ts">
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const search = ref('');
const sortOrder = ref('asc');
const users = ref<any[]>([]);
const isLoading = ref(false);

const formatRupiah = (num: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
};

const fetchUsers = async () => {
    isLoading.value = true;
    try {
        let url = `/api/nazhir/users?sort=${sortOrder.value}`;
        if (search.value) {
            url += `&search=${encodeURIComponent(search.value)}`;
        }
        const res = await axios.get(url);
        if (res.data && res.data.data) {
            users.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch users:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchUsers();
});

// Watch sort order immediately
watch(sortOrder, () => {
    fetchUsers();
});

// Watch search with a slight debounce
let timeout: any = null;
watch(search, () => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        fetchUsers();
    }, 300);
});
</script>

<template>
  <Head title="Manajemen User" />

  <NazhirLayout>
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-black text-gray-900 leading-tight">Manajemen User (Wakif)</h1>
          <p class="text-xs text-gray-500">Kelola data donatur wakif terdaftar serta pantau detail profil dan akumulasi donasi mereka.</p>
        </div>
      </div>

      <!-- Filters & Search Bar -->
      <section class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row items-center gap-4 justify-between">
        <div class="relative w-full max-w-md">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="h-4.5 w-4.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            type="text" 
            v-model="search"
            placeholder="Cari user berdasarkan nama, email, atau no hp..."
            class="w-full bg-gray-50 border border-gray-250 rounded-xl pl-10 pr-4 py-2 text-xs font-semibold text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C] transition-all"
          />
        </div>

        <div class="flex items-center gap-2 self-stretch sm:self-auto">
          <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Urutkan Akun:</span>
          <select 
            v-model="sortOrder"
            class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
          >
            <option value="asc">Terlama Bergabung (Asc)</option>
            <option value="desc">Terbaru Bergabung (Desc)</option>
          </select>
        </div>
      </section>

      <!-- Users Table -->
      <section class="bg-white border border-gray-150 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                <th class="px-6 py-4">ID User</th>
                <th class="px-6 py-4">Nama Pengguna</th>
                <th class="px-6 py-4">Kontak</th>
                <th class="px-6 py-4">Detail Profil</th>
                <th class="px-6 py-4">Alamat</th>
                <th class="px-6 py-4">Tgl Bergabung</th>
                <th class="px-6 py-4 text-right">Jumlah Berwakaf</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
              <tr v-if="isLoading">
                <td colspan="7" class="text-center py-10 text-gray-400">
                  <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#143E2C] mx-auto"></div>
                </td>
              </tr>
              <tr v-else-if="users.length === 0">
                <td colspan="7" class="text-center py-10 text-gray-400">Tidak ada user ditemukan.</td>
              </tr>
              <tr v-for="u in users" :key="u.id_user" class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-mono text-gray-500 font-bold">#{{ u.id_user }}</td>
                <td class="px-6 py-4 text-gray-900 font-bold">{{ u.nama }}</td>
                <td class="px-6 py-4 font-normal text-gray-600">
                  <div class="font-bold text-gray-800">{{ u.email }}</div>
                  <div class="text-[10px] text-gray-400 mt-0.5">{{ u.no_hp || '-' }}</div>
                </td>
                <td class="px-6 py-4 font-normal text-gray-650">
                  <div>Tgl Lahir: <span class="font-bold text-gray-800">{{ u.tanggal_lahir || '-' }}</span></div>
                  <div class="mt-0.5">Gender: <span class="font-bold text-gray-800">{{ u.jenis_kelamin === 'L' ? 'Laki-laki' : (u.jenis_kelamin === 'P' ? 'Perempuan' : u.jenis_kelamin || '-') }}</span></div>
                </td>
                <td class="px-6 py-4 font-normal text-gray-500 max-w-[200px] truncate" :title="u.alamat">
                  {{ u.alamat || '-' }}
                </td>
                <td class="px-6 py-4 font-normal text-gray-500">
                  {{ u.created_at ? new Date(u.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                </td>
                <td class="px-6 py-4 text-right font-black text-gray-900">
                  {{ formatRupiah(u.total_wakaf) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

    </div>
  </NazhirLayout>
</template>
