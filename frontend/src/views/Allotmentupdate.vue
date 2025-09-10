<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-3xl font-extrabold">FinnTerest</h1>
      <AppButton variant="primary" @click="goBack">Back</AppButton>
    </header>

    <!-- Center card -->
    <main class="flex-1 grid place-items-center px-4">
      <div class="w-full max-w-2xl bg-[#547164] text-white rounded-lg p-8 shadow" style="border-radius:12px">
        <h2 class="text-2xl font-extrabold text-center mb-8">Update Allotment</h2>

        <!-- labels left, inputs right -->
        <form @submit.prevent="submit" class="mx-auto w-[88%]">
          <div class="grid grid-cols-[1fr_1.2fr] gap-y-4 gap-x-6 items-center">
            <label class="text-[15px] opacity-95">Allotment</label>
            <input
              v-model.number="form.allotment"
              type="number"
              min="1"
              step="1"
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
              placeholder="e.g., 500"
            />

            <label class="text-[15px] opacity-95">Description</label>
            <input
              v-model.trim="form.description"
              type="text"
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
              placeholder="Short note"
            />

            <label class="text-[15px] opacity-95">Start Date</label>
            <input
              v-model="form.start_date"
              type="date"
              class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
            />

            <label class="text-[15px] opacity-95">End Date</label>
            <input
              v-model="form.end_date"
              type="date"
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
              :disabled="loading"
            >
              {{ loading ? 'Saving…' : 'Proceed' }}
            </AppButton>
          </div>

          <!-- Messages -->
          <p v-if="error" class="mt-3 text-red-200 text-sm">{{ error }}</p>
          <p v-if="success" class="mt-3 text-green-200 text-sm">{{ success }}</p>
          <p v-if="remaining !== null" class="mt-2 text-white/80 text-sm">
            Remaining budget after this: <span class="font-semibold">{{ remaining }}</span>
          </p>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api' 

const router = useRouter()


const userId = ref<number | null>(null)

type AllotmentForm = {
  allotment: number | null
  description: string
  start_date: string
  end_date: string
}

const form = ref<AllotmentForm>({
  allotment: null,
  description: '',
  start_date: '',
  end_date: '',
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const remaining = ref<number | null>(null)

// get current user id 
onMounted(async () => {
  try {
    const { data } = await api.get('/api/me') 
    userId.value = data?.id ?? data?.user?.id ?? null
  } catch {
    userId.value = null
  }
})

function goBack() {
  router.back()
}

function clearForm() {
  form.value = { allotment: null, description: '', start_date: '', end_date: '' }
  error.value = ''
  success.value = ''
  remaining.value = null
}

function firstValidationError(b: any): string | null {
  if (b?.errors && typeof b.errors === 'object') {
    const key = Object.keys(b.errors)[0]
    if (key && Array.isArray(b.errors[key]) && b.errors[key][0]) return b.errors[key][0]
  }
  return b?.error || b?.message || null
}

async function submit() {
  error.value = ''
  success.value = ''
  remaining.value = null

  if (!userId.value) {
    error.value = 'Not authenticated. Please log in again.'
    return router.push({ name: 'login', query: { redirect: '/allotments/update' } })
  }

  if (!form.value.allotment || form.value.allotment < 1) {
    error.value = 'Allotment must be a positive integer.'
    return
  }
  if (!form.value.description) {
    error.value = 'Description is required.'
    return
  }
  if (!form.value.start_date || !form.value.end_date) {
    error.value = 'Start and end dates are required.'
    return
  }

  loading.value = true
  try {
    const payload = {
      user_id: userId.value,
      allotment: form.value.allotment,
      description: form.value.description,
      start_date: form.value.start_date,
      end_date: form.value.end_date,
    }

    const { data, status } = await api.post('/api/allotments', payload)

    // 201 expected on success
    if (status === 201) {
      success.value = data?.message || 'Allotment created successfully.'
      remaining.value = data?.remaining_budget ?? null
      
    } else {
      success.value = 'Allotment created.'
    }
  } catch (e: any) {
    error.value =
      firstValidationError(e?.response?.data) ||
      e?.message ||
      'Failed to create allotment.'
  } finally {
    loading.value = false
  }
}
</script>
