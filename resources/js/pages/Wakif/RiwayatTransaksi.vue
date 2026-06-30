<script setup lang="ts">
import WakifLayout from '@/layouts/WakifLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Transaction data state
const transactions = ref<any[]>([]);
const isLoading = ref(true);

// Filters & Pagination state
const searchQuery = ref('');
const statusFilter = ref(''); // '', '0', '1', '2'
const startDate = ref('');
const endDate = ref('');
const currentPage = ref(1);
const rowsLimit = ref(10); // default limit 10 rows

// Load token
const getHeaders = () => {
    const token = localStorage.getItem('wakif_auth_token');
    return token ? { Authorization: `Bearer ${token}` } : {};
};

// Fetch transaction history
const fetchHistory = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/wakif/transaksi/riwayat', {
            headers: getHeaders()
        });
        if (response.data && response.data.success) {
            transactions.value = response.data.data || [];
        }
    } catch (e) {
        console.error('Failed to load transaction history:', e);
    } finally {
        isLoading.value = false;
    }
};

// Format Currency
const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

// Format Date
const formatDate = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Filtered and searched transactions (Client-side)
const filteredTransactions = computed(() => {
    return transactions.value.filter((t: any) => {
        // Search filter (Invoice ID or Program Name)
        const matchSearch = searchQuery.value === '' || 
            (t.kode_referensi && t.kode_referensi.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            (t.t03_program_wakaf?.nama_program && t.t03_program_wakaf.nama_program.toLowerCase().includes(searchQuery.value.toLowerCase()));

        // Status filter
        const tStatusStr = String(t.status_pembayaran ?? 0);
        const matchStatus = statusFilter.value === '' || tStatusStr === statusFilter.value;

        // Date range filter
        let matchDate = true;
        if (t.created_at) {
            const txDate = new Date(t.created_at);
            // Reset hours for date comparison
            txDate.setHours(0,0,0,0);

            if (startDate.value) {
                const start = new Date(startDate.value);
                start.setHours(0,0,0,0);
                if (txDate < start) matchDate = false;
            }
            if (endDate.value) {
                const end = new Date(endDate.value);
                end.setHours(0,0,0,0);
                if (txDate > end) matchDate = false;
            }
        } else {
            if (startDate.value || endDate.value) matchDate = false;
        }

        return matchSearch && matchStatus && matchDate;
    });
});

// Paginated transactions
const paginatedTransactions = computed(() => {
    const startIdx = (currentPage.value - 1) * rowsLimit.value;
    const endIdx = startIdx + rowsLimit.value;
    return filteredTransactions.value.slice(startIdx, endIdx);
});

// Total pages
const totalPages = computed(() => {
    return Math.ceil(filteredTransactions.value.length / rowsLimit.value) || 1;
});

// Reset page on filter changes
const handleFilterChange = () => {
    currentPage.value = 1;
};

// Reset all filters
const resetFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    startDate.value = '';
    endDate.value = '';
    currentPage.value = 1;
};

onMounted(() => {
    fetchHistory();
});
</script>

<template>
    <Head title="Riwayat Transaksi Wakaf" />

    <WakifLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-12">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">Riwayat Transaksi</h1>
                    <p class="text-xs text-gray-500 font-semibold mt-1">Pantau dan kelola seluruh transaksi wakaf yang pernah Anda lakukan</p>
                </div>
                <button @click="resetFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 transition duration-200">
                    Atur Ulang Filter
                </button>
            </div>

            <!-- Filters Grid -->
            <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm mb-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search Field -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cari Transaksi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" v-model="searchQuery" @input="handleFilterChange" class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]" placeholder="ID / Nama Program...">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Status Pembayaran</label>
                        <select v-model="statusFilter" @change="handleFilterChange" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                            <option value="">Semua Status</option>
                            <option value="0">Menunggu Verifikasi (Pending)</option>
                            <option value="1">Berhasil (Success)</option>
                            <option value="2">Gagal (Failed)</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" v-model="startDate" @change="handleFilterChange" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" v-model="endDate" @change="handleFilterChange" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                    </div>
                </div>

                <!-- Limit and Count summary -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-2 border-t border-gray-100 gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-semibold text-gray-700">Tampilkan:</label>
                        <select v-model="rowsLimit" @change="handleFilterChange" class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none">
                            <option :value="5">5 baris</option>
                            <option :value="10">10 baris</option>
                            <option :value="20">20 baris</option>
                            <option :value="50">50 baris</option>
                        </select>
                    </div>
                    <span class="text-xs text-gray-400">
                        Menampilkan {{ filteredTransactions.length ? (currentPage - 1) * rowsLimit + 1 : 0 }} - {{ Math.min(currentPage * rowsLimit, filteredTransactions.length) }} dari {{ filteredTransactions.length }} transaksi
                    </span>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white border border-gray-150 rounded-2xl shadow-sm overflow-hidden">
                <div v-if="isLoading" class="flex justify-center items-center py-24">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#638734]"></div>
                </div>

                <div v-else-if="filteredTransactions.length === 0" class="text-center py-20">
                    <svg class="w-12 h-12 text-gray-350 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-sm font-bold text-gray-800">Tidak ada transaksi ditemukan</h3>
                    <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau rentang filter tanggal Anda.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="py-4 px-6">ID Transaksi</th>
                                <th class="py-4 px-6">Program Wakaf</th>
                                <th class="py-4 px-6">Tanggal</th>
                                <th class="py-4 px-6 text-right">Nominal</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150">
                            <tr v-for="t in paginatedTransactions" :key="t.id_transaksi" class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6">
                                    <span class="font-mono text-xs font-bold text-gray-700">
                                        {{ t.kode_referensi || ('WKF-' + String(t.id_transaksi).padStart(6, '0')) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs font-bold text-gray-900 block max-w-[280px] truncate">
                                        {{ t.t03_program_wakaf?.nama_program || 'Program Wakaf' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs text-gray-500">
                                        {{ formatDate(t.created_at) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-xs font-black text-gray-900">
                                        {{ formatRupiah(t.nominal) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span v-if="t.status_pembayaran === 1" class="inline-block px-3 py-1 rounded-full bg-green-50 text-[#1e5842] border border-green-200 text-[10px] font-black uppercase">
                                        Berhasil
                                    </span>
                                    <span v-else-if="t.status_pembayaran === 2" class="inline-block px-3 py-1 rounded-full bg-red-50 text-red-800 border border-red-200 text-[10px] font-black uppercase">
                                        Gagal
                                    </span>
                                    <span v-else class="inline-block px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 border border-yellow-200 text-[10px] font-black uppercase">
                                        Menunggu Verifikasi
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <Link :href="`/transaksi/${t.id_transaksi}`" class="inline-flex items-center justify-center px-4 py-1.5 bg-[#638734]/10 hover:bg-[#638734] text-[#1e5842] hover:text-white font-bold rounded-lg text-xs transition duration-200 gap-1.5 border border-transparent">
                                        Lihat Invoice
                                        <span>&rarr;</span>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Pagination Footer -->
                <div v-if="totalPages > 1" class="bg-white px-6 py-4 border-t border-gray-150 flex justify-between items-center">
                    <button :disabled="currentPage === 1" @click="currentPage--" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 disabled:opacity-50 transition">
                        Sebelumnya
                    </button>
                    <div class="flex items-center gap-1.5">
                        <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="{'bg-[#638734] text-white border-transparent': currentPage === page, 'border-gray-300 text-gray-600 hover:bg-gray-50': currentPage !== page}" class="w-8 h-8 rounded-lg border text-xs font-bold transition flex items-center justify-center">
                            {{ page }}
                        </button>
                    </div>
                    <button :disabled="currentPage === totalPages" @click="currentPage++" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 disabled:opacity-50 transition">
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </WakifLayout>
</template>
