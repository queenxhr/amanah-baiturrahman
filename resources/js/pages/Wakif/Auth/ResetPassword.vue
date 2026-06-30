<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import WakifLayout from '@/layouts/WakifLayout.vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <WakifLayout>
        <div class="flex items-center justify-center min-h-screen sm:min-h-[70vh] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 sm:bg-white">
            <div class="max-w-md w-full space-y-8 bg-white border-0 sm:border border-gray-200 rounded-[20px] p-6 sm:p-8 shadow-none sm:shadow-sm">
                <div>
                    <h2 class="text-left text-2xl font-bold tracking-tight text-primary">
                        Atur Ulang Password
                    </h2>
                    <p class="mt-2 text-left text-xs font-semibold text-primary">
                        Masukkan password baru Anda di bawah ini.
                    </p>
                </div>

                <form class="mt-6 space-y-5" @submit.prevent="submit">
                    <div v-if="form.errors.email" class="p-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                        {{ form.errors.email }}
                    </div>
                    <div v-if="form.errors.password" class="p-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                        {{ form.errors.password }}
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-primary">Email</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required v-model="form.email" readonly
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 bg-gray-100 text-gray-500 rounded-full text-[13px] placeholder-gray-400 focus:outline-none cursor-not-allowed" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-primary">Password Baru</label>
                        <div class="mt-1 relative">
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" required v-model="form.password"
                                class="appearance-none block w-full px-4 py-3 pr-10 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                                placeholder="Masukkan password baru Anda" />
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
                        <label for="password_confirmation" class="block text-xs font-semibold text-primary">Konfirmasi Password Baru</label>
                        <div class="mt-1 relative">
                            <input id="password_confirmation" name="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" autocomplete="new-password" required v-model="form.password_confirmation"
                                class="appearance-none block w-full px-4 py-3 pr-10 border border-gray-400 rounded-full text-[13px] placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                                placeholder="Konfirmasi password baru Anda" />
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" @click="showConfirmPassword = !showConfirmPassword">
                                <svg v-if="showConfirmPassword" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                        <button type="submit" :disabled="form.processing"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-primary hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors cursor-pointer">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Atur Ulang Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </WakifLayout>
</template>
