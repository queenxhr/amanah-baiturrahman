<script setup lang="ts">
import NazhirLayout from '@/layouts/NazhirLayout.vue';
import RichTextEditor from '@/components/Nazhir/RichTextEditor.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps<{
    id: string | number;
}>();

const programName = ref('');
const programId = ref<number | null>(null);
const form = ref({
    judul_laporan: '',
    dana_disalurkan: '',
    penerima_manfaat: '',
    keterangan: ''
});

const errors = ref<Record<string, string>>({});
const isLoading = ref(true);
const isSubmitting = ref(false);

const fetchLaporanDetails = async () => {
    try {
        const res = await axios.get(`/api/nazhir/laporan/detail/${props.id}`);
        if (res.data && res.data.data) {
            const report = res.data.data;
            form.value.judul_laporan = report.judul_laporan || '';
            form.value.dana_disalurkan = report.dana_disalurkan ? String(report.dana_disalurkan) : '';
            form.value.penerima_manfaat = report.penerima_manfaat ? String(report.penerima_manfaat) : '';
            form.value.keterangan = report.keterangan || '';
            programId.value = report.id_program;
            
            if (report.id_program) {
                const progRes = await axios.get(`/api/wakif/program-wakaf/${report.id_program}`);
                if (progRes.data && progRes.data.data) {
                    programName.value = progRes.data.data.nama_program || '';
                }
            }
        }
    } catch (e) {
        console.error('Failed to fetch report details:', e);
        alert('Gagal mengambil detail laporan.');
    } finally {
        isLoading.value = false;
    }
};

const validateForm = () => {
    errors.value = {};
    let isValid = true;

    if (!form.value.judul_laporan.trim()) {
        errors.value.judul_laporan = 'Judul laporan wajib diisi.';
        isValid = false;
    }
    if (!form.value.dana_disalurkan || Number(form.value.dana_disalurkan) < 0) {
        errors.value.dana_disalurkan = 'Dana disalurkan harus diisi dengan angka minimal 0.';
        isValid = false;
    }
    if (!form.value.penerima_manfaat || Number(form.value.penerima_manfaat) < 0) {
        errors.value.penerima_manfaat = 'Penerima manfaat harus diisi dengan angka minimal 0.';
        isValid = false;
    }

    return isValid;
};

const handleSubmit = async () => {
    if (!validateForm()) return;
    isSubmitting.value = true;

    try {
        const payload = {
            id_program: programId.value,
            judul_laporan: form.value.judul_laporan,
            dana_disalurkan: Number(form.value.dana_disalurkan),
            penerima_manfaat: Number(form.value.penerima_manfaat),
            keterangan: form.value.keterangan
        };

        await axios.put(`/api/nazhir/laporan/${props.id}`, payload);

        alert('Laporan penyaluran berhasil diperbarui!');
        router.visit('/manajemen-program');
    } catch (e: any) {
        console.error('Failed to update report:', e);
        if (e.response && e.response.data && e.response.data.errors) {
            const apiErrors = e.response.data.errors;
            Object.keys(apiErrors).forEach(key => {
                errors.value[key] = apiErrors[key][0];
            });
        } else {
            alert('Terjadi kesalahan saat memperbarui laporan.');
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    fetchLaporanDetails();
});
</script>

<template>
  <Head title="Edit Laporan Penyaluran" />

  <NazhirLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      
      <!-- Breadcrumb / Header -->
      <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
        <Link href="/manajemen-program" class="hover:text-gray-600">Program</Link>
        <span>/</span>
        <span class="text-gray-700">Edit Laporan</span>
      </div>

      <div>
        <h1 class="text-2xl font-black text-gray-900 leading-tight">Edit Laporan Penyaluran</h1>
        <p class="text-xs text-gray-500">Edit laporan realisasi penyaluran dana wakaf untuk program yang telah selesai atau sedang berjalan.</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="bg-white border border-gray-150 rounded-2xl p-10 flex justify-center items-center shadow-xs">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#143E2C]"></div>
      </div>

      <!-- Form Card -->
      <form v-else @submit.prevent="handleSubmit" class="bg-white border border-gray-150 rounded-2xl p-6 md:p-8 shadow-xs space-y-5">
        
        <!-- Info Program Preview -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex flex-col gap-1">
          <span class="text-[9px] font-black text-[#143E2C] uppercase tracking-wider">Melaporkan Untuk Program</span>
          <span class="text-sm font-black text-gray-900">{{ programName || 'Program Wakaf' }}</span>
        </div>

        <!-- Judul Laporan -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-gray-750">Judul Laporan / Kegiatan <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            v-model="form.judul_laporan"
            placeholder="Contoh: Penyaluran Karpet Masjid Tahap Pertama..."
            :class="[
              'w-full bg-gray-50 border rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 transition-all',
              errors.judul_laporan ? 'border-red-400 focus:ring-red-400' : 'border-gray-250 focus:ring-[#143E2C] focus:border-[#143E2C]'
            ]"
          />
          <span v-if="errors.judul_laporan" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.judul_laporan }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Dana Disalurkan -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-gray-750">Dana Disalurkan (Rupiah) <span class="text-red-500">*</span></label>
            <input 
              type="number" 
              v-model="form.dana_disalurkan"
              placeholder="Contoh: 15000000"
              :class="[
                'w-full bg-gray-50 border rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 transition-all',
                errors.dana_disalurkan ? 'border-red-400 focus:ring-red-400' : 'border-gray-250 focus:ring-[#143E2C] focus:border-[#143E2C]'
              ]"
            />
            <span v-if="errors.dana_disalurkan" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.dana_disalurkan }}</span>
          </div>

          <!-- Penerima Manfaat -->
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-gray-750">Penerima Manfaat (Orang/Jiwa) <span class="text-red-500">*</span></label>
            <input 
              type="number" 
              v-model="form.penerima_manfaat"
              placeholder="Contoh: 150"
              :class="[
                'w-full bg-gray-50 border rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-1 transition-all',
                errors.penerima_manfaat ? 'border-red-400 focus:ring-red-400' : 'border-gray-250 focus:ring-[#143E2C] focus:border-[#143E2C]'
              ]"
            />
            <span v-if="errors.penerima_manfaat" class="text-[10px] font-bold text-red-500 mt-0.5">{{ errors.penerima_manfaat }}</span>
          </div>
        </div>

        <!-- Keterangan Laporan (RichTextEditor) -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-gray-750">Keterangan & Galeri Kegiatan</label>
          <RichTextEditor v-model="form.keterangan" />
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

<style scoped>
</style>
