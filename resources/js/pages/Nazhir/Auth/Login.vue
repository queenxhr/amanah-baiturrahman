<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const form = ref({
    email: '',
    password: ''
});

const processing = ref(false);
const errorMsg = ref('');
const showPassword = ref(false);

const submit = async () => {
    processing.value = true;
    errorMsg.value = '';
    
    try {
        const response = await axios.post('/api/nazhir/login', form.value);

        if (response.data.success) {
            localStorage.setItem('nazhir_auth_token', response.data.data.token);
            window.location.href = '/dashboard';
        }
    } catch (error: any) {
        if (error.response?.data?.message) {
            errorMsg.value = error.response.data.message;
        } else {
            errorMsg.value = 'Gagal melakukan login. Periksa koneksi Anda.';
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <Head title="Masuk - Nazhir" />

    <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-md w-full space-y-8 bg-white border border-gray-200 rounded-[20px] p-6 sm:p-8 shadow-sm">
            <div>
                <span class="inline-block px-3 py-1 text-[11px] font-bold tracking-wider text-primary uppercase bg-emerald-50 rounded-full mb-3">Portal Nazhir</span>
                <h2 class="text-left text-2xl font-bold tracking-tight text-primary">
                    Amanah Baiturrahman
                </h2>
                <p class="mt-2 text-left text-xs font-semibold text-primary">
                    Masuk ke panel manajemen program dan wakif.
                </p>
            </div>

            <form class="mt-6 space-y-5" @submit.prevent="submit">
                <div v-if="errorMsg" class="p-3 text-sm text-red-650 bg-red-50 border border-red-200 rounded mb-4">
                    {{ errorMsg }}
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-primary">Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required v-model="form.email"
                            class="appearance-none block w-full px-4 py-3 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                            placeholder="Masukkan email Anda" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-primary">Password</label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required v-model="form.password"
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
                </div>

                <div>
                    <button type="submit" :disabled="processing"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-primary hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors cursor-pointer">
                        <span v-if="processing">Memproses...</span>
                        <span v-else>Masuk sebagai Nazhir</span>
                    </button>
                </div>

                <div class="text-center mt-6">
                    <p class="text-[11px] text-gray-500 font-semibold">
                        Belum mengajukan akun Nazhir? <Link href="/nazhir/register" class="font-bold text-blue-500 hover:text-blue-600">Daftar di sini</Link>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
