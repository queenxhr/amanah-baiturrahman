import Swal from 'sweetalert2';

export const showAlert = async (message: string, title = 'Informasi'): Promise<void> => {
    await Swal.fire({
        title,
        text: message,
        icon: 'info',
        confirmButtonColor: '#1e5842',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-xl font-sans text-sm',
            confirmButton: 'px-5 py-2 rounded text-sm font-bold text-white transition'
        }
    });
};

export const showSuccess = async (message: string, title = 'Berhasil'): Promise<void> => {
    await Swal.fire({
        title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#1e5842',
        confirmButtonText: 'Selesai',
        customClass: {
            popup: 'rounded-xl font-sans text-sm',
            confirmButton: 'px-5 py-2 rounded text-sm font-bold text-white transition'
        }
    });
};

export const showError = async (message: string, title = 'Gagal'): Promise<void> => {
    await Swal.fire({
        title,
        text: message,
        icon: 'error',
        confirmButtonColor: '#e11d48', // rose-600 for error
        confirmButtonText: 'Tutup',
        customClass: {
            popup: 'rounded-xl font-sans text-sm',
            confirmButton: 'px-5 py-2 rounded text-sm font-bold text-white transition'
        }
    });
};

export const showConfirm = async (message: string, title = 'Konfirmasi'): Promise<boolean> => {
    const result = await Swal.fire({
        title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1e5842',
        cancelButtonColor: '#6b7280', // gray-500
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-xl font-sans text-sm',
            confirmButton: 'px-5 py-2 rounded text-sm font-bold text-white transition',
            cancelButton: 'px-5 py-2 rounded text-sm font-bold text-white transition'
        }
    });
    return result.isConfirmed;
};

export const showToastError = (message: string): void => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'error',
        title: message,
        customClass: {
            popup: 'rounded-xl font-sans text-xs'
        }
    });
};
