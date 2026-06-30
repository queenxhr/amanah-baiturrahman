<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const page = usePage();
const mobileMenuOpen = ref(false);
const profileDropdownOpen = ref(false);

const user = computed(() => (page.props.auth as any)?.user || null);
const userInitial = computed(() => {
    const name = user.value?.nama || 'H';
    return name.substring(0, 1).toUpperCase();
});

const handleLogout = async () => {
    profileDropdownOpen.value = false;
    try {
        const token = localStorage.getItem('wakif_auth_token');
        await axios.post('/api/wakif/logout', {}, {
            headers: token ? { Authorization: `Bearer ${token}` } : {}
        });
    } catch {
        // ignore
    }
    localStorage.removeItem('wakif_auth_token');
    window.location.href = '/';
};

</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
    <!-- Navbar (Sticky) -->
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-12 py-4 shadow-sm transition-all duration-300">
      <!-- Logo -->
      <Link href="/" class="hover:opacity-90">
        <img src="/logo.png" alt="Amanah Baiturrahman Logo" class="h-10 lg:h-12 object-contain" />
      </Link>

      <!-- Mobile Menu Button -->
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-primary focus:outline-none">
        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>

      <!-- Navigation (Desktop) -->
      <div class="hidden lg:flex items-center gap-8 text-[15px] font-bold text-gray-600">
        <Link href="/" 
              :class="{'text-primary border-b-2 border-primary': $page.url === '/', 'hover:text-primary': true}">
          Beranda
        </Link>
        <Link href="/program" 
              :class="{'text-primary border-b-2 border-primary': $page.url.startsWith('/program'), 'hover:text-primary': true}">
          Program Wakaf
        </Link>
        <Link href="/laporan" 
              :class="{'text-primary border-b-2 border-primary': $page.url.startsWith('/laporan'), 'hover:text-primary': true}">
          Laporan
        </Link>
        <Link href="/tentang" 
              :class="{'text-primary border-b-2 border-primary': $page.url.startsWith('/tentang'), 'hover:text-primary': true}">
          Tentang Kami
        </Link>
      </div>

      <!-- Auth Actions (Desktop) -->
      <div class="hidden lg:flex justify-end min-w-[200px] relative">
         <Link v-if="!$page.props.auth?.user" href="/login" class="px-6 py-2 border-2 border-primary text-primary rounded font-bold hover:bg-primary hover:text-white transition-colors duration-200">
           Masuk
         </Link>
         <div v-else class="relative">
            <button @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center gap-2 focus:outline-none group">
               <div class="w-9 h-9 rounded-full bg-primary/10 border border-primary flex items-center justify-center text-primary font-bold text-sm uppercase transition duration-200 group-hover:bg-primary/20">
                  {{ userInitial }}
               </div>
               <span class="font-bold text-xs text-gray-700 max-w-[120px] truncate">{{ user?.nama ?? 'Hamba Allah' }}</span>
               <svg class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200" :class="{'rotate-180': profileDropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div v-if="profileDropdownOpen" class="absolute right-0 mt-3 w-56 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50 transition-all">
                <div class="px-4 py-2 border-b border-gray-100 mb-1">
                    <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama Akun</span>
                    <span class="block text-xs font-bold text-gray-900 truncate">{{ user?.nama ?? 'Hamba Allah' }}</span>
                    <span class="block text-[10px] text-gray-500 truncate mt-0.5">{{ user?.email }}</span>
                </div>
                <Link href="/profil" @click="profileDropdownOpen = false" class="w-full text-left px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-primary transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Edit Profil
                </Link>
                <Link href="/riwayat-transaksi" @click="profileDropdownOpen = false" class="w-full text-left px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-primary transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    Riwayat Transaksi
                </Link>
                <div class="border-t border-gray-100 my-1"></div>
                <button @click="handleLogout" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar
                </button>
            </div>
         </div>
      </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div v-if="mobileMenuOpen" class="lg:hidden fixed inset-0 z-40 bg-white pt-24 px-6 flex flex-col space-y-6 overflow-y-auto">
        <Link href="/" @click="mobileMenuOpen=false" class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">Beranda</Link>
        <Link href="/program" @click="mobileMenuOpen=false" class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">Program Wakaf</Link>
        <Link href="/laporan" @click="mobileMenuOpen=false" class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">Laporan</Link>
        <Link href="/tentang" @click="mobileMenuOpen=false" class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">Tentang Kami</Link>
        
        <div class="pt-6 border-t border-gray-100">
           <Link v-if="!$page.props.auth?.user" href="/login" @click="mobileMenuOpen=false" class="block text-center w-full px-6 py-3 border border-primary text-primary rounded font-bold hover:bg-primary hover:text-white">
             Masuk
           </Link>
           <div v-else class="space-y-4">
               <div class="flex items-center gap-3 px-2">
                   <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary flex items-center justify-center text-primary font-bold text-sm uppercase">
                      {{ userInitial }}
                   </div>
                   <div>
                       <span class="block text-sm font-bold text-gray-800">{{ user?.nama ?? 'Hamba Allah' }}</span>
                       <span class="block text-xs text-gray-400">{{ user?.email }}</span>
                   </div>
               </div>
               <Link href="/profil" @click="mobileMenuOpen=false" class="w-full text-left px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 rounded flex items-center gap-2">
                   Edit Profil
               </Link>
               <Link href="/riwayat-transaksi" @click="mobileMenuOpen=false" class="w-full text-left px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 rounded flex items-center gap-2">
                   Riwayat Transaksi
               </Link>
               <button @click="mobileMenuOpen=false; handleLogout()" class="w-full text-left px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 rounded flex items-center gap-2">
                   Keluar
               </button>
           </div>
        </div>
    </div>

    <!-- Page Content Slot -->
    <main class="flex-1 w-full bg-white">
      <slot />
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 lg:py-10 px-6 lg:px-12">
       <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between">
          <div class="mb-6 md:mb-0">
             <div class="mb-4">
                <img src="/logo.png" alt="Amanah Baiturrahman Logo" class="h-12 object-contain" />
             </div>
             <p class="text-sm text-gray-400 mb-3">&copy; 2026 Wakaf Baiturrahman. All rights reserved.</p>
             <div class="flex items-center gap-4">
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[#E1306C] transition-colors flex items-center gap-1.5 text-xs font-semibold">
                   <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                   </svg>
                   <span>Instagram</span>
                </a>
                <a href="https://wa.me/628123456789" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[#25D366] transition-colors flex items-center gap-1.5 text-xs font-semibold">
                   <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.197 1.451 4.811 1.452 5.485 0 9.948-4.462 9.95-9.95.002-2.66-1.033-5.159-2.909-7.038C16.623 1.74 14.126.705 11.47.705c-5.49 0-9.953 4.463-9.955 9.953 0 1.708.469 3.376 1.358 4.831L1.87 20.89l5.632-1.478c1.554.847 3.125 1.286 4.717 1.286l-.001-.001zM17.47 14.397c-.3-.149-1.777-.877-2.031-.969-.253-.093-.438-.14-.622.14-.184.277-.714.877-.875 1.062-.162.186-.323.208-.622.059-.3-.149-1.264-.466-2.408-1.485-.89-.794-1.49-1.773-1.665-2.07-.175-.3-.019-.461.13-.61.135-.133.3-.349.45-.523.149-.174.199-.299.299-.498.1-.2.05-.375-.025-.524-.075-.15-.622-1.5-.852-2.053-.224-.539-.469-.465-.644-.474-.166-.008-.356-.01-.546-.01-.19 0-.5.07-.762.356-.262.287-1 .977-1 2.385s1.025 2.766 1.168 2.956c.143.19 2.017 3.08 4.886 4.319.682.295 1.214.471 1.629.603.686.218 1.312.187 1.806.114.551-.082 1.777-.726 2.027-1.429.25-.703.25-1.306.175-1.429-.076-.123-.257-.197-.557-.346z"/>
                   </svg>
                   <span>WhatsApp</span>
                </a>
             </div>
          </div>
          <div class="max-w-xs">
             <h4 class="font-bold text-gray-800 mb-2">Alamat</h4>
             <p class="text-sm text-gray-500 leading-relaxed">
               Jl. Gedong Lima No.30, Kertajaya, Kec. Padalarang, Kabupaten Bandung Barat, Jawa Barat 40553
             </p>
          </div>
       </div>
    </footer>
  </div>
</template>
