<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
  modelValue: string;
  placeholder?: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editorRef = ref<HTMLDivElement | null>(null);
const editorImageInput = ref<HTMLInputElement | null>(null);
const fontColorInput = ref<HTMLInputElement | null>(null);
const highlightColorInput = ref<HTMLInputElement | null>(null);
const savedRange = ref<Range | null>(null);

const activeStates = ref({
  bold: false,
  italic: false,
  underline: false,
  strikeThrough: false,
  insertUnorderedList: false,
  insertOrderedList: false,
  subscript: false,
  superscript: false,
});

const currentFont = ref('Roboto');
const currentSize = ref('3'); // Normal

// Watch external value updates to keep editor synchronized without losing cursor
watch(() => props.modelValue, (newVal) => {
  if (editorRef.value && editorRef.value.innerHTML !== newVal) {
    editorRef.value.innerHTML = newVal || '';
  }
});

const onInput = () => {
  if (editorRef.value) {
    emit('update:modelValue', editorRef.value.innerHTML);
  }
};

const format = (command: string, value: string = '') => {
  document.execCommand(command, false, value);
  onInput();
  updateActiveStates();
  if (editorRef.value) {
    editorRef.value.focus();
  }
};

// Save selection range
const saveSelection = () => {
  const sel = window.getSelection();
  if (sel && sel.rangeCount > 0) {
    savedRange.value = sel.getRangeAt(0);
  }
};

// Restore selection range
const restoreSelection = () => {
  if (savedRange.value) {
    const sel = window.getSelection();
    if (sel) {
      sel.removeAllRanges();
      sel.addRange(savedRange.value);
    }
  }
};

const handleSelectionChange = () => {
  if (editorRef.value && document.activeElement && editorRef.value.contains(document.activeElement)) {
    saveSelection();
    updateActiveStates();
  }
};

const updateActiveStates = () => {
  activeStates.value.bold = document.queryCommandState('bold');
  activeStates.value.italic = document.queryCommandState('italic');
  activeStates.value.underline = document.queryCommandState('underline');
  activeStates.value.strikeThrough = document.queryCommandState('strikeThrough');
  activeStates.value.insertUnorderedList = document.queryCommandState('insertUnorderedList');
  activeStates.value.insertOrderedList = document.queryCommandState('insertOrderedList');
  activeStates.value.subscript = document.queryCommandState('subscript');
  activeStates.value.superscript = document.queryCommandState('superscript');

  // Attempt to read current font family and size
  const font = document.queryCommandValue('fontName');
  if (font) {
    // strip out quotes if any
    currentFont.value = font.replace(/['"]/g, '');
  }
  const size = document.queryCommandValue('fontSize');
  if (size) {
    currentSize.value = size;
  }
};

const addLink = () => {
  saveSelection();
  const url = prompt('Masukkan URL Link:');
  restoreSelection();
  if (url) {
    format('createLink', url);
  }
};

const triggerImageUpload = () => {
  saveSelection();
  if (editorImageInput.value) {
    editorImageInput.value.click();
  }
};

const handleImageUpload = async (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    const formData = new FormData();
    formData.append('image', file);

    try {
      const res = await axios.post('/api/nazhir/upload-image', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      if (res.data && res.data.success && res.data.url) {
        restoreSelection();
        format('insertImage', res.data.url);
      }
    } catch (err) {
      console.error('Failed to upload editor image:', err);
      alert('Gagal mengunggah gambar. Pastikan file adalah gambar dan ukuran maksimal 5MB.');
    }
  }
};

onMounted(() => {
  if (editorRef.value) {
    editorRef.value.innerHTML = props.modelValue || '';
  }
  document.addEventListener('selectionchange', handleSelectionChange);
});

onUnmounted(() => {
  document.removeEventListener('selectionchange', handleSelectionChange);
});
</script>

<template>
  <div class="border border-gray-300 rounded-[20px] overflow-hidden focus-within:border-[#143E2C] focus-within:ring-1 focus-within:ring-[#143E2C] transition-all bg-white">
    <!-- Hidden Inputs -->
    <input type="file" ref="editorImageInput" accept="image/*" class="hidden" @change="handleImageUpload" />
    <input type="color" ref="fontColorInput" class="hidden" @input="(e: any) => format('foreColor', e.target.value)" />
    <input type="color" ref="highlightColorInput" class="hidden" @input="(e: any) => format('hiliteColor', e.target.value)" />

    <!-- Toolbar -->
    <div class="bg-white border-b border-gray-250 px-4 py-3 flex flex-wrap items-center gap-2 text-gray-700 select-none">
      
      <!-- Font Family -->
      <select 
        v-model="currentFont"
        @change="(e: any) => format('fontName', e.target.value)" 
        class="bg-white border border-gray-300 text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
      >
        <option value="Roboto">Roboto</option>
        <option value="Nunito Sans">Nunito Sans</option>
        <option value="Arial">Arial</option>
        <option value="Georgia">Georgia</option>
        <option value="Courier New">Courier</option>
      </select>

      <!-- Font Size -->
      <select 
        v-model="currentSize"
        @change="(e: any) => format('fontSize', e.target.value)" 
        class="bg-white border border-gray-300 text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#143E2C] cursor-pointer"
      >
        <option value="2">Kecil</option>
        <option value="3">Normal</option>
        <option value="4">Sedang</option>
        <option value="5">Besar</option>
        <option value="6">Sangat Besar</option>
      </select>

      <div class="h-5 w-px bg-gray-200 mx-1"></div>

      <!-- Formatting Buttons -->
      <button 
        type="button" 
        @click="format('bold')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center font-black transition', activeStates.bold ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Tebal (Bold)"
      >
        B
      </button>
      <button 
        type="button" 
        @click="format('italic')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center italic transition', activeStates.italic ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Miring (Italic)"
      >
        I
      </button>
      <button 
        type="button" 
        @click="format('underline')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center underline transition', activeStates.underline ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Garis Bawah (Underline)"
      >
        U
      </button>
      <button 
        type="button" 
        @click="format('strikeThrough')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center line-through transition', activeStates.strikeThrough ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Coret (Strikethrough)"
      >
        S
      </button>

      <div class="h-5 w-px bg-gray-200 mx-1"></div>

      <!-- Color Pickers -->
      <button 
        type="button" 
        @click="fontColorInput?.click()"
        class="p-1.5 hover:bg-gray-100 rounded-lg text-xs w-8 h-8 flex flex-col items-center justify-center transition"
        title="Warna Font"
      >
        <span class="font-bold text-gray-700 leading-none">A</span>
        <span class="w-4 h-1 mt-0.5 bg-[#143E2C] rounded-full"></span>
      </button>

      <button 
        type="button" 
        @click="highlightColorInput?.click()"
        class="p-1.5 hover:bg-gray-100 rounded-lg text-xs w-8 h-8 flex items-center justify-center transition"
        title="Warna Background Teks (Highlight)"
      >
        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M18.8 3c-.5 0-1 .2-1.4.6L12 8.9l-2.5-2.5c-.4-.4-.9-.6-1.4-.6s-1 .2-1.4.6L3.6 9.5c-.8.8-.8 2 0 2.8l8.1 8.1c.4.4.9.6 1.4.6s1-.2 1.4-.6l8.8-8.8c.8-.8.8-2 0-2.8l-3.1-3.2c-.4-.4-.9-.6-1.4-.6zm-5.7 13.9L8.3 12.1l7.1-7.1 4.8 4.8-7.1 7.1zM5 19h14v2H5v-2z"/>
        </svg>
      </button>

      <div class="h-5 w-px bg-gray-200 mx-1"></div>

      <!-- Lists -->
      <button 
        type="button" 
        @click="format('insertUnorderedList')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center transition', activeStates.insertUnorderedList ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Daftar Bullets"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
        </svg>
      </button>
      <button 
        type="button" 
        @click="format('insertOrderedList')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center transition', activeStates.insertOrderedList ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Daftar Angka"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
      </button>

      <!-- Indents -->
      <button 
        type="button" 
        @click="format('outdent')" 
        class="p-1.5 hover:bg-gray-100 rounded-lg text-xs w-8 h-8 flex items-center justify-center text-gray-600 transition"
        title="Kurangi Indent"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14v-6a2 2 0 00-2-2H9"></path>
        </svg>
      </button>
      <button 
        type="button" 
        @click="format('indent')" 
        class="p-1.5 hover:bg-gray-100 rounded-lg text-xs w-8 h-8 flex items-center justify-center text-gray-600 transition"
        title="Tambah Indent"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5v6a2 2 0 002 2h10"></path>
        </svg>
      </button>

      <div class="h-5 w-px bg-gray-200 mx-1"></div>

      <!-- Scripts -->
      <button 
        type="button" 
        @click="format('subscript')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center font-bold transition', activeStates.subscript ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Subscript (Bawah)"
      >
        x₂
      </button>
      <button 
        type="button" 
        @click="format('superscript')" 
        :class="['p-1.5 rounded-lg text-xs w-8 h-8 flex items-center justify-center font-bold transition', activeStates.superscript ? 'bg-[#143E2C]/10 text-[#143E2C]' : 'hover:bg-gray-100 text-gray-600']"
        title="Superscript (Atas)"
      >
        x²
      </button>

      <div class="h-5 w-px bg-gray-200 mx-1"></div>

      <!-- Links & Media -->
      <button 
        type="button" 
        @click="addLink" 
        class="p-1.5 hover:bg-gray-100 rounded-lg text-xs w-8 h-8 flex items-center justify-center text-gray-600 transition" 
        title="Tambah Link"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
        </svg>
      </button>
      <button 
        type="button" 
        @click="triggerImageUpload" 
        class="p-1.5 hover:bg-gray-100 rounded-lg text-xs w-8 h-8 flex items-center justify-center text-gray-600 transition" 
        title="Unggah Gambar Lokal"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
      </button>

    </div>

    <!-- Editable Content -->
    <div 
      ref="editorRef"
      contenteditable="true"
      @input="onInput"
      :placeholder="placeholder || 'Ketik di sini...'"
      class="p-6 min-h-[250px] focus:outline-none text-xs text-gray-800 leading-relaxed font-sans prose max-w-none editor-content"
      style="font-family: 'Roboto', 'Nunito Sans', sans-serif;"
    ></div>
  </div>
</template>

<style scoped>
.editor-content:empty::before {
  content: attr(placeholder);
  color: #9ca3af; /* gray-400 */
  cursor: text;
}

[contenteditable="true"] ul {
  list-style-type: disc;
  margin-left: 1.5rem;
}
[contenteditable="true"] ol {
  list-style-type: decimal;
  margin-left: 1.5rem;
}
[contenteditable="true"] img {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  margin: 1rem 0;
  display: block;
}
</style>
