<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import WakifLayout from '@/layouts/WakifLayout.vue';

const props = defineProps<{
    id: string | number;
}>();

const transaction = ref<any>(null);
const isLoading = ref(true);

const getHeaders = () => {
    const token = localStorage.getItem('wakif_auth_token');

    return token ? { Authorization: `Bearer ${token}` } : {};
};

const fetchDetail = async () => {
    isLoading.value = true;

    try {
        const response = await axios.get(`/api/wakif/transaksi/${props.id}`, {
            headers: getHeaders()
        });

        if (response.data && response.data.success) {
            transaction.value = response.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch transaction invoice detail:', e);
    } finally {
        isLoading.value = false;
    }
};

const formatRupiah = (value: number) => {
    if (value === undefined || value === null) {
return '-';
}

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const formatDate = (dateStr: string) => {
    if (!dateStr) {
return '-';
}

    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const handleDownloadPDF = () => {
    window.print();
};

onMounted(() => {
    fetchDetail();
});
</script>

<template>
    <Head title="Kuitansi Transaksi Wakaf" />

    <WakifLayout>
        <div class="max-w-3xl mx-auto px-6 py-12 print:py-0 print:px-0">
            <!-- Navigation Back (Hidden in Print) -->
            <div class="mb-6 print:hidden flex items-center justify-between">
                <Link href="/riwayat-transaksi" class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-primary gap-1.5 transition">
                    <span>&larr;</span> Kembali ke Riwayat
                </Link>
                <button @click="handleDownloadPDF" class="inline-flex items-center px-4 py-2 bg-[#638734] hover:bg-[#1e5842] text-white font-bold rounded-lg text-xs shadow-sm transition gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Download PDF
                </button>
            </div>

            <!-- Loading Spinner -->
            <div v-if="isLoading" class="flex justify-center items-center py-32 print:hidden">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#638734]"></div>
            </div>

            <!-- Invoice Card -->
            <div v-else-if="transaction" id="invoice-card" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden p-8 md:p-12 print:border-none print:shadow-none print:rounded-none">
                <!-- Invoice Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-150 pb-8 gap-6">
                    <div>
                        <div class="text-2xl font-black italic text-[#638734] leading-tight flex flex-col mb-2">
                            <span>Amanah</span>
                            <span>Baiturrahman</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium">Jl. Gedong Lima No.30, Padalarang, Bandung Barat</p>
                    </div>
                    <div class="sm:text-right">
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">KUITANSI WAKAF</h2>
                        <span class="block text-xs font-mono font-bold text-[#638734] mt-1">
                            {{ transaction.kode_referensi || ('WKF-' + String(transaction.id_transaksi).padStart(6, '0')) }}
                        </span>
                    </div>
                </div>

                <!-- Invoice Meta Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-8 border-b border-gray-150 text-xs">
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Wakif / Donatur</span>
                        <span class="block font-black text-gray-900 text-sm">{{ transaction.nama_donatur || 'Hamba Allah' }}</span>
                        <span class="block text-gray-500 mt-1">Status Keanggotaan: Wakif Terdaftar</span>
                    </div>
                    <div class="sm:text-right">
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Rincian Pembayaran</span>
                        <span class="block text-gray-500">Tanggal: <span class="font-bold text-gray-900">{{ formatDate(transaction.created_at) }}</span></span>
                        <span class="block text-gray-500 mt-1">Metode: <span class="font-bold text-gray-900 uppercase">{{ transaction.metode_pembayaran || 'QRIS' }}</span></span>
                    </div>
                </div>

                <!-- Invoice Table -->
                <div class="py-8">
                    <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-4">Deskripsi Wakaf</span>
                    <div class="border border-gray-150 rounded-xl overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3 px-4">Item Penyaluran</th>
                                    <th class="py-3 px-4 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-4 px-4">
                                        <span class="text-xs font-bold text-gray-900 block">
                                            Wakaf Tunai
                                        </span>
                                        <span class="text-[10px] text-gray-400 block mt-0.5 max-w-md">
                                            Disalurkan untuk program: {{ transaction.t03_program_wakaf?.nama_program || 'Program Wakaf Baiturrahman' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <span class="text-xs font-black text-gray-900">
                                            {{ formatRupiah(transaction.nominal) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="bg-gray-50 border-t border-gray-150 font-bold">
                                    <td class="py-3 px-4 text-xs text-gray-950 uppercase">Total Pembayaran</td>
                                    <td class="py-3 px-4 text-right text-sm font-black text-primary">
                                        {{ formatRupiah(transaction.nominal) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Invoice Footer Notes -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-8 border-t border-gray-150 gap-6">
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Pembayaran</span>
                        <span v-if="transaction.status_pembayaran === 1" class="inline-block px-3 py-1 rounded-full bg-green-50 text-[#1e5842] border border-green-200 text-[10px] font-black uppercase">
                            Berhasil / Lunas
                        </span>
                        <span v-else-if="transaction.status_pembayaran === 2" class="inline-block px-3 py-1 rounded-full bg-red-50 text-red-800 border border-red-200 text-[10px] font-black uppercase">
                            Gagal
                        </span>
                        <span v-else class="inline-block px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 border border-yellow-200 text-[10px] font-black uppercase">
                            Menunggu Verifikasi
                        </span>
                    </div>
                    <div class="text-center sm:text-right self-stretch sm:self-auto flex flex-col items-center sm:items-end">
                        <p class="text-[10px] text-gray-400 font-semibold mb-8">Tertanda,</p>
                        <div class="border-b border-gray-300 w-32 pb-1 text-center font-bold text-xs text-gray-900">
                            Pengelola Baiturrahman
                        </div>
                        <span class="text-[9px] text-gray-400 mt-1 block">Kuitansi sah dicetak secara elektronik</span>
                    </div>
                </div>
            </div>

            <!-- Error fallback -->
            <div v-else class="text-center py-24">
                <p class="text-sm text-gray-400 font-semibold">Gagal memuat detail transaksi. Silakan coba kembali.</p>
                <Link href="/riwayat-transaksi" class="inline-block mt-4 px-6 py-2 bg-[#638734] text-white text-xs font-bold rounded-lg hover:bg-[#1e5842] transition">
                    Kembali ke Riwayat
                </Link>
            </div>
        </div>
    </WakifLayout>
</template>

<style>
@media print {
    /* Hide layout navigation, sidebar, header, and footer */
    nav, footer, header, aside,
    .print\:hidden,
    [class*="sidebar"],
    [class*="md:pl-64"] > header {
        display: none !important;
    }
    
    /* Reset page layout for print */
    body, .min-h-screen, .flex-grow {
        background-color: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Remove sidebar offset */
    [class*="md:pl-64"] {
        padding-left: 0 !important;
    }

    /* Remove shadow and border radius in print */
    #invoice-card {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
}
</style>
