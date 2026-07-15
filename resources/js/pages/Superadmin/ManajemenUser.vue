<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import SuperadminLayout from '@/layouts/SuperadminLayout.vue';
import { showConfirm, showSuccess, showError } from '@/lib/alert';

const users = ref<any[]>([]);
const search = ref('');
const role = ref('');
const status = ref('');
const limit = ref(10);
const page = ref(1);
const pagination = ref<any>({});
const loading = ref(true);
const processingId = ref<number | null>(null);

const fetchUsers = async () => {
    loading.value = true;

    try {
        const params: any = {
            limit: limit.value,
            page: page.value
        };

        if (search.value) {
            params.search = search.value;
        }

        if (role.value) {
            params.role = role.value;
        }

        if (status.value) {
            params.status = status.value;
        }

        const response = await axios.get('/api/superadmin/users', { params });

        if (response.data.success) {
            users.value = response.data.data.data || [];
            pagination.value = {
                current_page: response.data.data.current_page,
                last_page: response.data.data.last_page,
                total: response.data.data.total,
                from: response.data.data.from,
                to: response.data.data.to
            };
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const onFilterChange = () => {
    page.value = 1;
    fetchUsers();
};

const handlePrevPage = () => {
    if (page.value > 1) {
        page.value--;
        fetchUsers();
    }
};

const handleNextPage = () => {
    if (page.value < pagination.value.last_page) {
        page.value++;
        fetchUsers();
    }
};

onMounted(() => {
    // Check url search params if any
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('status')) {
        status.value = urlParams.get('status') || '';
    }

    fetchUsers();
});

const approveUser = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menyetujui pendaftaran Nazhir ini?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/users/${id}/approve`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchUsers();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menyetujui pendaftaran.');
    } finally {
        processingId.value = null;
    }
};

const rejectUser = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menolak pendaftaran Nazhir ini?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/users/${id}/reject`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchUsers();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menolak pendaftaran.');
    } finally {
        processingId.value = null;
    }
};

const blockUser = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin memblokir akun ini?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/users/${id}/block`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchUsers();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal memblokir akun.');
    } finally {
        processingId.value = null;
    }
};

const unblockUser = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin membuka blokir akun ini?'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.put(`/api/superadmin/users/${id}/unblock`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchUsers();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal membuka blokir.');
    } finally {
        processingId.value = null;
    }
};

const deleteUser = async (id: number) => {
    if (!(await showConfirm('Apakah Anda yakin ingin menghapus akun ini secara permanen? Tindakan ini tidak dapat dibatalkan.'))) {
return;
}

    processingId.value = id;

    try {
        const response = await axios.delete(`/api/superadmin/users/${id}`);

        if (response.data.success) {
            await showSuccess(response.data.message);
            fetchUsers();
        }
    } catch (e: any) {
        await showError(e.response?.data?.message || 'Gagal menghapus akun.');
    } finally {
        processingId.value = null;
    }
};
</script>

<template>
    <Head title="Kelola Pengguna - Superadmin" />

    <SuperadminLayout>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Kelola Pengguna</h1>
                    <p class="text-xs text-gray-500 mt-1">
                        Verifikasi identitas Nazhir baru, setujui pendaftaran akun, dan blokir/unblokir akun Nazhir &amp; Wakif.
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-150 rounded-2xl p-5 grid grid-cols-1 sm:grid-cols-5 gap-4 shadow-sm">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Cari Pengguna</label>
                    <input type="text" v-model="search" @input="onFilterChange" placeholder="Nama, email..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filter Peran</label>
                    <select v-model="role" @change="onFilterChange"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                        <option value="">Semua Peran</option>
                        <option value="1">Nazhir</option>
                        <option value="2">Wakif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filter Status</label>
                    <select v-model="status" @change="onFilterChange"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Persetujuan</option>
                        <option value="active">Aktif</option>
                        <option value="blocked">Diblokir</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Limit</label>
                    <select v-model="limit" @change="onFilterChange"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#143E2C] focus:border-[#143E2C]">
                        <option :value="10">10 Data</option>
                        <option :value="25">25 Data</option>
                        <option :value="50">50 Data</option>
                    </select>
                </div>
                <div class="flex items-end justify-end">
                    <button @click="onFilterChange" class="px-4 py-2.5 bg-[#143E2C] hover:bg-[#1e5842] text-white rounded-xl text-xs font-bold transition-all w-full sm:w-auto">
                        Refresh Data
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white border border-gray-150 rounded-2xl overflow-hidden shadow-sm">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#143E2C]"></div>
                </div>

                <div v-else-if="users.length === 0" class="p-8 text-center text-gray-400 text-xs font-semibold">
                    Tidak ada pengguna ditemukan.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Alamat Email</th>
                                <th class="px-6 py-4">Nomor HP</th>
                                <th class="px-6 py-4">Peran</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr v-for="user in users" :key="user.id_user" class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ user.nama }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ user.email }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ user.no_hp }}</td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border',
                                        user.id_role === 1 ? 'bg-green-50 text-[#143E2C] border-green-200' : 'bg-blue-50 text-blue-600 border-blue-200'
                                    ]">
                                        {{ user.role_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border',
                                        user.status === 'active' ? 'bg-green-50 text-green-600 border-green-200' : 
                                        user.status === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse' : 
                                        user.status === 'rejected' ? 'bg-red-50 text-red-600 border-red-200' : 
                                        'bg-gray-50 text-gray-600 border-gray-200'
                                    ]">
                                        {{ user.status === 'rejected' ? 'ditolak' : user.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <!-- Approve/Reject Nazhir registration -->
                                    <button v-if="user.status === 'pending' && user.id_role === 1" 
                                        @click="approveUser(user.id_user)"
                                        :disabled="processingId !== null"
                                        class="px-2.5 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-bold rounded-lg text-[10px] transition-all">
                                        Setujui
                                    </button>
                                    <button v-if="user.status === 'pending' && user.id_role === 1" 
                                        @click="rejectUser(user.id_user)"
                                        :disabled="processingId !== null"
                                        class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-600 text-amber-600 hover:text-white border border-amber-200 hover:border-amber-600 disabled:opacity-50 font-bold rounded-lg text-[10px] transition-all">
                                        Tolak
                                    </button>

                                    <!-- Block / Unblock -->
                                    <button v-if="user.status === 'active'"
                                        @click="blockUser(user.id_user)"
                                        :disabled="processingId !== null"
                                        class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-red-600 disabled:opacity-50 font-bold rounded-lg text-[10px] transition-all">
                                        Blokir
                                    </button>
                                    <button v-if="user.status === 'blocked'"
                                        @click="unblockUser(user.id_user)"
                                        :disabled="processingId !== null"
                                        class="px-2.5 py-1.5 bg-[#143E2C] hover:bg-[#1e5842] disabled:opacity-50 text-white font-bold rounded-lg text-[10px] transition-all">
                                        Buka Blokir
                                    </button>

                                    <!-- Hapus Akun -->
                                    <button @click="deleteUser(user.id_user)"
                                         :disabled="processingId !== null"
                                         class="px-2.5 py-1.5 bg-gray-100 hover:bg-red-700 text-gray-700 hover:text-white border border-gray-300 hover:border-red-700 disabled:opacity-50 font-bold rounded-lg text-[10px] transition-all">
                                         Hapus
                                     </button>
                                 </td>
                             </tr>
                         </tbody>
                     </table>
                 </div>

                 <!-- Pagination Bar -->
                 <div class="bg-gray-50 border-t border-gray-150 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-gray-500">
                     <span>
                         Menampilkan {{ pagination.from || 0 }} - {{ pagination.to || 0 }} dari {{ pagination.total || 0 }} data
                     </span>
                     
                     <div class="flex items-center gap-2">
                         <button 
                             @click="handlePrevPage" 
                             :disabled="page === 1"
                             class="px-3 py-1.5 rounded-lg border border-gray-250 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white font-black"
                         >
                             &larr;
                         </button>
                         <span class="px-2">Halaman {{ page }} dari {{ pagination.last_page || 1 }}</span>
                         <button 
                             @click="handleNextPage" 
                             :disabled="page >= pagination.last_page"
                             class="px-3 py-1.5 rounded-lg border border-gray-250 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white font-black"
                         >
                             &rarr;
                         </button>
                     </div>
                 </div>
             </div>
        </div>
    </SuperadminLayout>
</template>
