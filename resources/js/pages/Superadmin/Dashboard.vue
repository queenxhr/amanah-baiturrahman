<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SuperadminLayout from '@/layouts/SuperadminLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

const users = ref<any[]>([]);
const programs = ref<any[]>([]);
const pencairans = ref<any[]>([]);

const loadingUsers = ref(true);
const loadingPrograms = ref(true);
const loadingPencairans = ref(true);

const processingId = ref<any>(null);

// Get headers helper
const getHeaders = () => {
    const token = localStorage.getItem('superadmin_auth_token');
    return token ? { Authorization: `Bearer ${token}` } : {};
};

const fetchData = async () => {
    fetchUsers();
    fetchPrograms();
    fetchPencairans();
};

const fetchUsers = async () => {
    loadingUsers.value = true;
    try {
        const response = await axios.get('/api/superadmin/users', { headers: getHeaders() });
        if (response.data.success) {
            users.value = response.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch users:', e);
    } finally {
        loadingUsers.value = false;
    }
};

const fetchPrograms = async () => {
    loadingPrograms.value = true;
    try {
        const response = await axios.get('/api/superadmin/programs', { headers: getHeaders() });
        if (response.data.success) {
            programs.value = response.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch programs:', e);
    } finally {
        loadingPrograms.value = false;
    }
};

const fetchPencairans = async () => {
    loadingPencairans.value = true;
    try {
        const response = await axios.get('/api/superadmin/pencairan', { headers: getHeaders() });
        if (response.data.success) {
            pencairans.value = response.data.data;
        }
    } catch (e) {
        console.error('Failed to fetch disbursements:', e);
    } finally {
        loadingPencairans.value = false;
    }
};

// Derived state
const pendingNazhirs = computed(() => {
    return users.value.filter(u => u.status === 'pending' && u.id_role === 1);
});

const pendingPrograms = computed(() => {
    return programs.value.filter(p => p.status_program === 2);
});

const pendingPencairans = computed(() => {
    return pencairans.value.filter(p => p.status_pencairan === 0);
});

const stats = computed(() => {
    return {
        totalUsers: users.value.length,
        totalNazhirs: users.value.filter(u => u.id_role === 1).length,
        totalWakifs: users.value.filter(u => u.id_role === 2).length,
        totalPrograms: programs.value.length,
        pendingProgramsCount: pendingPrograms.value.length,
        pendingNazhirsCount: pendingNazhirs.value.length,
        pendingPencairansCount: pendingPencairans.value.length,
    };
});

// Approvals & Quick actions
const approveNazhir = async (id: number) => {
    if (!(await showConfirm('Setujui pendaftaran akun Nazhir ini?'))) return;
    processingId.value = 'user-' + id;
    try {
        const res = await axios.put(`/api/superadmin/users/${id}/approve`, {}, { headers: getHeaders() });
        if (res.data.success) {
            await showSuccess(res.data.message);
            fetchUsers();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menyetujui akun.');
    } finally {
        processingId.value = null;
    }
};

const handleProgramAction = async (id: number, status: 'approve' | 'reject') => {
    const text = status === 'approve' ? 'Setujui dan tayangkan program ini?' : 'Tolak pengajuan program ini?';
    if (!(await showConfirm(text))) return;
    processingId.value = 'prog-' + id;
    try {
        const endpoint = `/api/superadmin/programs/${id}/${status}`;
        const res = await axios.put(endpoint, {}, { headers: getHeaders() });
        if (res.data.success) {
            await showSuccess(res.data.message);
            fetchPrograms();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal memproses program.');
    } finally {
        processingId.value = null;
    }
};

const handlePencairanAction = async (id: number, status: 'approve' | 'reject') => {
    const text = status === 'approve' ? 'Setujui pengajuan pencairan dana ini?' : 'Tolak pengajuan pencairan dana ini?';
    if (!(await showConfirm(text))) return;
    processingId.value = 'pencairan-' + id;
    try {
        const endpoint = `/api/superadmin/pencairan/${id}/${status}`;
        const res = await axios.put(endpoint, {}, { headers: getHeaders() });
        if (res.data.success) {
            await showSuccess(res.data.message);
            fetchPencairans();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal memproses pencairan.');
    } finally {
        processingId.value = null;
    }
};

const formatRupiah = (num: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
};

onMounted(() => {
    fetchData();
});
</script>

<template>
    <Head title="Dashboard - Superadmin" />

    <SuperadminLayout>
        <div class="space-y-8">
            <!-- Welcoming Banner -->
            <div class="relative bg-gradient-to-r from-[#143E2C] to-[#1e5842] rounded-[24px] p-6 md:p-8 overflow-hidden shadow-lg">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#b1cf49]/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 space-y-2">
                    <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-wider text-[#b1cf49] uppercase bg-white/10 border border-white/15 rounded-full">
                        Superadmin Control Room
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-white">Selamat Datang Kembali</h1>
                    <p class="text-xs text-gray-200 max-w-xl leading-relaxed">
                        Gunakan panel kontrol ini untuk mengawasi seluruh sistem, menyetujui akun Nazhir, menerbitkan program wakaf, serta menyetujui pencairan dana pembangunan.
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <!-- Total Users -->
                <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Pengguna</span>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900">{{ stats.totalUsers }}</span>
                        <div class="text-[10px] text-gray-500 font-semibold">
                            {{ stats.totalNazhirs }} Nazhir | {{ stats.totalWakifs }} Wakif
                        </div>
                    </div>
                </div>

                <!-- Pending Nazhirs -->
                <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nazhir Pending</span>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-2xl font-black text-amber-500">{{ stats.pendingNazhirsCount }}</span>
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-200">
                            Verifikasi
                        </span>
                    </div>
                </div>

                <!-- Pending Programs -->
                <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Program Pending</span>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-2xl font-black text-[#143E2C]">{{ stats.pendingProgramsCount }}</span>
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-green-50 text-[#143E2C] border border-green-200">
                            Reviu
                        </span>
                    </div>
                </div>

                <!-- Pending Disbursements -->
                <div class="bg-white border border-gray-150 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pencairan Pending</span>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-2xl font-black text-blue-600">{{ stats.pendingPencairansCount }}</span>
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-blue-50 text-blue-600 border border-blue-200">
                            Persetujuan
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pending Actions Center -->
            <div class="space-y-6">
                <h2 class="text-lg font-extrabold text-gray-900">Pusat Persetujuan Tertunda</h2>

                <!-- 1. Pending Nazhirs List -->
                <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            Verifikasi Akun Nazhir Baru ({{ pendingNazhirs.length }})
                        </h3>
                    </div>

                    <div v-if="loadingUsers" class="flex justify-center py-6">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#143E2C]"></div>
                    </div>
                    <div v-else-if="pendingNazhirs.length === 0" class="text-gray-400 text-xs py-2">
                        Tidak ada pendaftaran Nazhir baru yang menunggu persetujuan.
                    </div>
                    <div v-else class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto pr-2">
                        <div v-for="user in pendingNazhirs" :key="user.id_user" class="py-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-xs text-gray-900">{{ user.nama }}</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ user.email }} | {{ user.no_hp }}</p>
                            </div>
                            <div>
                                <button 
                                    @click="approveNazhir(user.id_user)"
                                    :disabled="processingId === 'user-' + user.id_user"
                                    class="px-3 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-black rounded-lg text-[10px] transition-all"
                                >
                                    Setujui
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Pending Programs List -->
                <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#143E2C]"></span>
                            Verifikasi Program Wakaf Baru ({{ pendingPrograms.length }})
                        </h3>
                    </div>

                    <div v-if="loadingPrograms" class="flex justify-center py-6">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#143E2C]"></div>
                    </div>
                    <div v-else-if="pendingPrograms.length === 0" class="text-gray-400 text-xs py-2">
                        Tidak ada program baru yang menunggu verifikasi.
                    </div>
                    <div v-else class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto pr-2">
                        <div v-for="prog in pendingPrograms" :key="prog.id_program" class="py-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-xs text-gray-900 truncate max-w-md">{{ prog.nama_program }}</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Target Dana: <span class="font-bold text-gray-900">{{ formatRupiah(prog.target_dana) }}</span> 
                                    | Batas Waktu: <span class="text-gray-500">{{ prog.due_date || 'Tidak Ada' }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button 
                                    @click="handleProgramAction(prog.id_program, 'approve')"
                                    :disabled="processingId === 'prog-' + prog.id_program"
                                    class="px-3 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-black rounded-lg text-[10px] transition-all"
                                >
                                    Setujui
                                </button>
                                <button 
                                    @click="handleProgramAction(prog.id_program, 'reject')"
                                    :disabled="processingId === 'prog-' + prog.id_program"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-red-600 disabled:opacity-50 font-black rounded-lg text-[10px] transition-all"
                                >
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Pending Disbursements List -->
                <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Persetujuan Pencairan Dana ({{ pendingPencairans.length }})
                        </h3>
                    </div>

                    <div v-if="loadingPencairans" class="flex justify-center py-6">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#143E2C]"></div>
                    </div>
                    <div v-else-if="pendingPencairans.length === 0" class="text-gray-400 text-xs py-2">
                        Tidak ada pengajuan pencairan dana baru yang tertunda.
                    </div>
                    <div v-else class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto pr-2">
                        <div v-for="p in pendingPencairans" :key="p.id_pencairan" class="py-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-xs text-gray-900 truncate max-w-md">{{ p.nama_program }}</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Diajukan Oleh: <span class="font-semibold text-gray-900">{{ p.nama_nazhir }}</span> 
                                    | Jumlah Dana: <span class="font-bold text-gray-900">{{ formatRupiah(p.jumlah_dana) }}</span>
                                </p>
                                <p class="text-[10px] text-gray-400 mt-1 italic leading-relaxed">
                                    Ket: "{{ p.keterangan || '-' }}"
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button 
                                    @click="handlePencairanAction(p.id_pencairan, 'approve')"
                                    :disabled="processingId === 'pencairan-' + p.id_pencairan"
                                    class="px-3 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-black rounded-lg text-[10px] transition-all"
                                >
                                    Setujui
                                </button>
                                <button 
                                    @click="handlePencairanAction(p.id_pencairan, 'reject')"
                                    :disabled="processingId === 'pencairan-' + p.id_pencairan"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-red-600 disabled:opacity-50 font-black rounded-lg text-[10px] transition-all"
                                >
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperadminLayout>
</template>
