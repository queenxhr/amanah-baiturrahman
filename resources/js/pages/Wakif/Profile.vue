<script setup lang="ts">
import WakifLayout from '@/Layouts/WakifLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const page = usePage();

// Profile Form State
const profileForm = ref({
    nama: '',
    email: '',
    no_hp: '',
    jenis_kelamin: '',
    tanggal_lahir: '',
    alamat: ''
});

// Password Form State
const passwordForm = ref({
    old_password: '',
    new_password: '',
    confirm_password: ''
});

// UI states
const isLoadingProfile = ref(true);
const isSavingProfile = ref(false);
const isSavingPassword = ref(false);

// Inline Validation Errors
const profileErrors = ref<{ [key: string]: string }>({});
const passwordErrors = ref<{ [key: string]: string }>({});

// Success messages
const profileSuccessMsg = ref('');
const passwordSuccessMsg = ref('');

const getHeaders = () => {
    const token = localStorage.getItem('auth_token');
    return token ? { Authorization: `Bearer ${token}` } : {};
};

// Fetch current user details
const fetchUserProfile = async () => {
    isLoadingProfile.value = true;
    try {
        const response = await axios.get('/api/wakif/user', {
            headers: getHeaders()
        });
        if (response.data && response.data.success) {
            const data = response.data.data;
            profileForm.value = {
                nama: data.nama || '',
                email: data.email || '',
                no_hp: data.no_hp || '',
                jenis_kelamin: data.jenis_kelamin || 'L',
                tanggal_lahir: data.tanggal_lahir ? data.tanggal_lahir.substring(0, 10) : '',
                alamat: data.alamat || ''
            };
        }
    } catch (e: any) {
        console.error('Failed to load profile details:', e);
        profileErrors.value = { global: 'Gagal memuat profil. Silakan muat ulang halaman.' };
    } finally {
        isLoadingProfile.value = false;
    }
};

// Save profile details
const handleSaveProfile = async () => {
    profileErrors.value = {};
    profileSuccessMsg.value = '';
    
    // Front-end validations
    let hasError = false;
    if (!profileForm.value.nama.trim()) {
        profileErrors.value.nama = 'Nama Lengkap wajib diisi.';
        hasError = true;
    }
    if (!profileForm.value.email.trim()) {
        profileErrors.value.email = 'Email wajib diisi.';
        hasError = true;
    }
    if (!profileForm.value.no_hp.trim()) {
        profileErrors.value.no_hp = 'No WhatsApp wajib diisi.';
        hasError = true;
    }

    if (hasError) return;

    isSavingProfile.value = true;
    try {
        const response = await axios.put('/api/wakif/user', profileForm.value, {
            headers: getHeaders()
        });
        if (response.data && response.data.success) {
            profileSuccessMsg.value = 'Profil Anda berhasil diperbarui.';
            // Sync with page props if Inertia manages it
            if (page.props.auth?.user) {
                page.props.auth.user.nama = profileForm.value.nama;
                page.props.auth.user.email = profileForm.value.email;
            }
        }
    } catch (e: any) {
        console.error('Failed to update profile:', e);
        if (e.response?.data?.errors) {
            const serverErrors = e.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
                profileErrors.value[key] = serverErrors[key][0];
            });
        } else {
            profileErrors.value.global = e.response?.data?.message || 'Gagal memperbarui profil. Silakan coba lagi.';
        }
    } finally {
        isSavingProfile.value = false;
    }
};

// Change password
const handleSavePassword = async () => {
    passwordErrors.value = {};
    passwordSuccessMsg.value = '';
    
    // Front-end validation
    let hasError = false;
    if (!passwordForm.value.old_password) {
        passwordErrors.value.old_password = 'Password Lama wajib diisi.';
        hasError = true;
    }
    if (!passwordForm.value.new_password) {
        passwordErrors.value.new_password = 'Password Baru wajib diisi.';
        hasError = true;
    } else if (passwordForm.value.new_password.length < 8) {
        passwordErrors.value.new_password = 'Password Baru minimal 8 karakter.';
        hasError = true;
    }
    if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
        passwordErrors.value.confirm_password = 'Konfirmasi Password Baru tidak sesuai.';
        hasError = true;
    }

    if (hasError) return;

    isSavingPassword.value = true;
    try {
        const response = await axios.post('/api/wakif/ubah-password', {
            old_password: passwordForm.value.old_password,
            new_password: passwordForm.value.new_password
        }, {
            headers: getHeaders()
        });
        if (response.data && response.data.success) {
            passwordSuccessMsg.value = 'Password Anda berhasil diperbarui.';
            passwordForm.value = {
                old_password: '',
                new_password: '',
                confirm_password: ''
            };
        }
    } catch (e: any) {
        console.error('Failed to change password:', e);
        if (e.response?.data?.errors) {
            const serverErrors = e.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
                passwordErrors.value[key] = serverErrors[key][0];
            });
        } else {
            passwordErrors.value.global = e.response?.data?.message || 'Password Lama salah atau terjadi kesalahan.';
        }
    } finally {
        isSavingPassword.value = false;
    }
};

onMounted(() => {
    fetchUserProfile();
});
</script>

<template>
    <Head title="Pengaturan Profil" />

    <WakifLayout>
        <div class="max-w-4xl mx-auto px-6 py-12">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Pengaturan Profil</h1>
                <p class="text-xs text-gray-500 font-semibold mt-1">Kelola data informasi akun dan kata sandi Anda</p>
            </div>

            <!-- Loading Spinner -->
            <div v-if="isLoadingProfile" class="flex justify-center items-center py-20">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#638734]"></div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Left Panel: Avatar & Summary -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm text-center">
                        <div class="w-20 h-20 rounded-full bg-[#638734]/10 border border-[#638734] flex items-center justify-center text-[#638734] font-black text-2xl uppercase mx-auto mb-4">
                            {{ (profileForm.nama || 'H').substring(0, 1) }}
                        </div>
                        <h2 class="text-base font-bold text-gray-900 truncate">{{ profileForm.nama || 'Hamba Allah' }}</h2>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ profileForm.email }}</p>
                        <span class="inline-block px-3 py-1 bg-[#638734]/10 text-[#1e5842] text-[10px] font-black uppercase rounded-full mt-4">
                            Wakif Aktif
                        </span>
                    </div>
                </div>

                <!-- Right Panel: Forms -->
                <div class="md:col-span-2 space-y-8">
                    <!-- Form Profile -->
                    <div class="bg-white border border-gray-150 rounded-2xl p-6 md:p-8 shadow-sm">
                        <h3 class="text-base font-black text-gray-900 mb-6 pb-2 border-b border-gray-100">Informasi Pribadi</h3>

                        <div v-if="profileSuccessMsg" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                            <span class="text-lg">✓</span> {{ profileSuccessMsg }}
                        </div>

                        <div v-if="profileErrors.global" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold">
                            {{ profileErrors.global }}
                        </div>

                        <form @submit.prevent="handleSaveProfile" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                                    <input type="text" v-model="profileForm.nama" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                    <span v-if="profileErrors.nama" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ profileErrors.nama }}</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email *</label>
                                    <input type="email" v-model="profileForm.email" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                    <span v-if="profileErrors.email" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ profileErrors.email }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">No. WhatsApp *</label>
                                    <input type="text" v-model="profileForm.no_hp" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                    <span v-if="profileErrors.no_hp" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ profileErrors.no_hp }}</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                                    <select v-model="profileForm.jenis_kelamin" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    <span v-if="profileErrors.jenis_kelamin" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ profileErrors.jenis_kelamin }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" v-model="profileForm.tanggal_lahir" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                <span v-if="profileErrors.tanggal_lahir" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ profileErrors.tanggal_lahir }}</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea rows="3" v-model="profileForm.alamat" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734] resize-none"></textarea>
                                <span v-if="profileErrors.alamat" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ profileErrors.alamat }}</span>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" :disabled="isSavingProfile" class="px-6 py-2.5 bg-[#638734] hover:bg-[#1e5842] text-white font-bold rounded-lg text-xs transition duration-200 disabled:opacity-50 flex items-center gap-2">
                                    <span v-if="isSavingProfile" class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></span>
                                    {{ isSavingProfile ? 'Menyimpan...' : 'Simpan Profil' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Form Password -->
                    <div class="bg-white border border-gray-150 rounded-2xl p-6 md:p-8 shadow-sm">
                        <h3 class="text-base font-black text-gray-900 mb-6 pb-2 border-b border-gray-100">Ubah Kata Sandi</h3>

                        <div v-if="passwordSuccessMsg" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                            <span class="text-lg">✓</span> {{ passwordSuccessMsg }}
                        </div>

                        <div v-if="passwordErrors.global" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold">
                            {{ passwordErrors.global }}
                        </div>

                        <form @submit.prevent="handleSavePassword" class="space-y-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Kata Sandi Lama *</label>
                                <input type="password" v-model="passwordForm.old_password" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                <span v-if="passwordErrors.old_password" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ passwordErrors.old_password }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Kata Sandi Baru *</label>
                                    <input type="password" v-model="passwordForm.new_password" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                    <span v-if="passwordErrors.new_password" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ passwordErrors.new_password }}</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru *</label>
                                    <input type="password" v-model="passwordForm.confirm_password" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#638734] focus:border-[#638734]">
                                    <span v-if="passwordErrors.confirm_password" class="text-[10px] text-red-600 font-semibold mt-1 block">{{ passwordErrors.confirm_password }}</span>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" :disabled="isSavingPassword" class="px-6 py-2.5 bg-[#1e5842] hover:bg-[#153e2f] text-white font-bold rounded-lg text-xs transition duration-200 disabled:opacity-50 flex items-center gap-2">
                                    <span v-if="isSavingPassword" class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></span>
                                    {{ isSavingPassword ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WakifLayout>
</template>
