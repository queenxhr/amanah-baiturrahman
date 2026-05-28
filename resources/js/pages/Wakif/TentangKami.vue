<script setup lang="ts">
import WakifLayout from '@/Layouts/WakifLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Tentang Kami" />

    <WakifLayout>
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-[#1e5842] to-[#638734] text-white py-20 px-6 lg:px-12 overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="relative z-10 max-w-4xl mx-auto text-center">
                <span class="text-xs font-bold uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full">Profil Lembaga</span>
                <h1 class="text-4xl md:text-5xl font-black mt-4 mb-6 leading-tight tracking-tight">
                    Tentang Amanah Baiturrahman
                </h1>
                <p class="text-sm md:text-base max-w-2xl mx-auto text-gray-100 font-light leading-relaxed">
                    Mewujudkan kemandirian umat melalui pengelolaan wakaf secara amanah, transparan, dan profesional untuk kemaslahatan masyarakat dan syiar Islam yang berkelanjutan.
                </p>
            </div>
        </div>

        <!-- Sejarah / History Section -->
        <section class="py-16 px-6 lg:px-12 max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-black text-gray-900 leading-tight">
                        Sejarah Singkat <br>
                        <span class="text-[#638734]">Mahad Baiturrahman</span>
                    </h2>
                    <div class="h-1.5 w-16 bg-[#638734] rounded"></div>
                    <p class="text-sm text-gray-600 leading-relaxed font-light">
                        Berbekal semangat syiar Islam dan kepedulian terhadap pendidikan keagamaan anak-anak yatim serta kaum dhuafa, Mahad Baiturrahman didirikan sebagai pusat pembelajaran Islam terpadu yang ramah bagi semua kalangan.
                    </p>
                    <p class="text-sm text-gray-600 leading-relaxed font-light">
                        Melalui gerakan wakaf tunai yang dihimpun secara kolektif dari para donatur dan wakif, kami berupaya menyediakan fasilitas belajar, asrama, dan kebutuhan operasional harian secara gratis. Hal ini bertujuan agar para santri dapat fokus menuntut ilmu tanpa dibebani biaya sedikit pun.
                    </p>
                </div>
                <div class="relative">
                    <div class="absolute -inset-2 bg-gradient-to-tr from-[#638734] to-[#b1cf49] rounded-2xl opacity-30 blur-lg"></div>
                    <img src="/dashboard_foto.png" alt="Amanah Baiturrahman" class="relative rounded-2xl shadow-xl w-full h-[320px] object-cover">
                </div>
            </div>
        </section>

        <!-- Visi & Misi Section -->
        <section class="bg-gray-50 py-16 px-6 lg:px-12">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-black text-gray-900">Visi & Misi Kami</h2>
                    <p class="text-xs text-gray-500 font-semibold mt-2">Landasan Kami dalam Berkhidmat</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <!-- Visi Card -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition duration-300">
                        <div>
                            <div class="w-12 h-12 bg-primary/10 border border-primary/20 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-[#638734]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Visi</h3>
                            <p class="text-sm text-gray-600 leading-relaxed font-light">
                                Menjadi lembaga pengelolaan wakaf yang amanah, unggul, dan tepercaya demi mewujudkan kemandirian umat, kesejahteraan masyarakat, dan tegaknya nilai-nilai keislaman secara rahmatan lil 'alamin.
                            </p>
                        </div>
                    </div>

                    <!-- Misi Card -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-[#b1cf49]/20 border border-[#b1cf49]/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-[#1e5842]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Misi</h3>
                        <ul class="space-y-3 text-sm text-gray-600 font-light">
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#638734] mt-1 font-bold">&#10003;</span>
                                Mengelola dana wakaf tunai secara transparan, profesional, dan akuntabel sesuai dengan syariat Islam.
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#638734] mt-1 font-bold">&#10003;</span>
                                Menyalurkan hasil pengelolaan wakaf secara tepat guna dan sasaran untuk pembangunan fasilitas ibadah dan pendidikan gratis.
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#638734] mt-1 font-bold">&#10003;</span>
                                Meningkatkan literasi dan kesadaran berwakaf di kalangan masyarakat luas melalui edukasi yang berkelanjutan.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hubungi Kami / Contact Callout -->
        <section class="py-16 px-6 lg:px-12 max-w-4xl mx-auto text-center">
            <div class="bg-gradient-to-tr from-gray-50 to-gray-100 border border-gray-200 rounded-3xl p-8 md:p-12 shadow-sm">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Mari Berkontribusi Bersama Kami</h3>
                <p class="text-sm text-gray-500 max-w-lg mx-auto mb-6 font-light">
                    Setiap rupiah wakaf yang Anda berikan adalah investasi abadi yang pahalanya terus mengalir tiada henti. Mari wujudkan mimpi santri untuk belajar gratis.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="/program" class="px-8 py-3 bg-[#638734] hover:bg-[#1e5842] text-white font-bold rounded-full text-xs shadow-md transition duration-200">
                        Lihat Program Wakaf
                    </a>
                </div>
            </div>
        </section>
    </WakifLayout>
</template>
