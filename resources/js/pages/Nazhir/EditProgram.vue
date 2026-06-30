<script setup lang="ts">
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import RichTextEditor from '@/components/Nazhir/RichTextEditor.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { showSuccess, showError } from '@/lib/alert';

const props = defineProps<{
    id: string | number;
}>();

const form = ref({
    nama_program: '',
    target_dana: '',
    due_date: '',
    status_program: 1,
    deskripsi: ''
});

const thumbnailFile = ref<File | null>(null);
const thumbnailPreview = ref<string | null>(null);
const existingThumbnailUrl = ref<string | null>(null);
const errors = ref<Record<string, string>>({});
const isLoading = ref(true);
const isSubmitting = ref(false);

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        thumbnailFile.value = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const fetchProgramDetails = async () => {
    try {
        const res = await axios.get(`/api/wakif/program-wakaf/${props.id}`);
        if (res.data && res.data.data) {
            const data = res.data.data;
            form.value.nama_program = data.nama_program || '';
            form.value.target_dana = String(data.target_dana || '');
            form.value.status_program = data.status_program !== undefined ? Number(data.status_program) : 1;
            form.value.deskripsi = data.deskripsi || '';
            existingThumbnailUrl.value = data.gambar_thumbnail || null;
            
            // Format due_date to YYYY-MM-DD
            if (data.due_date) {
                const dateObj = new Date(data.due_date);
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                form.value.due_date = `${year}-${month}-${day}`;
            }
        }
    } catch (e) {
        console.error('Failed to load program details:', e);
        await showError('Gagal memuat detail program.');
    } finally {
        isLoading.value = false;
    }
};

const validateForm = () => {
    errors.value = {};
    let isValid = true;

    if (!form.value.nama_program.trim()) {
        errors.value.nama_program = 'Nama program wajib diisi.';
        isValid = false;
    }
    if (!form.value.target_dana || Number(form.value.target_dana) <= 0) {
        errors.value.target_dana = 'Target dana harus diisi dengan angka lebih dari 0.';
        isValid = false;
    }
    if (!form.value.due_date) {
        errors.value.due_date = 'Tenggat waktu program wajib diisi.';
        isValid = false;
    }

    return isValid;
};

const handleSubmit = async () => {
    if (!validateForm()) return;
    isSubmitting.value = true;

    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('nama_program', form.value.nama_program);
        formData.append('target_dana', form.value.target_dana);
        formData.append('due_date', form.value.due_date);
        formData.append('status_program', String(form.value.status_program));
        formData.append('deskripsi', form.value.deskripsi);
        if (thumbnailFile.value) {
            formData.append('gambar_thumbnail', thumbnailFile.value);
        }

        await axios.post(`/api/nazhir/program/${props.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        await showSuccess('Program wakaf berhasil diperbarui!');
        router.visit('/manajemen-program');
    } catch (e: any) {
        console.error('Failed to update program:', e);
        if (e.response && e.response.data && e.response.data.errors) {
            const apiErrors = e.response.data.errors;
            Object.keys(apiErrors).forEach(key => {
                errors.value[key] = apiErrors[key][0];
            });
        } else {
            await showError('Terjadi kesalahan saat memperbarui program.');
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    fetchProgramDetails();
});
</script>

<template>
  <Head title="Edit Program Wakaf" />

  <NazhirLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      
      <!-- Breadcrumb / Header -->
      <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
        <Link href="/manajemen-program" class="hover:text-gray-600">Program</Link>
        <span>/</span>
        <span class="text-gray-700">Edit Program</span>
      </div>

      <div>
        <h1 class="text-2xl font-black text-gray-900 leading-tight">Edit Program Wakaf</h1>
        <p class="text-xs text-gray-500">Perbarui detail program wakaf, target dana, tenggat tanggal, atau unggah gambar thumbnail baru.</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="bg-white border border-gray-150 rounded-2xl p-10 flex justify-center items-center shadow-xs">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#143E2C]"></div>
      </div>

      <!-- Form Card -->
      <form v-else @submit.prevent="handleSubmit" class="bg-white border border-gray-150 rounded-2xl p-6 md:p-8 shadow-xs space-y-5">
        
        <!-- Nama Program -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-gray-750">Nama Program <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            v-model="form.nama_program"
            placeholder="Masukkan nama program wakaf..."
            :class="[
              'w-full bg-gray-50 border rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 transition-all',
              errors.nama_program ? 'border-red-400 focus:ring-red-400' : 'border-gray-250 focus:ring-[#143E2C] focus:border-[#143E2C]'
            ]"
          />
          <span v-if="errors.nama_program" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.nama_program }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Target Dana -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-gray-750">Target Dana (Rupiah) <span class="text-red-500">*</span></label>
            <input 
              type="number" 
              v-model="form.target_dana"
              placeholder="Contoh: 50000000"
              :class="[
                'w-full bg-gray-50 border rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 transition-all',
                errors.target_dana ? 'border-red-400 focus:ring-red-400' : 'border-gray-250 focus:ring-[#143E2C] focus:border-[#143E2C]'
              ]"
            />
            <span v-if="errors.target_dana" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.target_dana }}</span>
          </div>

          <!-- Tenggat Waktu (Due Date) -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-gray-750">Tenggat Tanggal <span class="text-red-500">*</span></label>
            <input 
              type="date" 
              v-model="form.due_date"
              :class="[
                'w-full bg-gray-50 border rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 transition-all cursor-pointer',
                errors.due_date ? 'border-red-400 focus:ring-red-400' : 'border-gray-250 focus:ring-[#143E2C] focus:border-[#143E2C]'
              ]"
            />
            <span v-if="errors.due_date" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.due_date }}</span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Status Program -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-gray-750">Status Program</label>
            <select 
              v-model="form.status_program"
              class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
            >
              <option :value="1">Dibuka</option>
              <option :value="0">Ditutup</option>
            </select>
          </div>

          <!-- Gambar Thumbnail -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-gray-750">Ubah Gambar Thumbnail</label>
            <input 
              type="file" 
              accept="image/*"
              @change="handleFileChange"
              class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
            />
            <span v-if="errors.gambar_thumbnail" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.gambar_thumbnail }}</span>
          </div>
        </div>

        <!-- Image Previews -->
        <div class="flex flex-wrap gap-4">
          <!-- Existing Image preview -->
          <div v-if="existingThumbnailUrl && !thumbnailPreview" class="border border-gray-200 rounded-xl p-3 bg-gray-50 flex flex-col items-center gap-2 max-w-xs">
            <span class="text-[10px] font-bold text-gray-400 uppercase">Gambar Saat Ini</span>
            <img :src="existingThumbnailUrl" class="w-40 h-28 object-cover rounded-lg" alt="Current Thumbnail" />
          </div>

          <!-- New Image preview -->
          <div v-if="thumbnailPreview" class="border border-gray-200 rounded-xl p-3 bg-gray-50 flex flex-col items-center gap-2 max-w-xs">
            <span class="text-[10px] font-bold text-amber-600 uppercase">Thumbnail Baru</span>
            <img :src="thumbnailPreview" class="w-40 h-28 object-cover rounded-lg border-2 border-amber-400" alt="New Thumbnail Preview" />
          </div>
        </div>

        <!-- Deskripsi (RichTextEditor) -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-gray-750">Deskripsi Lengkap</label>
          <RichTextEditor v-model="form.deskripsi" />
        </div>

        <!-- Submit Buttons -->
        <div class="border-t border-gray-100 pt-5 flex justify-end gap-3">
          <Link 
            href="/manajemen-program"
            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition"
          >
            Batal
          </Link>
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="px-6 py-2.5 bg-[#143E2C] hover:bg-[#1a4f38] text-white font-bold rounded-xl text-xs shadow-xs hover:shadow-md transition flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <span v-if="isSubmitting" class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></span>
            Simpan Perubahan
          </button>
        </div>

      </form>
    </div>
  </NazhirLayout>
</template>
