<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import WakifLayout from '@/layouts/WakifLayout.vue';
import { showAlert, showError } from '@/lib/alert';

const props = defineProps<{
    id: string | number;
}>();

const page = usePage();

const tab = ref('deskripsi');
const modalOpen = ref(false);
const modalStep = ref(1); // 1 = form, 2 = qris, 3 = success


// Program details state
const program = ref<any>(null);
const countdown = ref('Selesai');
const fullDeskripsi = ref('');
const donorCount = ref(0);
const donorsList = ref<any[]>([]);
const laporanList = ref<any[]>([]);

// Donors Pagination State
const currentPage = ref(1);
const totalPages = ref(1);
const limit = ref(10); // Show 10 per page as requested

// Form States
const nameInput = ref('');
const noHpInput = ref('');
const emailInput = ref('');
const nominal = ref<number | string | null>(null);
const customNominal = ref('');
const hideName = ref(false);
const pesanDoa = ref('');
const isSubmitting = ref(false);

// Error States
const nameError = ref('');
const noHpError = ref('');
const emailError = ref('');
const nominalError = ref('');
const paymentFileError = ref('');

// Date filters
const startDate = ref('');
const endDate = ref('');
const tempStartDate = ref('');
const tempEndDate = ref('');
const showDatePicker = ref(false);

// QRIS states
const mockTransactionId = ref('');
const createdTransaction = ref<any>(null);
const paymentFile = ref<File | null>(null);
const paymentFileName = ref('');
const paymentMethod = ref('qris');
const paymentMethodInput = ref('qris');
const qrisTimeLeft = ref('00:15:00');
const showCaraBayar = ref(true);
let countdownInterval: any = null;

const fileInput = ref<HTMLInputElement | null>(null);
const triggerFileInput = () => {
    fileInput.value?.click();
};

const formatRupiah = (number: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

const persentase = computed(() => {
    if (!program.value || program.value.target_dana <= 0) {
return 0;
}

    const p = (program.value.dana_terkumpul / program.value.target_dana) * 100;

    return Math.min(Math.round(p), 100);
});

const startQrisTimer = () => {
    let seconds = 15 * 60;
    qrisTimeLeft.value = '00:15:00';

    if (countdownInterval) {
clearInterval(countdownInterval);
}

    countdownInterval = setInterval(() => {
        seconds--;

        if (seconds <= 0) {
            clearInterval(countdownInterval);
            qrisTimeLeft.value = 'Expired';
            modalOpen.value = false;
            modalStep.value = 1;
            showError('Batas waktu pembayaran telah habis (15 menit). Transaksi Anda dibatalkan/kedaluwarsa.', 'Waktu Habis');

            return;
        }

        const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
        const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
        const secs = String(seconds % 60).padStart(2, '0');
        qrisTimeLeft.value = `${hrs}:${mins}:${secs}`;
    }, 1000);
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    showAlert('Disalin: ' + text, 'Berhasil');
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files.length > 0) {
        paymentFile.value = target.files[0];
        paymentFileName.value = target.files[0].name;
    }
};

const handleFileDrop = (e: DragEvent) => {
    e.preventDefault();

    if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
        paymentFile.value = e.dataTransfer.files[0];
        paymentFileName.value = e.dataTransfer.files[0].name;
    }
};

const applyDateFilter = () => {
    startDate.value = tempStartDate.value;
    endDate.value = tempEndDate.value;
    showDatePicker.value = false;
    loadDonors(1);
};

const clearDateFilter = () => {
    startDate.value = '';
    endDate.value = '';
    tempStartDate.value = '';
    tempEndDate.value = '';
    showDatePicker.value = false;
    loadDonors(1);
};

const proceedToQris = async () => {
    nameError.value = '';
    noHpError.value = '';
    emailError.value = '';
    nominalError.value = '';
    paymentFileError.value = '';

    const finalNominal = nominal.value === 'custom' ? Number(customNominal.value) : Number(nominal.value);
    let hasError = false;

    if (!nameInput.value && !hideName.value) {
        nameError.value = 'Nama wajib diisi, atau centang opsi Sembunyikan Nama Saya';
        hasError = true;
    }

    if (!noHpInput.value) {
        noHpError.value = 'No WhatsApp wajib diisi';
        hasError = true;
    }

    if (!emailInput.value) {
        emailError.value = 'Email wajib diisi';
        hasError = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        emailError.value = 'Format email tidak valid';
        hasError = true;
    }

    if (!finalNominal || finalNominal < 10000) {
        nominalError.value = 'Nominal wakaf minimal Rp10.000';
        hasError = true;
    } else if (String(finalNominal).length > 13) {
        nominalError.value = 'Nominal tidak boleh lebih dari 13 digit';
        hasError = true;
    }

    if (hasError) {
return;
}

    isSubmitting.value = true;

    try {
        const finalName = hideName.value ? 'Hamba Allah' : nameInput.value;
        const postData = {
            id_program: Number(props.id),
            nominal: finalNominal,
            pesan_doa: pesanDoa.value || '',
            hide_nama: hideName.value ? 1 : 0,
            nama: finalName,
            no_hp: noHpInput.value,
            email: emailInput.value,
            metode_pembayaran: paymentMethodInput.value.toUpperCase()
        };

        const isLoggedIn = !!page.props.auth?.user;
        const endpoint = isLoggedIn ? '/api/wakif/transaksi/user' : '/api/wakif/transaksi/guest';
        const token = localStorage.getItem('wakif_auth_token');
        const headers: any = {};

        if (isLoggedIn && token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await axios.post(endpoint, postData, { headers });

        if (response.data && response.data.success) {
            createdTransaction.value = response.data.data;
            mockTransactionId.value = createdTransaction.value.kode_referensi;
            paymentMethod.value = paymentMethodInput.value;
            startQrisTimer();
            modalStep.value = 2;
        }
    } catch (e: any) {
        console.error('Failed to initialize transaction:', e);
        const errMsg = e.response?.data?.message || 'Gagal membuat transaksi wakaf. Silakan coba lagi.';
        showError(errMsg);
    } finally {
        isSubmitting.value = false;
    }
};

const loadProgramData = async () => {
    if (!props.id || props.id === 'undefined' || isNaN(Number(props.id))) {
        console.warn('loadProgramData called with invalid ID:', props.id);

        return;
    }

    try {
        // Fetch program basic info
        const progRes = await axios.get(`/api/wakif/program-wakaf/${props.id}`);

        if (progRes.data && progRes.data.data) {
            program.value = progRes.data.data;
        }
        
        // Fetch countdown
        const countRes = await axios.get(`/api/wakif/program-wakaf/${props.id}/countdown`);

        if (countRes.data && countRes.data.data) {
            countdown.value = countRes.data.data.formatted;
        }

        // Fetch description
        const descRes = await axios.get(`/api/wakif/program-wakaf/${props.id}/deskripsi`);

        if (descRes.data && descRes.data.data) {
            fullDeskripsi.value = descRes.data.data.deskripsi;
        }

        // Fetch donor count
        const countDonorRes = await axios.get(`/api/wakif/counter-donatur?id_program=${props.id}`);

        if (countDonorRes.data && countDonorRes.data.data) {
            donorCount.value = countDonorRes.data.data.counter_donatur;
        }

        // Fetch reports
        const laporanRes = await axios.get(`/api/wakif/berita-laporan?id_program=${props.id}`);

        if (laporanRes.data && laporanRes.data.data) {
            laporanList.value = laporanRes.data.data;
        }

        // Fetch donors page 1
        await loadDonors(1);

    } catch (e) {
        console.error('Error fetching program detail data:', e);
    }
};

const loadDonors = async (pageNumber: number) => {
    if (!props.id || props.id === 'undefined' || isNaN(Number(props.id))) {
return;
}

    try {
        let url = `/api/wakif/program-wakaf/${props.id}/donatur?page=${pageNumber}&limit=${limit.value}`;

        if (startDate.value) {
            url += `&start=${startDate.value}`;
        }

        if (endDate.value) {
            url += `&end=${endDate.value}`;
        }

        const donorsRes = await axios.get(url);

        if (donorsRes.data && donorsRes.data.data) {
            const resData = donorsRes.data.data;
            donorsList.value = resData.data || [];
            currentPage.value = resData.current_page || 1;
            totalPages.value = resData.last_page || 1;
        }
    } catch (e) {
        console.error('Error fetching donors list:', e);
    }
};

const submitWakaf = async () => {
    paymentFileError.value = '';

    if (!paymentFile.value) {
        paymentFileError.value = 'Silakan unggah bukti pembayaran terlebih dahulu.';

        return;
    }

    if (!createdTransaction.value || !createdTransaction.value.id_transaksi) {
        showAlert('Data transaksi tidak ditemukan. Silakan ulangi proses.', 'Peringatan');

        return;
    }

    isSubmitting.value = true;

    try {
        const formData = new FormData();
        formData.append('bukti_pembayaran', paymentFile.value);
        formData.append('_method', 'PATCH');

        const endpoint = `/api/wakif/transaksi/${createdTransaction.value.id_transaksi}/bukti`;
        const response = await axios.post(endpoint, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data && response.data.success) {
            createdTransaction.value = response.data.data;

            if (countdownInterval) {
clearInterval(countdownInterval);
}

            modalStep.value = 3;
        }
    } catch (e: any) {
        console.error('Submit transaction proof failed:', e);
        const errMsg = e.response?.data?.message || 'Gagal mengirim bukti pembayaran. Silakan coba lagi.';
        showError(errMsg);
    } finally {
        isSubmitting.value = false;
    }
};

const closeModalAndRefresh = async () => {
    if (modalStep.value === 2 && createdTransaction.value) {
        try {
            const txId = createdTransaction.value.id_transaksi;
            await axios.patch(`/api/wakif/transaksi/${txId}/cancel`);
        } catch (e) {
            console.error('Failed to auto-cancel transaction on close:', e);
        }
    }

    modalOpen.value = false;
    modalStep.value = 1;
    createdTransaction.value = null;
    paymentFile.value = null;
    paymentFileName.value = '';
    // Clear form
    nominal.value = null;
    customNominal.value = '';
    pesanDoa.value = '';
    // Reset errors
    nameError.value = '';
    noHpError.value = '';
    emailError.value = '';
    nominalError.value = '';
    paymentFileError.value = '';

    // Reset inputs to user data if logged in
    if (page.props.auth?.user) {
        const u = page.props.auth.user as any;
        nameInput.value = u.nama || '';
        noHpInput.value = u.no_hp || '';
        emailInput.value = u.email || '';
    } else {
        nameInput.value = '';
        noHpInput.value = '';
        emailInput.value = '';
    }

    if (countdownInterval) {
clearInterval(countdownInterval);
}

    await loadProgramData();
};

const visiblePages = computed(() => {
    const pages: (number | string)[] = [];

    if (totalPages.value <= 5) {
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i);
        }
    } else {
        if (currentPage.value <= 3) {
            pages.push(1, 2, 3, '...', totalPages.value);
        } else if (currentPage.value >= totalPages.value - 2) {
            pages.push(1, '...', totalPages.value - 2, totalPages.value - 1, totalPages.value);
        } else {
            pages.push(1, '...', currentPage.value, '...', totalPages.value);
        }
    }

    return pages;
});

onMounted(async () => {
    // Auto-fill logged in user info if present
    if (page.props.auth?.user) {
        const u = page.props.auth.user as any;
        nameInput.value = u.nama || '';
        noHpInput.value = u.no_hp || '';
        emailInput.value = u.email || '';
    }

    await loadProgramData();

    // Check if ?wakaf=true query param is present
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('wakaf') === 'true' && program.value && program.value.status_program !== 0 && program.value.dana_terkumpul < program.value.target_dana) {
        modalOpen.value = true;
    }
});
</script>

<template>
    <Head :title="program?.nama_program || 'Pembangunan Madrasah'" />

    <WakifLayout>
        <!-- Breadcrumbs -->
        <div class="px-6 lg:px-12 py-4 text-[11px] font-semibold text-gray-500 border-b border-gray-100 flex items-center gap-2">
            <Link href="/" class="hover:text-primary">Beranda</Link> >
            <Link href="/program" class="hover:text-primary">Program Wakaf</Link> >
            <span class="text-gray-400">{{ program?.nama_program || 'Loading...' }}</span>
        </div>

        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-10">
            <!-- Main Content Split -->
            <div class="flex flex-col-reverse lg:flex-row gap-12 lg:gap-16">
                
                <!-- Left Content: Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-1 leading-tight">{{ program?.nama_program || 'Loading...' }}</h1>
                    <p class="text-[12px] text-gray-500 mb-6 font-medium">
                        Penggalangan dana dimulai {{ program ? new Date(program.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '' }}
                    </p>
                    
                    <p class="text-right text-[11px] font-bold text-[#638734] italic mb-1">{{ countdown }}</p>
                    
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <p class="text-xs text-[#638734] font-bold mb-[2px]">Terkumpul</p>
                            <p class="font-black text-gray-900 text-xl">{{ formatRupiah(program?.dana_terkumpul || 0) }}</p>
                        </div>
                        <p class="font-black text-gray-900 text-sm">{{ persentase }}%</p>
                    </div>
                    
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6 overflow-hidden">
                        <div class="bg-[#A4C240] h-2.5 rounded-full" :style="`width: ${persentase}%`"></div>
                    </div>
                    
                    <div class="flex border border-gray-200 rounded divide-x divide-gray-200 mb-8">
                        <div class="flex-1 p-3 flex items-center gap-3">
                            <div class="text-[#638734] shrink-0">
                               <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-semibold mb-0">Dana Dibutuhkan</p>
                                <p class="text-sm font-bold text-gray-800 leading-tight">{{ formatRupiah(program?.target_dana || 0) }}</p>
                            </div>
                        </div>
                        <div class="flex-1 p-3 flex items-center gap-3">
                            <div class="text-[#638734] shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-semibold mb-0">Hingga</p>
                                <p class="text-sm font-bold text-gray-800 leading-tight">
                                    {{ program ? new Date(program.due_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
 
                    <button 
                        @click="modalOpen = true" 
                        :disabled="program && (program.status_program === 0 || program.dana_terkumpul >= program.target_dana)"
                        class="w-full bg-[#1e5842] hover:bg-[#143E2C] text-white font-bold py-3.5 px-4 rounded text-sm transition-colors shadow disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none"
                    >
                        {{ program && (program.status_program === 0 || program.dana_terkumpul >= program.target_dana) ? 'Program Telah Selesai / Ditutup' : 'Wakaf Sekarang' }}
                    </button>
                </div>
                
                <!-- Right Content: Image -->
                <div class="flex-1">
                     <div class="w-full h-[320px] lg:h-[450px] bg-gray-200 rounded overflow-hidden">
                        <img :src="program?.gambar_thumbnail || '/dashboard_foto.png'" alt="Madrasah" class="w-full h-full object-cover">
                     </div>
                </div>

            </div>

            <!-- Description / Tabs Section -->
            <div class="mt-12 lg:mt-8 flex flex-col lg:flex-row gap-12 lg:gap-16">
                 <!-- Empty invisible left space to align tabs with image on desktop -->
                 <div class="hidden lg:block flex-1"></div>
                 
                 <!-- Tabs & Content mapped to right side visually matching the image column width -->
                 <div class="flex-1">
                     <!-- Tab Navigation -->
                     <div class="flex border-b border-gray-200 mb-8 w-full max-w-lg">
                        <button @click="tab = 'deskripsi'" :class="{'border-b-2 border-primary text-gray-900 font-bold': tab === 'deskripsi', 'text-gray-500 font-semibold': tab !== 'deskripsi'}" class="flex-1 pb-4 text-xs">
                          Deskripsi
                        </button>
                        <button @click="tab = 'donatur'" :class="{'border-b-2 border-primary text-gray-900 font-bold': tab === 'donatur', 'text-gray-500 font-semibold': tab !== 'donatur'}" class="flex-1 pb-4 text-xs">
                          Donatur ({{ donorCount }})
                        </button>
                        <button @click="tab = 'berita'" :class="{'border-b-2 border-primary text-gray-900 font-bold': tab === 'berita', 'text-gray-500 font-semibold': tab !== 'berita'}" class="flex-1 pb-4 text-xs">
                          Berita Laporan
                        </button>
                     </div>

                     <!-- Tab Content: Deskripsi -->
                     <div v-show="tab === 'deskripsi'" class="text-[12px] text-gray-600 space-y-6 max-w-2xl font-sans">
                         <h3 class="text-sm font-bold text-gray-900 font-sans">{{ program?.nama_program }}</h3>
                         <div class="leading-relaxed text-justify rich-text-content" v-html="fullDeskripsi"></div>
                         <img :src="program?.gambar_thumbnail || 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80'" alt="Inline madrasah view" class="w-full h-56 object-cover rounded">
                     </div>

                     <!-- Tab Content: Donatur -->
                     <div v-show="tab === 'donatur'" class="max-w-2xl">
                          <!-- Date Filter UI -->
                          <div class="mb-6 relative">
                              <label class="block text-[11px] font-bold text-gray-500 mb-1">Filter tanggal</label>
                              <div class="flex items-center gap-3">
                                  <div class="relative flex-1 max-w-xs">
                                      <input 
                                          type="text" 
                                          readonly 
                                          @click="showDatePicker = !showDatePicker"
                                          :value="startDate && endDate ? `${startDate} - ${endDate}` : 'Pilih Rentang Tanggal'" 
                                          class="w-full border border-[#d6c56b] rounded px-3 py-2 text-[12px] cursor-pointer bg-white focus:outline-none focus:ring-1 focus:ring-[#d6c56b]"
                                      />
                                      <button 
                                          type="button" 
                                          @click="showDatePicker = !showDatePicker"
                                          class="absolute right-0 top-0 bottom-0 px-3 bg-[#1e5842] hover:bg-[#143E2C] text-white rounded-r flex items-center justify-center transition-colors"
                                      >
                                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                          </svg>
                                      </button>
                                  </div>
                                  <!-- Limit Dropdown -->
                                  <div class="flex items-center gap-1.5 shrink-0">
                                      <span class="text-[11px] font-bold text-gray-500">Tampilkan:</span>
                                      <select v-model="limit" @change="loadDonors(1)" class="border border-[#d6c56b] rounded px-2.5 py-2 text-[12px] bg-white focus:outline-none focus:ring-1 focus:ring-[#d6c56b]">
                                          <option :value="10">10</option>
                                          <option :value="20">20</option>
                                          <option :value="50">50</option>
                                          <option :value="100">100</option>
                                      </select>
                                  </div>
                                  <button 
                                      v-if="startDate || endDate" 
                                      type="button" 
                                      @click="clearDateFilter" 
                                      class="text-[11px] text-red-600 hover:text-red-800 font-bold"
                                  >
                                      Reset
                                  </button>
                              </div>

                              <!-- Date Picker Dropdown -->
                              <div v-if="showDatePicker" class="absolute left-0 mt-2 z-50 bg-white border border-gray-200 rounded-lg shadow-xl p-4 w-72">
                                  <h4 class="text-xs font-bold text-gray-700 mb-3 text-center">Pilih Rentang Tanggal</h4>
                                  <div class="space-y-3">
                                      <div>
                                          <label class="block text-[10px] text-gray-500 font-bold mb-1">Mulai</label>
                                          <input type="date" v-model="tempStartDate" class="w-full border border-gray-300 rounded p-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-primary" />
                                      </div>
                                      <div>
                                          <label class="block text-[10px] text-gray-500 font-bold mb-1">Sampai</label>
                                          <input type="date" v-model="tempEndDate" class="w-full border border-gray-300 rounded p-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-primary" />
                                      </div>
                                      <div class="flex gap-2 justify-end pt-2">
                                          <button type="button" @click="showDatePicker = false" class="px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-50 font-semibold text-gray-600">Batal</button>
                                          <button type="button" @click="applyDateFilter" class="px-3 py-1 text-xs bg-[#1e5842] hover:bg-[#143E2C] text-white rounded font-bold">OK</button>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="space-y-4">
                              <div v-if="donorsList.length === 0" class="text-center text-xs text-gray-400 py-6">
                                  Belum ada donatur untuk program ini.
                              </div>
                              <div v-for="(d, i) in donorsList" :key="i" class="border-b border-gray-100 pb-4">
                                  <h4 class="text-xs font-bold text-gray-900">{{ d.hide_nama ? 'Hamba Allah' : d.nama }}</h4>
                                  <p class="text-[10px] text-gray-400 mb-1">
                                      {{ new Date(d.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}) }}
                                      • <span class="font-bold text-primary">{{ formatRupiah(d.nominal) }}</span>
                                  </p>
                                  <p v-if="d.pesan_doa" class="text-[11px] font-medium text-gray-600 block italic leading-tight">"{{ d.pesan_doa }}"</p>
                              </div>
                          </div>
                          
                          <!-- Pagination -->
                          <div v-if="totalPages > 1" class="flex justify-end mt-8 gap-1.5 items-center">
                              <button 
                                v-for="(p, idx) in visiblePages" 
                                :key="idx"
                                @click="p !== '...' ? loadDonors(Number(p)) : null"
                                :disabled="p === '...'"
                                :class="{
                                  'bg-[#1e5842] text-white border-[#1e5842]': currentPage === p, 
                                  'border border-gray-200 text-gray-600 hover:bg-gray-50': currentPage !== p && p !== '...',
                                  'text-gray-400 border-none cursor-default': p === '...'
                                }"
                                class="w-7 h-7 rounded border flex items-center justify-center text-[10px] font-bold transition">
                                  {{ p }}
                              </button>
                              <button 
                                @click="currentPage < totalPages ? loadDonors(currentPage + 1) : null"
                                :disabled="currentPage === totalPages"
                                class="w-7 h-7 rounded border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center text-[10px] font-bold transition disabled:opacity-50">
                                  &gt;
                              </button>
                          </div>
                     </div>

                     <!-- Tab Content: Berita Laporan -->
                     <div v-show="tab === 'berita'" class="max-w-2xl relative border-l-2 border-gray-100 pl-6 ml-2 space-y-10 py-2">
                          <div v-if="laporanList.length === 0" class="text-center text-xs text-gray-400 py-6">
                              Belum ada berita laporan penyaluran untuk program ini.
                          </div>
                          <div v-for="(news, i) in laporanList" :key="i" class="relative">
                               <div class="absolute -left-[35px] top-1">
                                   <svg class="w-5 h-5 text-gray-400 bg-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                               </div>
                               <div class="flex flex-col md:flex-row gap-6">
                                   <div class="w-24 shrink-0 mt-1">
                                       <p class="text-[11px] text-green-600 font-bold">
                                           {{ new Date(news.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) }}
                                       </p>
                                   </div>
                                   <div class="flex-1">
                                        <h4 class="text-sm font-bold text-gray-900 mb-3">{{ news.judul_laporan }}</h4>
                                        <div class="text-[11px] text-gray-600 leading-relaxed font-sans text-justify mb-2 rich-text-content" v-html="news.keterangan"></div>
                                       
                                       <!-- Image presentation for the report -->
                                       <div class="my-3 max-w-md">
                                           <img :src="news.gambar_laporan || '/dashboard_foto.png'" alt="Laporan Penyaluran" class="w-full h-44 object-cover rounded shadow-sm">
                                       </div>
                                       
                                       <div class="flex gap-4 text-[10px] text-gray-500 font-sans mt-2">
                                           <span>Dana Disalurkan: <strong class="text-primary">{{ formatRupiah(news.dana_disalurkan) }}</strong></span>
                                           <span>Penerima Manfaat: <strong>{{ news.penerima_manfaat }} Orang</strong></span>
                                       </div>
                                   </div>
                               </div>
                          </div>
                     </div>

                 </div>
            </div>
        </section>

        <!-- Dynamic Wakaf Modal -->
        <div v-if="modalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl w-full max-w-[500px] overflow-hidden shadow-2xl relative flex flex-col max-h-[95vh]">
                <!-- Close cross -->
                <button @click="closeModalAndRefresh" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 z-10 font-bold p-1">
                    ✕
                </button>
                
                <!-- STEP 1: Input Form -->
                <div v-if="modalStep === 1" class="p-6 overflow-y-auto">
                    <h2 class="text-center font-bold text-[16px] text-gray-900 mb-6">Wakaf {{ program?.nama_program }}</h2>
                    
                    <!-- Informasi Data Diri -->
                    <h3 class="text-[12px] font-bold text-gray-800 mb-3 border-b border-gray-100 pb-1">Informasi Data Diri</h3>
                    
                    <div class="mb-4">
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                                        <input type="text" v-model="nameInput" :disabled="hideName" placeholder="Masukkan nama Anda" class="w-full border border-[#d6c56b] rounded px-3 py-2 text-[12px] focus:outline-none focus:ring-1 focus:ring-[#d6c56b] disabled:bg-gray-100">
                                        <p v-if="nameError" class="text-red-500 text-[10px] mt-1 font-semibold">{{ nameError }}</p>
                                        
                                        <label class="flex items-center mt-2.5 gap-2 cursor-pointer">
                                            <input type="checkbox" v-model="hideName" class="w-3.5 h-3.5 rounded border-gray-300 text-primary focus:ring-primary">
                                            <span class="text-[11px] text-gray-700 font-medium">Sembunyikan nama Saya (Hamba Allah)</span>
                                        </label>
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">No WhatsApp <span class="text-red-500">*</span></label>
                                        <input type="text" v-model="noHpInput" placeholder="Masukkan No WhatsApp Anda" class="w-full border border-[#d6c56b] rounded px-3 py-2 text-[12px] focus:outline-none focus:ring-1 focus:ring-[#d6c56b]">
                                        <p v-if="noHpError" class="text-red-500 text-[10px] mt-1 font-semibold">{{ noHpError }}</p>
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                        <input type="email" v-model="emailInput" placeholder="Masukkan Email Anda" class="w-full border border-[#d6c56b] rounded px-3 py-2 text-[12px] focus:outline-none focus:ring-1 focus:ring-[#d6c56b]">
                                        <p v-if="emailError" class="text-red-500 text-[10px] mt-1 font-semibold">{{ emailError }}</p>
                                    </div>

                                    <!-- Nominal -->
                                    <h3 class="text-[12px] font-bold text-gray-800 mb-3 border-b border-gray-100 pb-1">Nominal <span class="text-red-500">*</span></h3>
                                    
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mb-3">
                                        <button v-for="val in [10000, 50000, 75000, 100000, 500000, 1000000, 1500000]" :key="val" 
                                          type="button"
                                          @click="nominal = val; customNominal = ''; nominalError = ''"
                                          :class="{'border-[#b1cf49] bg-[#b1cf49]/10 text-gray-800 font-bold': nominal === val, 'border-gray-300 text-gray-500': nominal !== val}"
                                          class="border rounded py-1.5 text-[11px] hover:bg-gray-50 transition">
                                           Rp{{ val.toLocaleString('id-ID') }}
                                        </button>
                                        <button type="button" @click="nominal = 'custom'; nominalError = ''" 
                                           :class="{'bg-[#c5db54] border-[#b1cf49] text-gray-800 font-bold': nominal === 'custom', 'border-gray-300 text-gray-500 hover:bg-gray-50': nominal !== 'custom'}" 
                                           class="border rounded py-1.5 text-[11px] transition">
                                           Lainnya
                                        </button>
                                    </div>

                                    <div class="flex items-center border border-[#d6c56b] rounded overflow-hidden">
                                        <div class="bg-gray-50 px-3 py-2 text-[12px] text-gray-500 border-r border-[#d6c56b] font-semibold">Rp</div>
                                        <input type="number" :disabled="nominal !== 'custom'" v-model="customNominal" placeholder="Masukkan nominal uang yang akan diwakafkan" class="w-full px-3 py-2 text-[12px] focus:outline-none focus:ring-0 disabled:bg-gray-100">
                                    </div>
                                    <p v-if="nominalError" class="text-red-500 text-[10px] mt-1 font-semibold mb-4">{{ nominalError }}</p>
                                    <div v-else class="mb-5"></div>

                    <!-- Doa -->
                    <div class="mb-4">
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Do'a Terbaik</label>
                        <textarea rows="3" v-model="pesanDoa" placeholder="Tuliskan do'a terbaik Anda" class="w-full border border-[#d6c56b] rounded px-3 py-2 text-[12px] focus:outline-none focus:ring-1 focus:ring-[#d6c56b]"></textarea>
                    </div>

                    <!-- Metode Pembayaran Selection -->
                    <div class="mb-5">
                        <label class="block text-[11px] font-semibold text-gray-700 mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- QRIS Option -->
                            <div 
                                @click="paymentMethodInput = 'qris'"
                                :class="paymentMethodInput === 'qris' ? 'border-[#1e5842] bg-[#1e5842]/5 border-2' : 'border-gray-300 hover:bg-gray-50 border'"
                                class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition select-none"
                            >
                                <div class="w-4 h-4 rounded-full border border-gray-400 flex items-center justify-center">
                                    <div v-if="paymentMethodInput === 'qris'" class="w-2.5 h-2.5 rounded-full bg-[#1e5842]"></div>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-gray-950 leading-none">QRIS</h4>
                                </div>
                            </div>

                            <!-- Transfer Bank BCA Option -->
                            <div 
                                @click="paymentMethodInput = 'bca'"
                                :class="paymentMethodInput === 'bca' ? 'border-[#1e5842] bg-[#1e5842]/5 border-2' : 'border-gray-300 hover:bg-gray-50 border'"
                                class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition select-none"
                            >
                                <div class="w-4 h-4 rounded-full border border-gray-400 flex items-center justify-center">
                                    <div v-if="paymentMethodInput === 'bca'" class="w-2.5 h-2.5 rounded-full bg-[#1e5842]"></div>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-gray-950 leading-none">Transfer BCA</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button :disabled="isSubmitting" @click="proceedToQris" class="bg-[#1e5842] hover:bg-green-800 text-white font-bold py-2 px-6 rounded text-sm transition shadow shadow-green-900/30 disabled:opacity-50 flex items-center gap-2">
                            <span v-if="isSubmitting" class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"></span>
                            {{ isSubmitting ? 'Memproses...' : 'Lanjutkan' }}
                        </button>
                    </div>
                </div>

                <!-- STEP 2: QRIS / BCA Payment -->
                <div v-if="modalStep === 2" class="p-6 overflow-y-auto">
                    <h2 class="text-center font-bold text-[16px] text-gray-900 mb-2">Wakaf {{ program?.nama_program }}</h2>
                    
                    <div class="bg-gray-50 border border-gray-150 rounded-lg p-3.5 mb-4">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Ikrar Berwakaf</h4>
                        <p class="text-[11.5px] text-gray-700 leading-relaxed">
                            Saya <strong class="text-gray-900">{{ hideName ? 'Hamba Allah' : nameInput }}</strong> menitipkan amanah harta sebesar <strong class="text-primary">{{ formatRupiah(nominal === 'custom' ? Number(customNominal) : Number(nominal)) }}</strong>, atas nama untuk <strong class="text-gray-900">Program Wakaf {{ program?.nama_program }}</strong>. Semoga dapat tersampaikan sesuai amanah dan ketentuan syariat.
                        </p>
                    </div>

                    <!-- Nominal & Transaksi ID -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between items-center bg-gray-50 border border-gray-100 rounded p-2.5">
                            <div>
                                <span class="text-[9.5px] text-gray-400 font-bold block">Nominal Wakaf</span>
                                <span class="text-sm font-black text-gray-900">{{ formatRupiah(nominal === 'custom' ? Number(customNominal) : Number(nominal)) }}</span>
                            </div>
                            <button @click="copyToClipboard(String(nominal === 'custom' ? Number(customNominal) : Number(nominal)))" class="text-xs text-primary hover:text-green-800 font-bold flex items-center gap-1">
                                Salin
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            </button>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 border border-gray-100 rounded p-2.5">
                            <div>
                                <span class="text-[9.5px] text-gray-400 font-bold block">ID Transaksi</span>
                                <span class="text-xs font-mono font-bold text-gray-800">{{ mockTransactionId }}</span>
                            </div>
                            <button @click="copyToClipboard(mockTransactionId)" class="text-xs text-primary hover:text-green-800 font-bold flex items-center gap-1">
                                Salin
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="flex justify-center mb-4">
                        <div class="bg-gray-100 text-gray-700 text-[11px] font-bold px-4 py-1.5 rounded-full flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                            Pembayaran dalam: <span class="font-mono text-red-600 font-black">{{ qrisTimeLeft }}</span>
                        </div>
                    </div>

                    <!-- Selected Payment Method Banner -->
                    <div class="px-4 py-2.5 bg-[#1e5842]/5 border border-[#1e5842]/20 rounded-xl mb-4 text-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Metode Pembayaran Terpilih</span>
                        <span class="text-xs font-black text-[#1e5842] uppercase mt-0.5 block">
                            {{ paymentMethod === 'qris' ? 'QRIS' : 'Transfer Bank BCA' }}
                        </span>
                    </div>

                    <!-- QRIS Image Box -->
                    <div v-show="paymentMethod === 'qris'" class="flex flex-col items-center border border-gray-200 rounded-lg p-4 bg-white mb-4">
                        <span class="text-xs font-black text-gray-800 tracking-wider mb-2">QRIS PEMBAYARAN</span>
                        <div class="w-48 h-48 border border-gray-100 flex items-center justify-center p-2 rounded bg-white">
                            <img src="/QRIS.png" alt="QRIS Code" class="max-w-full max-h-full object-contain" />
                        </div>
                        <a href="/QRIS.png" download="QRIS-Amanah-Baiturrahman.png" class="mt-3 px-4 py-1.5 bg-[#1e5842]/10 hover:bg-[#1e5842]/20 text-[#1e5842] rounded-full text-[11px] font-bold transition flex items-center gap-1.5 border border-[#1e5842]/25">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh QRIS
                        </a>
                        <span class="text-[9px] text-gray-400 mt-2 text-center">Scan QRIS menggunakan aplikasi pembayaran digital Anda</span>
                    </div>

                    <!-- BCA Transfer Info Box -->
                    <div v-show="paymentMethod === 'bca'" class="border border-gray-200 rounded-lg p-4 bg-white mb-4 flex flex-col items-center">
                        <span class="text-xs font-black text-gray-800 tracking-wider mb-3">TRANSFER BANK BCA</span>
                        <div class="w-full bg-gray-50 p-3.5 rounded-lg border border-gray-150 space-y-3">
                            <div class="flex justify-between items-center border-b border-gray-200/50 pb-2">
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">Nama Bank</span>
                                <span class="text-xs font-bold text-gray-900">BCA (Bank Central Asia)</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-200/50 pb-2">
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">No. Rekening</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-gray-900 font-mono tracking-wider">12345678</span>
                                    <button type="button" @click="copyToClipboard('12345678')" class="text-primary hover:text-green-800 p-1 hover:bg-gray-200/50 rounded transition duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">Nama Penerima</span>
                                <span class="text-xs font-bold text-gray-905">Yayasan Amanah Baiturrahman</span>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible Cara Membayar -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                        <button @click="showCaraBayar = !showCaraBayar" class="w-full flex justify-between items-center px-4 py-2 bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-700">
                            <span>Cara Membayar</span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{'rotate-180': showCaraBayar}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div v-show="showCaraBayar" class="p-3 text-[11px] text-gray-600 space-y-2 bg-white leading-relaxed">
                            <template v-if="paymentMethod === 'qris'">
                                <p>1. Buka aplikasi e-wallet (Gopay, OVO, Dana, LinkAja) or Mobile Banking Anda.</p>
                                <p>2. Pilih menu <strong>Scan QRIS</strong> or bayar menggunakan gambar QR.</p>
                                <p>3. Pindai/Scan QR Code yang tertera di atas.</p>
                                <p>4. Masukkan nominal yang sesuai (<strong>{{ formatRupiah(nominal === 'custom' ? Number(customNominal) : Number(nominal)) }}</strong>).</p>
                                <p>5. Selesaikan proses transfer lalu simpan bukti transaksi.</p>
                                <p>6. Unggah bukti pembayaran tersebut pada kolom di bawah ini.</p>
                            </template>
                            <template v-else>
                                <p>1. Buka aplikasi Mobile Banking, Internet Banking, or pergi ke ATM BCA terdekat.</p>
                                <p>2. Pilih menu <strong>Transfer</strong> -&gt; <strong>Ke Rekening BCA</strong>.</p>
                                <p>3. Masukkan nomor rekening tujuan: <strong>12345678</strong>.</p>
                                <p>4. Masukkan nominal yang sesuai (<strong>{{ formatRupiah(nominal === 'custom' ? Number(customNominal) : Number(nominal)) }}</strong>).</p>
                                <p>5. Selesaikan proses transfer lalu simpan struk/bukti transaksi.</p>
                                <p>6. Unggah bukti pembayaran tersebut pada kolom di bawah ini.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Upload File Box -->
                    <div class="mb-6">
                        <label class="block text-[11px] font-bold text-gray-700 mb-1.5">Upload Bukti Pembayaran <span class="text-red-500">*</span></label>
                        <div 
                            @dragover.prevent
                            @drop.prevent="handleFileDrop"
                            class="border-2 border-dashed border-gray-300 hover:border-primary rounded-lg p-5 flex flex-col items-center justify-center cursor-pointer transition-colors bg-gray-55"
                            @click="triggerFileInput"
                        >
                            <input 
                                type="file" 
                                ref="fileInput" 
                                class="hidden" 
                                accept="image/jpeg,image/png,image/jpg,application/pdf"
                                @change="handleFileChange"
                            />
                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="text-[11px] text-gray-600 text-center font-medium">
                                <span class="text-primary font-bold">Klik untuk cari</span> or drag/drop bukti pembayaran
                            </p>
                            <p class="text-[9px] text-gray-400 mt-1">Maks. 2MB (JPG, JPEG, PNG)</p>
                            
                            <!-- Display file name if chosen -->
                            <div v-if="paymentFileName" class="mt-3 bg-[#b1cf49]/15 border border-[#b1cf49] rounded px-3 py-1 text-[11px] text-gray-800 font-semibold flex items-center gap-1 max-w-full">
                                <span class="truncate max-w-[200px]">{{ paymentFileName }}</span>
                                <button type="button" @click.stop="paymentFile = null; paymentFileName = ''" class="text-red-600 font-bold hover:text-red-800 pl-1">✕</button>
                            </div>
                        </div>
                        <p v-if="paymentFileError" class="text-red-500 text-[10px] mt-1.5 font-semibold text-center">{{ paymentFileError }}</p>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex justify-between items-center border-t border-gray-100 pt-4">
                        <button type="button" @click="modalStep = 1" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                            Kembali
                        </button>
                        <button :disabled="isSubmitting || !paymentFile" @click="submitWakaf" class="bg-[#1e5842] hover:bg-green-800 text-white font-bold py-2 px-6 rounded text-sm transition shadow shadow-green-900/30 disabled:opacity-50">
                            {{ isSubmitting ? 'Memproses...' : 'Lanjutkan' }}
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Success Screen -->
                <div v-if="modalStep === 3" class="p-8 flex flex-col items-center text-center">
                    <!-- Checkmark Circle -->
                    <div class="w-16 h-16 bg-[#b1cf49]/10 border-2 border-[#b1cf49] rounded-full flex items-center justify-center text-[#1e5842] mb-4 shadow-lg shadow-[#b1cf49]/20">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    
                    <h2 class="text-lg font-black text-gray-900 mb-2">Menunggu Verifikasi</h2>
                    <p class="text-xs text-gray-500 mb-6 max-w-sm">
                        Alhamdulillah, bukti pembayaran Anda telah kami terima. Tim kami sedang melakukan verifikasi — Anda akan mendapat konfirmasi melalui email setelah diverifikasi.
                    </p>

                    <!-- Transaction details card -->
                    <div class="w-full bg-gray-50 border border-gray-150 rounded-xl p-4 mb-8 text-left space-y-2.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-400 font-semibold">Nama Wakif</span>
                            <span class="font-bold text-gray-800">{{ createdTransaction?.nama || (hideName ? 'Hamba Allah' : nameInput) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2.5">
                            <span class="text-gray-400 font-semibold">Nominal Wakaf</span>
                            <span class="font-black text-primary">{{ formatRupiah(createdTransaction?.nominal || (nominal === 'custom' ? Number(customNominal) : Number(nominal))) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2.5">
                            <span class="text-gray-400 font-semibold">ID Transaksi</span>
                            <span class="font-mono font-bold text-gray-800">{{ createdTransaction?.kode_referensi || mockTransactionId }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2.5">
                            <span class="text-gray-400 font-semibold">Status Pembayaran</span>
                            <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-[10px] font-bold">Menunggu Verifikasi</span>
                        </div>
                    </div>

                    <button @click="closeModalAndRefresh" class="w-full bg-[#1e5842] hover:bg-green-800 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition shadow shadow-green-900/30">
                        Selesai
                    </button>
                </div>
            </div>
        </div>

    </WakifLayout>
</template>
