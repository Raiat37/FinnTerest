<template>
  <div class="min-h-screen bg-[oklch(97.5%_0.003_83.24)] flex flex-col">
    <!-- Top bar (same as other pages) -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-3xl font-extrabold">FinnTerest</h1>
      <AppButton variant="primary" @click="goBack">Back</AppButton>
    </header>

    <!-- Edit Profile card -->
    <main class="flex-1 grid place-items-center px-4 py-8">
      <div
        class="w-full max-w-2xl bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-blue-400"
        style="border-radius:12px"
      >
        <h2 class="text-2xl font-extrabold text-center mb-8">Edit Profile</h2>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block mb-1 font-semibold">User Name</label>
            <input v-model.trim="form.name" type="text" required
              class="w-full px-3 py-2 rounded bg-white text-black" />
          </div>

          <div>
            <label class="block mb-1 font-semibold">E-Mail ID</label>
            <input v-model.trim="form.email" type="email" required
              class="w-full px-3 py-2 rounded bg-white text-black" />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Job</label>
            <input v-model.trim="form.job" type="text"
              class="w-full px-3 py-2 rounded bg-white text-black" />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Current Salary</label>
            <input v-model.number="form.salary" type="number" min="0"
              class="w-full px-3 py-2 rounded bg-white text-black" />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Current Expenditure</label>
            <input v-model.number="form.expenditure" type="number" min="0"
              class="w-full px-3 py-2 rounded bg-white text-black" />
          </div>

          <div class="mt-6 text-center">
            <AppButton type="submit" variant="primary" :disabled="loading">
              {{ loading ? 'Saving…' : 'Save Changes' }}
            </AppButton>
          </div>
        </form>

        <div v-if="error" class="mt-4 text-center text-red-400 font-medium">{{ error }}</div>
        <div v-if="success" class="mt-4 text-center text-green-400 font-medium">{{ success }}</div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const success = ref('')

type EditForm = {
  name: string
  email: string
  job: string | null
  salary: number | null
  expenditure: number | null
}

const form = ref<EditForm>({
  name: '',
  email: '',
  job: null,
  salary: null,
  expenditure: null,
})

function goBack() {
  router.back()
}

function firstValidationError(b: any): string | null {
  if (b?.errors && typeof b.errors === 'object') {
    const k = Object.keys(b.errors)[0]
    if (k && Array.isArray(b.errors[k]) && b.errors[k][0]) return b.errors[k][0]
  }
  return b?.message ?? null
}

// Pre-fill with current values from /api/me
onMounted(async () => {
  try {
    const { data } = await api.get('/api/me')
    const u = data?.user ?? {}
    form.value.name        = u.name ?? ''
    form.value.email       = u.email ?? ''
    form.value.job         = u.job ?? null
    form.value.salary      = u.salary ?? null
    form.value.expenditure = u.expenditure ?? null
  } catch (e) {
    console.error('Failed to load profile:', e)
  }
})

async function submit() {
  error.value = ''
  success.value = ''
  loading.value = true
  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
      job: form.value.job,
      salary: form.value.salary,
      expenditure: form.value.expenditure,
    }

    await api.post('/api/profile/update', payload)

    success.value = 'Profile update submitted for approval.'
    // optional redirect after few seconds
    setTimeout(() => router.push('/dashboard'), 1200)
  } catch (e: any) {
    const msg = firstValidationError(e?.response?.data) || e?.message || 'Update failed'
    error.value = msg
  } finally {
    loading.value = false
  }
}
</script>
