<script setup lang="ts">
import WakifLayout from '@/Layouts/WakifLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, computed, watch } from 'vue';
import axios from 'axios';

// Month & Year filters
const selectedMonth = ref<string | number>(''); // default: all months
const selectedYear = ref(new Date().getFullYear());
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

// Data states
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

// Formatters
const formatRupiah = (num: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
};

const formatShortCurrency = (num: number) => {
    if (num >= 1000000000) return 'Rp' + (num / 1000000000).toFixed(1).replace('.0', '') + 'M';
    if (num >= 1000000) return 'Rp' + (num / 1000000).toFixed(1).replace('.0', '') + 'Jt';
    return formatRupiah(num);
};

const getMonthName = (monthNum: number) => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    return months[monthNum - 1] || '';
};

// Fetch data
const fetchCounters = async () => {
    try {
        let url = '/api/wakif/counter?';
        if (selectedMonth.value) url += `bulan=${selectedMonth.value}&`;
        if (selectedYear.value) url += `tahun=${selectedYear.value}`;
        const res = await axios.get(url);
        if (res.data && res.data.data) {
            counters.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch counters:', e);
    }
};

const fetchPenyebaran = async () => {
    try {
        let url = '/api/wakif/penyebaran-program?';
        if (selectedMonth.value) url += `bulan=${selectedMonth.value}&`;
        if (selectedYear.value) url += `tahun=${selectedYear.value}`;
        const res = await axios.get(url);
        if (res.data && res.data.data) {
            penyebaran.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch penyebaran program:', e);
    }
};

const fetchTrend = async () => {
    isLoadingTrend.value = true;
    try {
        const res = await axios.get(`/api/wakif/trend-wakaf?tahun=${selectedYear.value}`);
        if (res.data && res.data.data) {
            trendData.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch trend wakaf:', e);
    } finally {
        isLoadingTrend.value = false;
    }
};

onMounted(() => {
    fetchCounters();
    fetchPenyebaran();
    fetchTrend();
});

watch([selectedMonth, selectedYear], () => {
    fetchCounters();
    fetchPenyebaran();
    fetchTrend();
});

// Chart computed properties
// Map trendData to full 12 months array
const fullYearTrend = computed(() => {
    const trendMap = new Map();
    trendData.value.forEach(item => {
        trendMap.set(Number(item.bulan), Number(item.wakaf_terkumpul));
    });

    const result = [];
    for (let m = 1; m <= 12; m++) {
        result.push({
            bulan: m,
            label: getMonthName(m),
            val: trendMap.has(m) ? trendMap.get(m) : 0
        });
    }
    return result;
});

// Max value to scale SVG height
const maxTrendValue = computed(() => {
    const vals = fullYearTrend.value.map(item => item.val);
    const max = Math.max(...vals);
    return max === 0 ? 100000 : max * 1.1; // padding 10%
});

// SVG dimensions
const chartWidth = 700;
const chartHeight = 300;
const paddingX = 40;
const paddingY = 30;

// Coordinate mapping
const points = computed(() => {
    return fullYearTrend.value.map((item, idx) => {
        const x = paddingX + (idx / 11) * (chartWidth - paddingX * 2);
        const y = chartHeight - paddingY - (item.val / maxTrendValue.value) * (chartHeight - paddingY * 2);
        return { x, y, val: item.val, label: item.label };
    });
});

// Area path string (goes down to bottom line for gradient fill)
const areaPath = computed(() => {
    if (points.value.length === 0) return '';
    let p = `M ${points.value[0].x} ${points.value[0].y}`;
    for (let i = 1; i < points.value.length; i++) {
        p += ` L ${points.value[i].x} ${points.value[i].y}`;
    }
    // close the path
    p += ` L ${points.value[points.value.length - 1].x} ${chartHeight - paddingY}`;
    p += ` L ${points.value[0].x} ${chartHeight - paddingY} Z`;
    return p;
});

// Line path string (only outlines the points)
const linePath = computed(() => {
    if (points.value.length === 0) return '';
    let p = `M ${points.value[0].x} ${points.value[0].y}`;
    for (let i = 1; i < points.value.length; i++) {
        p += ` L ${points.value[i].x} ${points.value[i].y}`;
    }
    return p;
});
</script>

<template>
    <Head title="Laporan Perkembangan Wakaf" />

    <WakifLayout>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-[#1e5842] to-[#143E2C] py-12 px-6 lg:px-12 text-white">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black mb-2">Laporan Perkembangan & Transparansi</h1>
                    <p class="text-xs text-gray-200/90 max-w-xl leading-relaxed">
                        Kami menyajikan laporan penyaluran dana wakaf dan grafik trend pengumpulan secara realtime sebagai bentuk amanah dan tanggung jawab kami.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold text-gray-700">Bulan:</span>
                        <select v-model="selectedMonth" class="bg-white border border-gray-300 rounded px-3 py-1.5 text-xs text-gray-700 font-semibold focus:outline-none focus:ring-1 focus:ring-[#1e5842]">
                            <option v-for="m in monthsList" :key="m.value" :value="m.value" class="text-gray-750 font-medium">{{ m.label }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold text-gray-700">Tahun:</span>
                        <select v-model="selectedYear" class="bg-white border border-gray-300 rounded px-3 py-1.5 text-xs text-gray-700 font-semibold focus:outline-none focus:ring-1 focus:ring-[#1e5842]">
                            <option v-for="y in yearsList" :key="y" :value="y" class="text-gray-750 font-medium">{{ y }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Grid (Counter) -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-10 -mt-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 bg-white border border-gray-150 rounded-2xl shadow-xl shadow-gray-100 p-6 md:p-8">
                <div class="flex flex-col items-center text-center">
                    <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ counters.program }}</span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Program Wakaf</span>
                </div>
                
                <div class="flex flex-col items-center text-center border-l border-gray-100">
                    <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ formatShortCurrency(counters.wakaf_terkumpul) }}</span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Wakaf Terkumpul</span>
                </div>

                <div class="flex flex-col items-center text-center border-l border-gray-100">
                    <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ counters.donatur }}</span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Donatur Terdaftar</span>
                </div>

                <div class="flex flex-col items-center text-center border-l border-gray-100">
                    <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ counters.penerima_manfaat }}</span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Penerima Manfaat</span>
                </div>
            </div>
        </section>

        <!-- Charts Dashboard Section -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-6 mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Chart 1: Trend Wakaf (Line / Area Chart) -->
                <div class="lg:col-span-2 bg-white border border-gray-150 rounded-2xl p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-sm font-black text-gray-900">Trend Wakaf Per Bulan</h3>
                            <p class="text-[10px] text-gray-400 font-semibold">Total dana masuk untuk tahun {{ selectedYear }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary inline-block"></span>
                            <span class="text-[10px] font-bold text-gray-500">Nominal Wakaf</span>
                        </div>
                    </div>

                    <!-- SVG Chart Container -->
                    <div class="relative w-full overflow-hidden mt-4">
                        <div v-if="isLoadingTrend" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        </div>

                        <!-- Render SVG -->
                        <svg :viewBox="`0 0 ${chartWidth} ${chartHeight}`" width="100%" height="auto" class="overflow-visible">
                            <defs>
                                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#1e5842" stop-opacity="0.3"/>
                                    <stop offset="100%" stop-color="#1e5842" stop-opacity="0.0"/>
                                </linearGradient>
                            </defs>

                            <!-- Horizontal Grid Lines -->
                            <line v-for="grid in 5" :key="grid" 
                                  :x1="paddingX" 
                                  :y1="paddingY + ((grid - 1) / 4) * (chartHeight - paddingY * 2)" 
                                  :x2="chartWidth - paddingX" 
                                  :y2="paddingY + ((grid - 1) / 4) * (chartHeight - paddingY * 2)" 
                                  stroke="#f1f5f9" 
                                  stroke-width="1.5" />

                            <!-- X Axis Line -->
                            <line :x1="paddingX" :y1="chartHeight - paddingY" :x2="chartWidth - paddingX" :y2="chartHeight - paddingY" stroke="#cbd5e1" stroke-width="1.5" />

                            <!-- Area Path -->
                            <path :d="areaPath" fill="url(#chartGrad)" />

                            <!-- Glowing Line Path -->
                            <path :d="linePath" fill="none" stroke="#1e5842" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                            <!-- Interactive Points -->
                            <g v-for="(p, i) in points" :key="i">
                                <!-- Glow ring on hover -->
                                <circle :cx="p.x" :cy="p.y" r="8" 
                                        :fill="activeTooltipIndex === i ? 'rgba(30, 88, 66, 0.2)' : 'transparent'" 
                                        class="transition duration-150" />
                                
                                <!-- Core dot -->
                                <circle :cx="p.x" :cy="p.y" r="4.5" 
                                        :fill="activeTooltipIndex === i ? '#b1cf49' : '#1e5842'" 
                                        stroke="#ffffff" 
                                        stroke-width="1.5"
                                        @mouseenter="activeTooltipIndex = i"
                                        @mouseleave="activeTooltipIndex = null"
                                        class="cursor-pointer transition duration-150" />

                                <!-- Month label (X Axis) -->
                                <text :x="p.x" :y="chartHeight - 10" 
                                      text-anchor="middle" 
                                      fill="#64748b" 
                                      class="text-[10px] font-bold font-sans">
                                    {{ p.label }}
                                </text>
                            </g>

                            <!-- Tooltip drawn in SVG -->
                            <g v-if="activeTooltipIndex !== null">
                                <rect :x="points[activeTooltipIndex].x - 65" 
                                      :y="points[activeTooltipIndex].y - 45" 
                                      width="130" 
                                      height="32" 
                                      rx="6" 
                                      fill="#1e293b" 
                                      shadow="0 4px 6px -1px rgb(0 0 0 / 0.1)" />
                                
                                <text :x="points[activeTooltipIndex].x" 
                                      :y="points[activeTooltipIndex].y - 25" 
                                      text-anchor="middle" 
                                      fill="#ffffff" 
                                      class="text-[9.5px] font-black">
                                    {{ formatRupiah(points[activeTooltipIndex].val) }}
                                </text>
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Chart 2: Penyebaran Program -->
                <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 mb-2">Penyebaran Program</h3>
                        <p class="text-[10px] text-gray-400 font-semibold mb-6">Persentase donatur berdasarkan program wakaf</p>

                        <div v-if="penyebaran.length === 0" class="text-center text-xs text-gray-400 py-12">
                            Belum ada penyebaran donasi program.
                        </div>
                        <div v-else class="space-y-5">
                            <div v-for="(p, i) in penyebaran" :key="i" class="space-y-1.5">
                                <div class="flex justify-between items-center text-xs font-bold text-gray-700">
                                    <span class="truncate max-w-[180px]">{{ p.nama_program || 'Program Wakaf' }}</span>
                                    <span class="text-primary font-black">{{ p.persen }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                    <!-- Animated width bar -->
                                    <div class="h-full bg-gradient-to-r from-[#1e5842] to-[#b1cf49] rounded-full transition-all duration-1000 ease-out" 
                                         :style="{ width: `${p.persen}%` }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4 mt-6 text-[9.5px] text-gray-400 font-semibold text-center leading-relaxed">
                        Data distribusi di atas dihitung secara realtime berdasarkan jumlah seluruh transaksi wakaf yang sukses.
                    </div>
                </div>

            </div>
        </section>
    </WakifLayout>
</template>

<style scoped>
select {
    cursor: pointer;
}
</style>
