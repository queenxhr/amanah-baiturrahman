<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    program: {
        id: number;
        nama_program: string;
        deskripsi: string;
        target_dana: number;
        dana_terkumpul: number;
        due_date: string;
        gambar_placeholder?: string;
    }
}>();

const formatRupiah = (number: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

const persentase = computed(() => {
    if (props.program.target_dana <= 0) return 0;
    const p = (props.program.dana_terkumpul / props.program.target_dana) * 100;
    return Math.min(Math.round(p), 100);
});

const defaultImage = "/dashboard_foto.png";
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
        <div class="h-40 w-full overflow-hidden bg-gray-100">
             <img :src="program.gambar_placeholder || defaultImage" alt="Program Wakaf" class="w-full h-full object-cover">
        </div>
        <div class="p-5 flex flex-col min-h-[240px]">
            <h3 class="font-bold text-[#143E2C] text-base mb-2 leading-snug line-clamp-2">
                {{ program.nama_program }}
            </h3>
            <p class="text-xs text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                {{ program.deskripsi }}
            </p>
            
            <div class="mt-auto">
                <div class="flex justify-between items-end mb-1">
                    <div>
                        <p class="text-[11px] text-gray-500 font-medium mb-0.5">Terkumpul</p>
                        <p class="font-bold text-[#1e5842] text-base">{{ formatRupiah(program.dana_terkumpul) }}</p>
                    </div>
                    <p class="font-bold text-[#1e5842] text-base">{{ persentase }}%</p>
                </div>
                
                <!-- Progress bar -->
                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-3 overflow-hidden">
                    <div class="bg-[#A4C240] h-1.5 rounded-full" :style="`width: ${persentase}%`"></div>
                </div>
                <div class="flex border border-gray-200 rounded divide-x divide-gray-200 mb-4 mt-2">
                    <div class="flex-1 p-2 flex flex-col items-center justify-center min-w-0">
                        <div class="flex items-center gap-1 text-[#638734] mb-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-[10px] text-gray-500 font-medium">Dana Dibutuhkan</p>
                        </div>
                        <p class="text-xs font-bold text-gray-800 truncate w-full text-center">{{ formatRupiah(program.target_dana) }}</p>
                    </div>
                    <div class="flex-1 p-2 flex flex-col items-center justify-center min-w-0">
                    <div class="flex items-center gap-1 text-[#638734] mb-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[10px] text-gray-500 font-medium">Hingga</p>
                        </div>
                        <p class="text-xs font-bold text-gray-800 truncate w-full text-center">
                            {{ 
                            program.due_date && !isNaN(Date.parse(program.due_date.replace(/\.\d+Z$/, 'Z'))) 
                            ? new Date(program.due_date.replace(/\.\d+Z$/, 'Z')).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) 
                            : '-' 
                            }}
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-row items-center gap-2">
                    <a :href="`/program/${program.id}?wakaf=true`" class="flex-1 flex items-center justify-center bg-[#1e5842] hover:bg-[#143E2C] text-white font-bold py-2 px-1 rounded-full text-[11px] lg:text-xs transition-colors shadow-sm text-center whitespace-nowrap">
                        Wakaf Sekarang
                    </a>
                    <a :href="`/program/${program.id}`" class="flex-1 flex items-center justify-center bg-white border border-[#1e5842] text-[#1e5842] hover:bg-gray-50 font-semibold py-2 px-1 rounded-full text-[11px] lg:text-xs transition-colors text-center whitespace-nowrap">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
