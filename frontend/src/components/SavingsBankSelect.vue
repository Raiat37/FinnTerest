<template>
  <div class="space-y-1">
    <label class="block text-sm font-medium">Choose Bank</label>
    <select
      class="w-full rounded-lg border p-2 bg-white text-black"
      v-model="model"
      :disabled="disabled || loading"
    >
      <option value="" disabled>Select a bank</option>
      <option v-for="b in banks" :key="b.id" :value="b.id" class="text-black">
        {{ b.name }}
      </option>
    </select>
    <p v-if="loading" class="text-sm opacity-70">Loading banks…</p>
    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import api from '@/lib/api';

interface Bank { id:number; name:string; website_url:string }

const props = defineProps<{
  modelValue: number | null;
  disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue', 'loaded']);

const banks = ref<Bank[]>([]);
const loading = ref(false);
const error   = ref<string | null>(null);

const model = ref<number | null>(props.modelValue ?? null);
watch(model, v => emit('update:modelValue', v));
watch(() => props.modelValue, v => model.value = v ?? null);

onMounted(async () => {
  loading.value = true;
  error.value   = null;
  try {
    const { data } = await api.get<Bank[]>('/api/banks');
    banks.value = data;
    emit('loaded', data);
  } catch (e:any) {
    error.value = e?.response?.data?.message || 'Failed to load banks';
  } finally {
    loading.value = false;
  }
});
</script>
