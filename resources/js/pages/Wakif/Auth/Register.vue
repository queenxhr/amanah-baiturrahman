<script setup lang="ts">
import WakifLayout from '@/Layouts/WakifLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    nama: '',
    email: '',
    no_hp: '',
    password: ''
});

const processing = ref(false);
const errorMsg = ref('');
const successMsg = ref('');
const showPassword = ref(false);

const submit = async () => {
    processing.value = true;
    errorMsg.value = '';
    successMsg.value = '';
    
    try {
        const response = await axios.post('/api/wakif/signup', form.value);
        if (response.status === 201 || response.data.success) {
            successMsg.value = 'Akun berhasil dibuat! Silakan Masuk.';
            form.value = { nama: '', email: '', no_hp: '', password: '' };
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
        }
    } catch (error: any) {
        if (error.response?.data?.message) {
            errorMsg.value = error.response.data.message;
        } else {
            errorMsg.value = 'Gagal melakukan pendaftaran. Silakan periksa data Anda.';
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <Head title="Daftar" />

    <WakifLayout>
        <div class="flex items-center justify-center min-h-screen sm:min-h-[70vh] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 sm:bg-white">
            <div class="max-w-md w-full space-y-6 bg-white border-0 sm:border border-gray-200 rounded-[20px] p-6 sm:p-8 shadow-none sm:shadow-sm">
                <div>
                    <h2 class="text-left text-2xl font-bold tracking-tight text-primary">
                        Daftar akun Amanah
                    </h2>
                </div>

                <form class="mt-4 space-y-4" @submit.prevent="submit">
                    <div v-if="errorMsg" class="p-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                        {{ errorMsg }}
                    </div>
                    <div v-if="successMsg" class="p-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded">
                        {{ successMsg }}
                    </div>

                    <div>
                        <label for="nama" class="block text-xs font-semibold text-primary">Nama Lengkap</label>
                        <div class="mt-1">
                            <input id="nama" name="nama" type="text" autocomplete="name" required v-model="form.nama"
                                class="appearance-none block w-full px-4 py-3 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nama lengkap Anda" />
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-primary">Email</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required v-model="form.email"
                                class="appearance-none block w-full px-4 py-3 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                                placeholder="Masukkan email Anda. Cth: budi@email.com" />
                             <p class="mt-1 text-[10px] text-gray-400 font-semibold">Gunakan alamat email aktif Anda</p>
                        </div>
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-primary">No Handphone</label>
                        <div class="mt-1">
                            <input id="no_hp" name="no_hp" type="text" autocomplete="tel" required v-model="form.no_hp"
                                class="appearance-none block w-full px-4 py-3 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                                placeholder="Masukkan no handphone Anda. Cth: 628123456789" />
                             <p class="mt-1 text-[10px] text-gray-400 font-semibold">Gunakan no handphone aktif Anda</p>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-primary">Password</label>
                        <div class="mt-1 relative">
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" required v-model="form.password"
                                class="appearance-none block w-full px-4 py-3 pr-10 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                                placeholder="Masukkan password Anda" />
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" @click="showPassword = !showPassword">
                                <svg v-if="showPassword" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-[10px] text-gray-400 font-semibold">Gunakan minimal 8 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="processing"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-primary hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors">
                            <span v-if="processing">Mendaftar...</span>
                            <span v-else>Daftar</span>
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-[11px] text-gray-500 font-semibold">
                            Sudah punya akun? <Link href="/login" class="font-bold text-gray-800 hover:text-primary">Masuk sekarang</Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </WakifLayout>
</template>
