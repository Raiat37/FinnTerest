<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-3xl font-extrabold">FinnTerest</h1>
      <AppButton variant="primary" @click="goBack">Back</AppButton>
    </header>

    <!-- Center card -->
    <main class="flex-1 grid place-items-center px-4">
      <div
        class="w-full max-w-2xl bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-blue-400"
        style="border-radius:12px"
      >
        <h2 class="text-2xl font-extrabold text-center mb-8">Add Goals</h2>

        <form @submit.prevent="submit" class="mx-auto w-[88%]">
          <div class="grid grid-cols-[1fr_1.2fr] gap-y-4 gap-x-6 items-center">
            <label class="text-[15px] opacity-95">Name</label>
            <input
              v-model.trim="form.name"
              type="text"
              required
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
              placeholder="Goal name"
            />

            <label class="text-[15px] opacity-95">Description</label>
            <input
              v-model.trim="form.description"
              type="text"
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
              placeholder="Optional"
            />

            <label class="text-[15px] opacity-95">Start Date</label>
            <input
              v-model="form.start_date"
              type="date"
              required
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
            />

            <label class="text-[15px] opacity-95">End Date</label>
            <input
              v-model="form.end_date"
              type="date"
              required
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
            />
          </div>

          <!-- Buttons -->
          <div class="mt-7 flex items-center gap-4">
            <AppButton
              type="button"
              variant="secondary"
              class="!min-w-[120px] !px-4 !py-2"
              @click="clearForm"
              :disabled="loading"
            >
              Add new
            </AppButton>

            <AppButton
              type="submit"
              class="!min-w-[120px] !px-4 !py-2"
              :disabled="loading || !form.name || !form.start_date || !form.end_date"
            >
              {{ loading ? 'Saving…' : 'Proceed' }}
            </AppButton>
          </div>

          <!-- Messages -->
          <p v-if="error" class="mt-3 text-red-200 text-sm">{{ error }}</p>
          <p v-if="success" class="mt-3 text-green-200 text-sm">{{ success }}</p>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api' // axios instance (withCredentials: false, Bearer header)

const router = useRouter()
const loading = ref(false)
const error = ref('')
const success = ref('')

type GoalForm = {
  name: string
  description: string
  start_date: string
  end_date: string
}

const form = ref<GoalForm>({
  name: '',
  description: '',
  start_date: '',
  end_date: '',
})

function goBack() {
  router.back()
}

function clearForm() {
  form.value = { name: '', description: '', start_date: '', end_date: '' }
  error.value = ''
  success.value = ''
}

function firstValidationError(b: any): string | null {
  if (b?.errors && typeof b.errors === 'object') {
    const k = Object.keys(b.errors)[0]
    if (k && Array.isArray((b.errors as any)[k]) && (b.errors as any)[k][0]) return (b.errors as any)[k][0]
  }
  return b?.message ?? null
}

async function submit() {
  error.value = ''
  success.value = ''

  // end >= start small guard
  if (form.value.start_date && form.value.end_date && form.value.end_date < form.value.start_date) {
    error.value = 'End date must be the same or after start date.'
    return
  }

  loading.value = true
  try {
    const payload = { ...form.value } // user_id comes from auth()->id() on server
    const { status, data } = await api.post('/api/goals', payload)
    if (status === 200 || status === 201) {
      success.value = data?.message || 'Goal created successfully.'
      // keep form so user can duplicate quickly; or clear after a short delay:
      setTimeout(clearForm, 800)
    } else {
      success.value = 'Goal created.'
    }
  } catch (e: any) {
    error.value =
      firstValidationError(e?.response?.data) ||
      e?.message ||
      'Failed to create goal.'
  } finally {
    loading.value = false
  }
}
</script>
