<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, computed } from 'vue';
import ProgramCard from '@/components/Wakif/ProgramCard.vue';
import WakifLayout from '@/layouts/WakifLayout.vue';

// Stats state 
const stats = ref({
    program_Count: 0,
    wakaf_terkumpul: 0,
    donatur_count: 0,
    penerima_manfaat: 0
});

// Programs state
const programs = ref([]);
const searchQuery = ref('');

const filteredPrograms = computed(() => {
    if (!searchQuery.value) {
return programs.value;
}

    return programs.value.filter((p: any) => 
        p.nama_program.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        p.deskripsi.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const formatShortCurrency = (num: number) => {
    if (num >= 1000000000) {
return 'Rp' + (num / 1000000000).toFixed(1).replace('.0', '') + 'M';
}

    if (num >= 1000000) {
return 'Rp' + (num / 1000000).toFixed(1).replace('.0', '') + 'Jt';
}

    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
};

onMounted(async () => {
    // Fetch stats counters from backend API
    try {
        const statsResponse = await axios.get('/api/wakif/counter');

        if (statsResponse.data && statsResponse.data.data) {
            const d = statsResponse.data.data;
            stats.value = {
                program_Count: d.program || 0,
                wakaf_terkumpul: Number(d.wakaf_terkumpul) || 0,
                donatur_count: d.donatur || 0,
                penerima_manfaat: Number(d.penerima_manfaat) || 0
            };
        }
    } catch(e) {
        console.error('Failed to fetch stats counters:', e);
    }
    
    // Fetch programs from backend API built earlier
    try {
        const response = await axios.get('/api/wakif/program-wakaf');

        if (response.data && response.data.data) {
              // Map backend format to card format
              programs.value = response.data.data.slice(0, 4).map((p: any) => ({
                  id: p.id_program,
                  nama_program: p.nama_program,
                  deskripsi: p.deskripsi,
                  target_dana: p.target_dana,
                  dana_terkumpul: p.dana_terkumpul,
                  due_date: p.due_date,
                  gambar_placeholder: p.gambar_thumbnail
              }));
        }
    } catch {
        // Fallback dummy
    }
});
</script>

<template>
    <Head title="Beranda" />

    <WakifLayout>
        <!-- Hero Section -->
        <section class="relative w-full h-[450px] bg-gray-900 overflow-hidden flex flex-col justify-center px-6 lg:px-12">
            <!-- Background Image -->
            <div class="absolute inset-0 opacity-60">
                <img src="/dashboard_foto.png" alt="Background Madrasah" class="w-full h-full object-cover">
            </div>
            
            <div class="relative z-10 max-w-2xl text-white mt-12 md:mt-0">
                <h1 class="text-4xl md:text-5xl font-black mb-4 leading-tight tracking-tight text-white">
                    Mahad <br> Baiturrahman
                    <span class="inline-block transform -translate-y-6 ml-2 text-3xl">✨</span>
                </h1>
                <p class="text-xs mb-8 max-w-lg leading-relaxed text-white font-medium">
                    Berbekal semangat syiar Islam, Baiturrahman membuka berbagai program wakaf tunai yang akan disalurkan untuk pondok pesantren, asrama yatim, dan masjid agar terus bisa beroperasi gratis tanpa memungut biaya santri. Mari raih pahala jariyah bersama.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full max-w-lg">
                    <div class="relative w-full sm:w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                               <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="searchQuery" class="block w-full pl-10 pr-3 py-2 border border-transparent rounded-full leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent sm:text-sm" placeholder="Cari program...">
                    </div>
                    <Link href="/tentang" class="inline-flex justify-center items-center px-6 py-2 border border-transparent text-sm font-bold rounded-full text-primary bg-white hover:bg-gray-50 focus:outline-none transition w-full sm:w-auto">
                        Tentang Kami &rarr;
                    </Link>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 text-center divide-x-0 md:divide-x divide-gray-100">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">{{ stats.program_Count }}</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-1">Program Wakaf</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">{{ formatShortCurrency(stats.wakaf_terkumpul) }}</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-1">Wakaf Terkumpul</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">{{ stats.donatur_count }}+</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-1">Donatur</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">{{ stats.penerima_manfaat }}+</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-1">Penerima Manfaat</p>
                </div>
            </div>
        </section>

        <!-- Program Wakaf -->
        <section class="max-w-7xl mx-auto px-12 py-8">
            <div class="flex justify-between items-end mb-6">
                <h2 class="text-2xl font-black text-gray-900">Program Wakaf</h2>
                <Link href="/program" class="text-[10px] font-bold text-primary hover:underline">Lihat Program Lainnya</Link>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- If API loading, show dummy -->
                <ProgramCard v-for="(p, i) in filteredPrograms" :key="i" :program="p" />
            </div>
        </section>

        <!-- Alur Wakaf -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-12 overflow-hidden">
            <div class="border border-gray-200 rounded-xl p-6 lg:p-10 bg-white relative">
                <h2 class="text-2xl font-black text-gray-900 text-center mb-12">Alur Wakaf</h2>
                
                <div class="flex flex-col md:flex-row items-center md:items-start justify-between relative px-2 md:px-0 space-y-8 md:space-y-0 w-full">
                    <!-- Lines connecting dots -->
                    <div class="hidden md:block absolute top-8 left-16 right-16 h-[1px] bg-gray-200 -z-10"></div>
                    <div class="md:hidden absolute top-0 bottom-0 left-[50%] -translate-x-[50%] w-[1px] bg-gray-200 -z-10"></div>

                    
                    <div class="flex flex-col items-center bg-white px-1 md:px-2 flex-1">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-green-50 rounded-full flex items-center justify-center mb-3 shadow-md">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#143E2C] mb-1 text-center">Pilih Program</h4>
                        <p class="text-[11px] lg:text-xs font-semibold text-gray-600 text-center leading-relaxed">Pilih program wakaf yang sesuai niat Anda.</p>
                    </div>

                    <div class="flex items-center text-primary pt-3 md:pt-6 rotate-90 md:rotate-0"><svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></div>
                    
                    <div class="flex flex-col items-center bg-white px-1 md:px-2 flex-1">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-green-50 rounded-full flex items-center justify-center mb-3 shadow-md">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#143E2C] text-center leading-tight mb-1">Berniat & Nominal</h4>
                        <p class="text-[11px] lg:text-xs font-semibold text-gray-600 text-center leading-relaxed">Pilih nominal wakaf yang sesuai niat Anda.</p>
                    </div>

                    <div class="flex items-center text-primary pt-3 md:pt-6 rotate-90 md:rotate-0"><svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></div>
                    
                    <div class="flex flex-col items-center bg-white px-1 md:px-2 flex-1">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-green-50 rounded-full flex items-center justify-center mb-3 shadow-md">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#143E2C] mb-1 text-center">Bayar</h4>
                        <p class="text-[11px] lg:text-xs font-semibold text-gray-600 text-center leading-relaxed">Scan QRIS atau transfer untuk pembayaran mudah.</p>
                    </div>

                    <div class="flex items-center text-primary pt-3 md:pt-6 rotate-90 md:rotate-0"><svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></div>

                    <div class="flex flex-col items-center bg-white px-1 md:px-2 flex-1">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-green-50 rounded-full flex items-center justify-center mb-3 shadow-md">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#143E2C] mb-1 text-center">Verifikasi</h4>
                        <p class="text-[11px] lg:text-xs font-semibold text-gray-600 text-center leading-relaxed">Sistem memverifikasi donasi/wakaf Anda.</p>
                    </div>

                    <div class="flex items-center text-primary pt-3 md:pt-6 rotate-90 md:rotate-0"><svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></div>

                    <div class="flex flex-col items-center bg-white px-1 md:px-2 flex-1">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-green-50 rounded-full flex items-center justify-center mb-3 shadow-md">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#143E2C] mb-1 text-center">Penyaluran</h4>
                        <p class="text-[11px] lg:text-xs font-semibold text-gray-600 text-center leading-relaxed">Wakaf disalurkan dan dibagikan.</p>
                    </div>
                </div>
            </div>
        </section>

    </WakifLayout>
</template>
