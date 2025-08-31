<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-3xl font-extrabold">FinnTerest</h1>
      <AppButton variant="primary" @click="goBack">Back</AppButton>
    </header>

    <!-- center card -->
    <main class="flex-1 grid place-items-center px-4">
      <div
        class="w-full max-w-xl bg-[#547164] text-white rounded-lg p-8 shadow"
        style="border-radius:12px"
      >
        <h2 class="text-2xl font-extrabold text-center mb-8">Set Budget</h2>

        <div class="flex flex-col items-center gap-4">
          <input
            v-model.number="budget"
            type="number"
            min="0"
            step="0.01"
            class="w-[70%] max-w-md rounded-md bg-white text-gray-800 px-4 py-3 outline-none"
            :class="{'ring-2 ring-red-400': error}"
            placeholder="Enter amount"
          />

          <AppButton
            :disabled="loading || budget === null || String(budget) === ''"
            @click="submit"
            class="!min-w-[110px] !px-4 !py-2"
          >
            {{ loading ? 'Saving…' : 'Proceed' }}
          </AppButton>

          <p v-if="error" class="text-red-200 text-sm text-center mt-1">{{ error }}</p>
          <p v-if="success" class="text-green-200 text-sm text-center mt-1">{{ success }}</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api' // axios instance (withCredentials: false)

const router = useRouter()
const budget = ref<number | null>(null)
const loading = ref(false)
const error = ref('')
const success = ref('')

// CHANGE THIS if your route is not under /api
const UPDATE_BUDGET_ENDPOINT = '/api/update-budget' // if you registered Route::post in routes/api.php
// If your route is in routes/web.php without api prefix, use '/update-budget'

function goBack() {
  router.back()
}

async function submit() {
  error.value = ''
  success.value = ''
  if (budget.value === null || isNaN(budget.value) || budget.value < 0) {
    error.value = 'Please enter a valid non-negative amount.'
    return
  }

  loading.value = true
  try {
    await api.post(UPDATE_BUDGET_ENDPOINT, { budget: budget.value })
    success.value = 'Budget updated successfully'
    setTimeout(() => router.push('/dashboard'), 800)
  } catch (e: any) {
    error.value =
      e?.response?.data?.message ||
      e?.response?.data?.errors?.budget?.[0] ||
      'Failed to update budget.'
  } finally {
    loading.value = false
  }
}
</script>
