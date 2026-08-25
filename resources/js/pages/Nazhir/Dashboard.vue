<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, computed, watch } from 'vue';
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

// Tabs
const activeTab = ref<'laporan' | 'transaksi'>('laporan');

// Programs dropdown helper
const programsList = ref<Array<{ id_program: number; nama_program: string }>>([]);
const fetchProgramsDropdown = async () => {
    try {
        const res = await axios.get('/api/nazhir/program?all=1');

        if (res.data && res.data.data) {
            programsList.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch programs for filter:', e);
    }
};
// ==================== LAPORAN TAB STATES & METHODS ====================
const selectedMonth = ref<string | number>('');
const selectedYear = ref(new Date().getFullYear());
const selectedProgram = ref<string | number>(''); // program filter for counters
const yearsList = ref([2024, 2025, 2026, 2027]);
const monthsList = ref([
    { value: '', label: 'Semua Bulan' },
    { value: 1, label: 'Januari' },
    { value: 2, label: 'Februari' },
    { value: 3, label: 'Maret' },
    { value: 4, label: 'April' },
    { value: 5, label: 'Mei' },
    { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' },
    { value: 8, label: 'Agustus' },
    { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' },
    { value: 11, label: 'November' },
    { value: 12, label: 'Desember' }
]);

const counters = ref({
    program: 0,
    wakaf_terkumpul: 0,
    donatur: 0,
    penerima_manfaat: 0
});
const penyebaran = ref<Array<{ nama_program: string; persen: number }>>([]);
const trendData = ref<Array<{ bulan: number; wakaf_terkumpul: number | string }>>([]);
const isLoadingTrend = ref(false);
const activeTooltipIndex = ref<number | null>(null);

const formatRupiah = (num: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
};

const formatShortCurrency = (num: number) => {
    if (num >= 1000000000) {
return 'Rp' + (num / 1000000000).toFixed(1).replace('.0', '') + 'M';
}

    if (num >= 1000000) {
return 'Rp' + (num / 1000000).toFixed(1).replace('.0', '') + 'Jt';
}

    return formatRupiah(num);
};

const getMonthName = (monthNum: number) => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

    return months[monthNum - 1] || '';
};

const fetchCounters = async () => {
    try {
        let url = '/api/nazhir/counter?';

        if (selectedMonth.value) {
url += `bulan=${selectedMonth.value}&`;
}

        if (selectedYear.value) {
url += `tahun=${selectedYear.value}&`;
}

        if (selectedProgram.value) {
url += `program=${selectedProgram.value}`;
}

        const res = await axios.get(url);

        if (res.data && res.data.data) {
            counters.value = res.data.data;
        }
    } catch (e) {
        console.error(e);
    }
};

const fetchPenyebaran = async () => {
    try {
        let url = '/api/nazhir/penyebaran-program?';

        if (selectedMonth.value) {
url += `bulan=${selectedMonth.value}&`;
}

        if (selectedYear.value) {
url += `tahun=${selectedYear.value}`;
}

        const res = await axios.get(url);

        if (res.data && res.data.data) {
            penyebaran.value = res.data.data;
        }
    } catch (e) {
        console.error(e);
    }
};

const fetchTrend = async () => {
    isLoadingTrend.value = true;

    try {
        let url = `/api/nazhir/trend-wakaf?tahun=${selectedYear.value}`;
        if (selectedMonth.value) {
            url += `&bulan=${selectedMonth.value}`;
        }
        if (selectedProgram.value) {
            url += `&program=${selectedProgram.value}`;
        }
        const res = await axios.get(url);

        if (res.data && res.data.data) {
            trendData.value = res.data.data;
        }
    } catch (e) {
        console.error(e);
    } finally {
        isLoadingTrend.value = false;
    }
};

// SVG calculations
const chartTrendData = computed(() => {
    const result = [];
    if (!selectedMonth.value) {
        // Full year (12 months)
        const trendMap = new Map();
        trendData.value.forEach(item => {
            trendMap.set(Number(item.bulan), Number(item.wakaf_terkumpul));
        });

        for (let m = 1; m <= 12; m++) {
            result.push({
                key: m,
                label: getMonthName(m),
                val: trendMap.has(m) ? trendMap.get(m) : 0,
                fullLabel: `${getMonthName(m)} ${selectedYear.value}`
            });
        }
    } else {
        // Selected month (days of month)
        const monthNum = Number(selectedMonth.value);
        const yearNum = Number(selectedYear.value);
        const daysInMonth = new Date(yearNum, monthNum, 0).getDate();

        const trendMap = new Map();
        trendData.value.forEach(item => {
            const dayKey = Number(item.tanggal || item.tgl || item.bulan);
            trendMap.set(dayKey, Number(item.wakaf_terkumpul));
        });

        for (let d = 1; d <= daysInMonth; d++) {
            result.push({
                key: d,
                label: `${d}`,
                val: trendMap.has(d) ? trendMap.get(d) : 0,
                fullLabel: `Tgl ${d} ${getMonthName(monthNum)} ${yearNum}`
            });
        }
    }
    return result;
});

const maxTrendValue = computed(() => {
    const vals = chartTrendData.value.map(item => item.val);
    const max = Math.max(...vals, 0);

    return max === 0 ? 100000 : max * 1.1;
});

const chartWidth = 700;
const chartHeight = 300;
const paddingX = 40;
const paddingY = 30;

const points = computed(() => {
    const len = chartTrendData.value.length;
    if (len === 0) return [];

    const divisor = len > 1 ? len - 1 : 1;

    return chartTrendData.value.map((item, idx) => {
        const x = paddingX + (idx / divisor) * (chartWidth - paddingX * 2);
        const y = chartHeight - paddingY - (item.val / maxTrendValue.value) * (chartHeight - paddingY * 2);

        return { x, y, val: item.val, label: item.label, fullLabel: item.fullLabel };
    });
});

const areaPath = computed(() => {
    if (points.value.length === 0) {
        return '';
    }

    let p = `M ${points.value[0].x} ${points.value[0].y}`;

    for (let i = 1; i < points.value.length; i++) {
        p += ` L ${points.value[i].x} ${points.value[i].y}`;
    }

    p += ` L ${points.value[points.value.length - 1].x} ${chartHeight - paddingY}`;
    p += ` L ${points.value[0].x} ${chartHeight - paddingY} Z`;

    return p;
});

const linePath = computed(() => {
    if (points.value.length === 0) {
        return '';
    }

    let p = `M ${points.value[0].x} ${points.value[0].y}`;

    for (let i = 1; i < points.value.length; i++) {
        p += ` L ${points.value[i].x} ${points.value[i].y}`;
    }

    return p;
});


// ==================== TRANSAKSI TAB STATES & METHODS ====================

const todayDate = computed(() => new Date().toLocaleDateString('en-CA'));

const txStart = ref('');
const txEnd = ref('');
const tempTxStart = ref('');
const tempTxEnd = ref('');
const showTxDatePicker = ref(false);

const applyTxDateFilter = () => {
    if (tempTxStart.value && tempTxStart.value > todayDate.value) {
        tempTxStart.value = todayDate.value;
    }
    if (tempTxEnd.value && tempTxEnd.value > todayDate.value) {
        tempTxEnd.value = todayDate.value;
    }
    txStart.value = tempTxStart.value;
    txEnd.value = tempTxEnd.value;
    showTxDatePicker.value = false;
};

const clearTxDateFilter = () => {
    txStart.value = '';
    txEnd.value = '';
    tempTxStart.value = '';
    tempTxEnd.value = '';
    showTxDatePicker.value = false;
};

const txProgram = ref<string | number>('');
const txLimit = ref(10);
const txSort = ref('desc');
const txPage = ref(1);
const transactions = ref<any[]>([]);
const txPagination = ref<any>({});
const isLoadingTx = ref(false);

// Modal states
const showImageModal = ref(false);
const activeImageUrl = ref('');

const fetchTransactions = async () => {
    isLoadingTx.value = true;

    try {
        let url = `/api/nazhir/transaksi?limit=${txLimit.value}&page=${txPage.value}&sort=${txSort.value}`;

        if (txStart.value) {
url += `&start=${txStart.value}`;
}

        if (txEnd.value) {
url += `&end=${txEnd.value}`;
}

        if (txProgram.value) {
url += `&program=${txProgram.value}`;
}
        
        const res = await axios.get(url);

        if (res.data && res.data.data) {
            transactions.value = res.data.data.data || [];
            txPagination.value = {
                current_page: res.data.data.current_page,
                last_page: res.data.data.last_page,
                total: res.data.data.total,
                from: res.data.data.from,
                to: res.data.data.to
            };
        }
    } catch (e) {
        console.error('Failed to fetch transactions:', e);
    } finally {
        isLoadingTx.value = false;
    }
};

const handleApprove = async (id: number, status: number) => {
    if (!(await showConfirm(status === 1 ? 'Approve transaksi ini?' : 'Tolak transaksi ini?'))) {
return;
}

    try {
        await axios.put(`/api/nazhir/transaksi/${id}/approve`, { status_pembayaran: status });
        await showSuccess('Status pembayaran berhasil diperbarui!');
        fetchTransactions();
        fetchCounters(); // recalculate counters too
    } catch (e) {
        console.error('Approve failed:', e);
        await showError('Gagal memproses transaksi.');
    }
};

const openImageModal = (url: string) => {
    activeImageUrl.value = url;
    showImageModal.value = true;
};

const exportCsvUrl = computed(() => {
    let url = '/api/nazhir/transaksi/export-csv?';

    if (txStart.value) {
url += `start=${txStart.value}&`;
}

    if (txEnd.value) {
url += `end=${txEnd.value}&`;
}

    if (txProgram.value) {
url += `program=${txProgram.value}&`;
}

    url += `sort=${txSort.value}`;

    return url;
});

const triggerCsvExport = () => {
    window.open(exportCsvUrl.value, '_blank');
};

const handlePrevPage = () => {
    if (txPage.value > 1) {
        txPage.value--;
        fetchTransactions();
    }
};

const handleNextPage = () => {
    if (txPage.value < txPagination.value.last_page) {
        txPage.value++;
        fetchTransactions();
    }
};

// Initial triggers
onMounted(() => {
    fetchProgramsDropdown();
    fetchCounters();
    fetchPenyebaran();
    fetchTrend();
    fetchTransactions();
});

// Watchers for Laporan Tab Filters
watch([selectedMonth, selectedYear, selectedProgram], () => {
    fetchCounters();
    fetchPenyebaran();
    fetchTrend();
});

// Watchers for Transaksi filters/limit resetting page to 1
watch([txStart, txEnd, txProgram, txLimit, txSort], () => {
    txPage.value = 1;
    fetchTransactions();
});
</script>

<template>
  <Head title="Nazhir Dashboard" />

  <NazhirLayout>
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Top Title and Tabs Toggle -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-black text-gray-900 leading-tight">Dashboard Overview</h1>
          <p class="text-xs text-gray-500">Monitor perkembangan program wakaf dan persetujuan transaksi masuk.</p>
        </div>

        <!-- Tab Toggle Buttons -->
        <div class="flex bg-gray-200 p-1 rounded-xl self-stretch md:self-auto shadow-inner">
          <button 
            @click="activeTab = 'laporan'"
            :class="[
              'flex-1 md:flex-initial px-6 py-2 rounded-lg text-xs font-bold transition duration-200',
              activeTab === 'laporan' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            Laporan & Perkembangan
          </button>
          <button 
            @click="activeTab = 'transaksi'"
            :class="[
              'flex-1 md:flex-initial px-6 py-2 rounded-lg text-xs font-bold transition duration-200',
              activeTab === 'transaksi' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            Manajemen Wakif
          </button>
        </div>
      </div>

      <!-- ==================== TAB 1: LAPORAN ==================== -->
      <div v-if="activeTab === 'laporan'" class="space-y-6">
        
        <!-- Filters Banner -->
        <section class="bg-gradient-to-r from-[#1e5842] to-[#143E2C] py-8 px-6 md:px-8 rounded-2xl text-white shadow-md">
          <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
              <h2 class="text-xl font-black mb-1">Filter Laporan Realtime</h2>
              <p class="text-[11px] text-gray-200/90 leading-relaxed max-w-lg">
                Pilih filter bulan, tahun, dan program wakaf di sebelah kanan untuk memperbarui visualisasi statistik secara detail.
              </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 bg-white/10 backdrop-blur-xs p-3 rounded-xl border border-white/10 w-full lg:w-auto">
              <div class="flex flex-col flex-1 sm:flex-initial">
                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-wider mb-1">Bulan</span>
                <select v-model="selectedMonth" class="bg-[#143E2C] border border-white/20 rounded px-2.5 py-1 text-xs text-white font-semibold focus:outline-none focus:ring-1 focus:ring-[#b1cf49] cursor-pointer">
                  <option v-for="m in monthsList" :key="m.value" :value="m.value">{{ m.label }}</option>
                </select>
              </div>
              
              <div class="flex flex-col flex-1 sm:flex-initial">
                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-wider mb-1">Tahun</span>
                <select v-model="selectedYear" class="bg-[#143E2C] border border-white/20 rounded px-2.5 py-1 text-xs text-white font-semibold focus:outline-none focus:ring-1 focus:ring-[#b1cf49] cursor-pointer">
                  <option v-for="y in yearsList" :key="y" :value="y">{{ y }}</option>
                </select>
              </div>

              <div class="flex flex-col w-full sm:w-48">
                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-wider mb-1">Program Wakaf</span>
                <select v-model="selectedProgram" class="bg-[#143E2C] border border-white/20 rounded px-2.5 py-1 text-xs text-white font-semibold focus:outline-none focus:ring-1 focus:ring-[#b1cf49] cursor-pointer truncate">
                  <option value="">Semua Program</option>
                  <option v-for="p in programsList" :key="p.id_program" :value="p.id_program">{{ p.nama_program }}</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        <!-- Stats Counters -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Program Wakaf</span>
            <div class="flex items-baseline justify-between mt-2">
              <span class="text-2xl font-black text-gray-900">{{ counters.program }}</span>
              <div class="p-1.5 bg-[#b1cf49]/10 text-[#143E2C] rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
              </div>
            </div>
          </div>

          <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Wakaf Terkumpul</span>
            <div class="flex items-baseline justify-between mt-2">
              <span class="text-2xl font-black text-gray-900 truncate mr-2">{{ formatShortCurrency(counters.wakaf_terkumpul) }}</span>
              <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
            </div>
          </div>

          <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Donatur Terdaftar</span>
            <div class="flex items-baseline justify-between mt-2">
              <span class="text-2xl font-black text-gray-900">{{ counters.donatur }}</span>
              <div class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0" /></svg>
              </div>
            </div>
          </div>

          <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Penerima Manfaat</span>
            <div class="flex items-baseline justify-between mt-2">
              <span class="text-2xl font-black text-gray-900">{{ counters.penerima_manfaat }}</span>
              <div class="p-1.5 bg-orange-50 text-orange-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
              </div>
            </div>
          </div>
        </section>

        <!-- Visual Charts Grid -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Monthly Trend Line Chart -->
          <div class="lg:col-span-2 bg-white border border-gray-150 rounded-2xl p-6 shadow-xs relative">
            <div class="flex justify-between items-center mb-6">
              <div>
                <h3 class="text-sm font-black text-gray-900">{{ selectedMonth ? 'Trend Wakaf Per Tanggal' : 'Trend Wakaf Per Bulan' }}</h3>
                <p class="text-[10px] text-gray-400 font-semibold">{{ selectedMonth ? `Total dana masuk bulan ${monthsList.find(m => m.value == selectedMonth)?.label || ''} ${selectedYear}` : `Total dana masuk untuk tahun ${selectedYear}` }}</p>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#143E2C] inline-block"></span>
                <span class="text-[10px] font-bold text-gray-500">Nominal Wakaf</span>
              </div>
            </div>

            <!-- SVG Line Chart container -->
            <div class="relative w-full overflow-hidden min-h-[220px]">
              <div v-if="isLoadingTrend" class="absolute inset-0 bg-white/75 backdrop-blur-xs flex items-center justify-center z-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#143E2C]"></div>
              </div>

              <svg :viewBox="`0 0 ${chartWidth} ${chartHeight}`" width="100%" height="auto" class="overflow-visible">
                <defs>
                  <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#143E2C" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#143E2C" stop-opacity="0.0"/>
                  </linearGradient>
                </defs>
                <line v-for="grid in 5" :key="grid" 
                      :x1="paddingX" 
                      :y1="paddingY + ((grid - 1) / 4) * (chartHeight - paddingY * 2)" 
                      :x2="chartWidth - paddingX" 
                      :y2="paddingY + ((grid - 1) / 4) * (chartHeight - paddingY * 2)" 
                      stroke="#f1f5f9" stroke-width="1.5" />
                <line :x1="paddingX" :y1="chartHeight - paddingY" :x2="chartWidth - paddingX" :y2="chartHeight - paddingY" stroke="#cbd5e1" stroke-width="1.5" />
                <path :d="areaPath" fill="url(#chartGrad)" />
                <path :d="linePath" fill="none" stroke="#143E2C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <g v-for="(p, i) in points" :key="i">
                  <circle :cx="p.x" :cy="p.y" r="8" 
                          :fill="activeTooltipIndex === i ? 'rgba(20, 62, 44, 0.2)' : 'transparent'" 
                          class="transition duration-150" />
                  <circle :cx="p.x" :cy="p.y" r="4.5" 
                          :fill="activeTooltipIndex === i ? '#b1cf49' : '#143E2C'" 
                          stroke="#ffffff" stroke-width="1.5"
                          @mouseenter="activeTooltipIndex = i"
                          @mouseleave="activeTooltipIndex = null"
                          class="cursor-pointer transition duration-150" />
                  <text :x="p.x" :y="chartHeight - 10" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold font-sans">
                    {{ p.label }}
                  </text>
                </g>
                <g v-if="activeTooltipIndex !== null">
                  <rect :x="Math.max(10, Math.min(chartWidth - 145, points[activeTooltipIndex].x - 67.5))" 
                        :y="Math.max(10, points[activeTooltipIndex].y - 48)" 
                        width="135" 
                        height="38" 
                        rx="6" 
                        fill="#1e293b" 
                        shadow="0 4px 6px -1px rgb(0 0 0 / 0.1)" />
                  <text :x="Math.max(77.5, Math.min(chartWidth - 77.5, points[activeTooltipIndex].x))" 
                        :y="Math.max(24, points[activeTooltipIndex].y - 32)" 
                        text-anchor="middle" 
                        fill="#94a3b8" 
                        class="text-[8.5px] font-bold">
                    {{ points[activeTooltipIndex].fullLabel }}
                  </text>
                  <text :x="Math.max(77.5, Math.min(chartWidth - 77.5, points[activeTooltipIndex].x))" 
                        :y="Math.max(39, points[activeTooltipIndex].y - 17)" 
                        text-anchor="middle" 
                        fill="#ffffff" 
                        class="text-[9.5px] font-black">
                    {{ formatRupiah(points[activeTooltipIndex].val) }}
                  </text>
                </g>
              </svg>
            </div>
          </div>

          <!-- Penyebaran Program -->
          <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
              <h3 class="text-sm font-black text-gray-900 mb-1">Penyebaran Program</h3>
              <p class="text-[10px] text-gray-400 font-semibold mb-6">Persentase donatur berdasarkan program wakaf</p>

              <div v-if="penyebaran.length === 0" class="text-center text-xs text-gray-400 py-12">
                Belum ada penyebaran donasi program.
              </div>
              <div v-else class="space-y-4">
                <div v-for="(p, i) in penyebaran" :key="i" class="space-y-1">
                  <div class="flex justify-between items-center text-xs font-bold text-gray-700">
                    <span class="truncate max-w-[170px]">{{ p.nama_program }}</span>
                    <span class="text-[#143E2C] font-black">{{ p.persen }}%</span>
                  </div>
                  <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-[#143E2C] to-[#b1cf49] rounded-full transition-all duration-1000 ease-out" 
                         :style="{ width: `${p.persen}%` }">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mt-6 text-[9px] text-gray-400 font-bold text-center leading-relaxed">
              Distribusi dihitung secara realtime berdasarkan jumlah seluruh transaksi wakaf yang berstatus sukses (Berhasil).
            </div>
          </div>
        </section>

      </div>

      <!-- ==================== TAB 2: TRANSAKSI WAKIF ==================== -->
      <div v-else class="space-y-6">
        
        <!-- Search, Filters and Export Banner -->
        <section class="bg-white border border-gray-150 rounded-2xl p-5 shadow-xs flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
          <div class="flex flex-wrap items-center gap-4 flex-1">
            
            <!-- Date Filter Calendar Popover (Modeled after ProgramDetail.vue) -->
            <div class="flex flex-col relative min-w-[200px]">
              <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Filter Tanggal</label>
              <div class="relative max-w-xs flex items-center">
                <input 
                  type="text" 
                  readonly 
                  @click="showTxDatePicker = !showTxDatePicker"
                  :value="txStart && txEnd ? `${txStart} s/d ${txEnd}` : 'Pilih Rentang Tanggal'" 
                  class="w-full bg-gray-50 border border-gray-250 rounded-xl pl-3 pr-10 py-1.5 text-xs font-semibold text-gray-700 cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#143E2C]"
                />
                <button 
                  type="button" 
                  @click="showTxDatePicker = !showTxDatePicker"
                  class="absolute right-0 top-0 bottom-0 px-3 bg-[#1e5842] hover:bg-[#143E2C] text-white rounded-r-xl flex items-center justify-center transition-colors"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </button>
              </div>

              <!-- Date Picker Dropdown Popover -->
              <div v-if="showTxDatePicker" class="absolute left-0 top-[100%] mt-2 z-50 bg-white border border-gray-200 rounded-xl shadow-xl p-4 w-72">
                <h4 class="text-xs font-bold text-gray-750 mb-3 text-center">Pilih Rentang Tanggal</h4>
                <div class="space-y-3">
                  <div>
                    <label class="block text-[10px] text-gray-400 font-bold mb-1">Mulai</label>
                    <input 
                      type="date" 
                      v-model="tempTxStart" 
                      :max="tempTxEnd && tempTxEnd < todayDate ? tempTxEnd : todayDate"
                      class="w-full border border-gray-300 rounded-lg p-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#143E2C]" 
                    />
                  </div>
                  <div>
                    <label class="block text-[10px] text-gray-400 font-bold mb-1">Sampai</label>
                    <input 
                      type="date" 
                      v-model="tempTxEnd" 
                      :min="tempTxStart || undefined"
                      :max="todayDate"
                      class="w-full border border-gray-300 rounded-lg p-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#143E2C]" 
                    />
                  </div>
                  <div class="flex gap-2 justify-end pt-2">
                    <button type="button" @click="showTxDatePicker = false" class="px-3 py-1.5 text-[10px] border border-gray-300 rounded-lg hover:bg-gray-50 font-bold text-gray-600">Batal</button>
                    <button type="button" @click="applyTxDateFilter" class="px-3 py-1.5 text-[10px] bg-[#1e5842] hover:bg-[#143E2C] text-white rounded-lg font-bold">OK</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reset Button for Date Filter -->
            <div v-if="txStart || txEnd" class="flex flex-col justify-end self-end">
              <button 
                type="button" 
                @click="clearTxDateFilter" 
                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-650 rounded-xl text-xs font-bold border border-red-200 transition"
              >
                Reset Tanggal
              </button>
            </div>

            <div class="flex flex-col w-56">
              <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Program Wakaf</label>
              <select 
                v-model="txProgram"
                class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-1.5 text-xs text-gray-700 font-semibold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
              >
                <option value="">Semua Program</option>
                <option v-for="p in programsList" :key="p.id_program" :value="p.id_program">{{ p.nama_program }}</option>
              </select>
            </div>

            <div class="flex flex-col min-w-[80px]">
              <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Limit Row</label>
              <select 
                v-model="txLimit"
                class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-1.5 text-xs text-gray-700 font-semibold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
              >
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
            </div>

            <div class="flex flex-col min-w-[120px]">
              <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Urutan Tanggal</label>
              <select 
                v-model="txSort"
                class="bg-gray-50 border border-gray-250 rounded-xl px-3 py-1.5 text-xs text-gray-700 font-semibold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
              >
                <option value="desc">Terbaru (Desc)</option>
                <option value="asc">Terlama (Asc)</option>
              </select>
            </div>
            <div class="flex items-end justify-end">
                <button @click="fetchTransactions" :disabled="isLoadingTx" class="px-4 py-2.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white rounded-xl text-xs font-bold transition-all w-full sm:w-auto">
                    Refresh Data
                </button>
            </div>

          </div>

          <!-- CSV Export Button -->
          <div class="flex items-end">
            <button 
              @click="triggerCsvExport"
              class="w-full lg:w-auto px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition duration-200"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              Ekspor CSV
            </button>
          </div>
        </section>

        <!-- Transactions Table -->
        <section class="bg-white border border-gray-150 rounded-2xl shadow-xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                  <th class="px-6 py-4">ID Transaksi / Ref</th>
                  <th class="px-6 py-4">Nama Donatur</th>
                  <th class="px-6 py-4">Program Wakaf</th>
                  <th class="px-6 py-4">Nominal</th>
                  <th class="px-6 py-4">Tanggal</th>
                  <th class="px-6 py-4 text-center">Metode</th>
                  <th class="px-6 py-4 text-center">Bukti</th>
                  <th class="px-6 py-4 text-center">Status</th>
                  <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
                <tr v-if="isLoadingTx">
                  <td colspan="9" class="text-center py-10 text-gray-400">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#143E2C] mx-auto"></div>
                  </td>
                </tr>
                <tr v-else-if="transactions.length === 0">
                  <td colspan="9" class="text-center py-10 text-gray-400">Belum ada transaksi ditemukan.</td>
                </tr>
                <tr v-for="t in transactions" :key="t.id_transaksi" class="hover:bg-gray-50/50 transition">
                  <td class="px-6 py-4 font-mono font-bold text-gray-900">
                    {{ t.kode_referensi ?? `WKF-${String(t.id_transaksi).padStart(6, '0')}` }}
                  </td>
                  <td class="px-6 py-4 text-gray-900">{{ t.nama_donatur || 'Hamba Allah' }}</td>
                  <td class="px-6 py-4 min-w-[150px] max-w-[250px] whitespace-normal break-words" :title="t.t03_program_wakaf?.nama_program">
                    {{ t.t03_program_wakaf?.nama_program || '-' }}
                  </td>
                  <td class="px-6 py-4 text-gray-900 font-bold">{{ formatRupiah(t.nominal) }}</td>
                  <td class="px-6 py-4 text-gray-500 font-normal">
                    {{ new Date(t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                  </td>
                  <td class="px-6 py-4 text-center font-bold text-gray-700 uppercase">
                    {{ t.metode_pembayaran || 'QRIS' }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <button 
                      v-if="t.bukti_pembayaran"
                      @click="openImageModal(t.bukti_pembayaran)"
                      class="px-2.5 py-1 text-[10px] font-black uppercase text-[#143E2C] bg-[#b1cf49]/20 hover:bg-[#b1cf49]/35 rounded-md border border-[#b1cf49]/50 transition duration-150"
                    >
                      Lihat Gambar
                    </button>
                    <span v-else class="text-gray-400 italic font-medium">Belum diunggah</span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span 
                      :class="[
                        'inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider',
                        t.status_pembayaran === 1 ? 'bg-green-100 text-green-700' : 
                        t.status_pembayaran === 2 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-750'
                      ]"
                    >
                      {{ t.status_pembayaran === 1 ? 'Berhasil' : t.status_pembayaran === 2 ? 'Gagal' : 'Menunggu' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="t.status_pembayaran === 0" class="flex justify-end gap-1.5">
                      <button 
                        @click="handleApprove(t.id_transaksi, 1)"
                        class="p-1.5 bg-green-500 hover:bg-green-650 text-white rounded-lg transition"
                        title="Approve"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                      </button>
                      <button 
                        @click="handleApprove(t.id_transaksi, 2)"
                        class="p-1.5 bg-red-500 hover:bg-red-650 text-white rounded-lg transition"
                        title="Tolak"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                    </div>
                    <span v-else class="text-[10px] text-gray-400 font-normal">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar -->
          <div class="bg-gray-50 border-t border-gray-150 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-gray-500">
            <span>
              Menampilkan {{ txPagination.from || 0 }} - {{ txPagination.to || 0 }} dari {{ txPagination.total || 0 }} data
            </span>
            
            <div class="flex items-center gap-2">
              <button 
                @click="handlePrevPage" 
                :disabled="txPage === 1"
                class="px-3 py-1.5 rounded-lg border border-gray-250 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white font-black"
              >
                &larr;
              </button>
              <span class="px-2">Halaman {{ txPage }} dari {{ txPagination.last_page || 1 }}</span>
              <button 
                @click="handleNextPage" 
                :disabled="txPage >= txPagination.last_page"
                class="px-3 py-1.5 rounded-lg border border-gray-250 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white font-black"
              >
                &rarr;
              </button>
            </div>
          </div>
        </section>

      </div>

    </div>

    <!-- Image Modal for Bukti Pembayaran -->
    <div 
      v-if="showImageModal" 
      @click="showImageModal = false"
      class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-6"
    >
      <div 
        @click.stop
        class="bg-white rounded-2xl overflow-hidden shadow-2xl max-w-lg w-full flex flex-col relative animate-scale"
      >
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-[#143E2C] text-white">
          <span class="text-sm font-black">Bukti Transfer Pembayaran</span>
          <button @click="showImageModal = false" class="text-white hover:text-gray-200 focus:outline-none font-bold text-base">&times;</button>
        </div>
        <div class="p-6 bg-gray-50 flex items-center justify-center overflow-auto max-h-[70vh]">
          <img :src="activeImageUrl" class="max-w-full h-auto rounded-xl shadow-inner border border-gray-200" alt="Bukti Transfer" />
        </div>
        <div class="px-6 py-4 border-t border-gray-150 flex justify-end bg-gray-50">
          <button 
            @click="showImageModal = false"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-xl text-xs font-black text-gray-700"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

  </NazhirLayout>
</template>

<style scoped>
select, input[type="date"] {
    cursor: pointer;
}
.animate-scale {
    animation: scaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
