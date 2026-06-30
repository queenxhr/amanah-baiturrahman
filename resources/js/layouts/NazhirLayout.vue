<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';

const page = usePage();
const isSidebarOpen = ref(false);

const user = computed(() => (page.props.auth as any)?.user || null);
const userInitial = computed(() => {
    const name = user.value?.nama || 'N';

    return name.substring(0, 1).toUpperCase();
});

onMounted(() => {
    isSidebarOpen.value = window.innerWidth >= 768;
});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebarOnMobile = () => {
    if (typeof window !== 'undefined' && window.innerWidth < 768) {
        isSidebarOpen.value = false;
    }
};

const handleLogout = async () => {
    try {
        const token = localStorage.getItem('nazhir_auth_token');
        await axios.post('/api/nazhir/logout', {}, {
            headers: token ? { Authorization: `Bearer ${token}` } : {}
        });
    } catch {
        // ignore
    }

    localStorage.removeItem('nazhir_auth_token');
    window.location.href = '/nazhir/login';
};

</script>

<template>
  <div class="min-h-screen bg-gray-100 flex font-sans overflow-x-hidden">
    
    <!-- Sidebar (Desktop and Mobile drawer) -->
    <aside :class="[
      'fixed inset-y-0 left-0 h-screen w-64 bg-[#143E2C] text-white flex flex-col justify-between p-6 transform transition-transform duration-300 shadow-xl z-30 overflow-y-auto',
      isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
      <div class="flex flex-col flex-1">
        <!-- Logo -->
        <div class="mb-8">
          <img src="/logo.png" alt="Amanah Baiturrahman Logo" class="h-10 mb-2 object-contain" />
          <span class="text-[10px] uppercase font-black text-gray-300 tracking-wider">Panel Nazhir</span>
        </div>

        <!-- Profile Box -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 mb-8 flex items-center gap-3">
          <div class="w-11 h-11 rounded-full bg-[#b1cf49]/20 border border-[#b1cf49]/40 flex items-center justify-center text-[#b1cf49] font-black text-base uppercase">
            {{ userInitial }}
          </div>
          <div class="min-w-0 flex-1">
            <h4 class="font-bold text-sm text-white break-words whitespace-normal">{{ user?.nama ?? 'Ust. Ahmad' }}</h4>
            <span class="inline-block bg-[#b1cf49] text-[#143E2C] text-[9px] font-black uppercase px-2 py-0.5 rounded-full mt-0.5">
              Nazhir
            </span>
          </div>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-2 flex-1">
          <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 px-2">Menu Utama</span>
          <Link href="/dashboard" 
                @click="closeSidebarOnMobile"
                :class="[
                  'flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition duration-250',
                  $page.url === '/dashboard' ? 'bg-[#b1cf49] text-[#143E2C]' : 'text-gray-300 hover:bg-white/5 hover:text-white'
                ]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" /></svg>
            Dashboard
          </Link>
          <Link href="/manajemen-user" 
                @click="closeSidebarOnMobile"
                :class="[
                  'flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition duration-250',
                  $page.url === '/manajemen-user' ? 'bg-[#b1cf49] text-[#143E2C]' : 'text-gray-300 hover:bg-white/5 hover:text-white'
                ]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            Manajemen User
          </Link>
          <Link href="/manajemen-program" 
                @click="closeSidebarOnMobile"
                :class="[
                  'flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition duration-250',
                  ($page.url === '/manajemen-program' || $page.url.startsWith('/program') || $page.url.startsWith('/laporan')) ? 'bg-[#b1cf49] text-[#143E2C]' : 'text-gray-300 hover:bg-white/5 hover:text-white'
                ]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            Manajemen Program
          </Link>
          <Link href="/manajemen-pencairan" 
                @click="closeSidebarOnMobile"
                :class="[
                  'flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition duration-250',
                  $page.url === '/manajemen-pencairan' ? 'bg-[#b1cf49] text-[#143E2C]' : 'text-gray-300 hover:bg-white/5 hover:text-white'
                ]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Pencairan Dana
          </Link>
        </nav>
      </div>

      <!-- Logout Button at bottom -->
      <div class="border-t border-white/10 pt-4 mt-6">
        <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-red-200 hover:bg-red-500/10 hover:text-red-400 transition duration-200">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
          Keluar
        </button>
      </div>
    </aside>

    <!-- Overlay when mobile sidebar open -->
    <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="md:hidden fixed inset-0 z-20 bg-black/50 backdrop-blur-xs"></div>

    <!-- Main Content Area -->
    <div class="flex-grow min-w-0 flex flex-col transition-all duration-300" :class="isSidebarOpen ? 'md:pl-64' : 'pl-0'">
      <!-- Top Header Navbar -->
      <header class="sticky top-0 bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 z-20 shadow-sm">
        <div class="flex items-center gap-3">
          <button @click="toggleSidebar" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </button>
          <span class="md:hidden flex items-center">
             <img src="/logo.png" alt="Amanah Baiturrahman Logo" class="h-6 object-contain" />
          </span>
          <span class="hidden md:inline text-sm font-bold text-gray-700">Panel Admin Nazhir</span>
        </div>
        <div class="flex items-center gap-4">
          <!-- Profile in sidebar only -->
        </div>
      </header>

      <!-- Main Content Area -->
      <main class="flex-grow p-6 md:p-10">
        <slot />
      </main>
    </div>

  </div>
</template>
