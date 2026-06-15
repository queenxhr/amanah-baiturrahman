<script setup lang="ts">
import WakifLayout from '@/Layouts/WakifLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Lupa Password" />

    <WakifLayout>
        <div class="flex items-center justify-center min-h-screen sm:min-h-[70vh] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 sm:bg-white">
            <div class="max-w-md w-full space-y-8 bg-white border-0 sm:border border-gray-200 rounded-[20px] p-6 sm:p-8 shadow-none sm:shadow-sm">
                <div>
                    <h2 class="text-left text-2xl font-bold tracking-tight text-primary">
                        Lupa Password
                    </h2>
                    <p class="mt-2 text-left text-xs font-semibold text-primary">
                        Masukkan email Anda untuk menerima link reset password.
                    </p>
                </div>

                <form class="mt-6 space-y-5" @submit.prevent="submit">
                    <div v-if="status" class="p-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded">
                        {{ status }}
                    </div>

                    <div v-if="form.errors.email" class="p-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                        {{ form.errors.email }}
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
                        <button type="submit" :disabled="form.processing"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-primary hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors cursor-pointer">
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Link Reset Password</span>
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <p class="text-[11px] text-gray-500 font-semibold">
                            Kembali ke <Link href="/login" class="font-bold text-blue-500 hover:text-blue-600">Halaman Masuk</Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </WakifLayout>
</template>
