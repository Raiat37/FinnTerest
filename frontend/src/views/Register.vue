<template>
  <div class="min-h-screen bg-[oklch(97.5%_0.003_83.24)] flex flex-col">
    <!-- Top bar (same as other pages) -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-3xl font-extrabold">FinnTerest</h1>
      <AppButton variant="primary" @click="goBack">Back</AppButton>
    </header>

    <!-- Registration card -->
    <main class="flex-1 grid place-items-center px-4 py-8">
      <div
        class="w-full max-w-2xl bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-blue-400"
        style="border-radius:12px"
      >
        <h2 class="text-2xl font-extrabold text-center mb-8">Registration</h2>

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
            <label class="block mb-1 font-semibold">Password</label>
            <input v-model="form.password" type="password" required minlength="8"
              class="w-full px-3 py-2 rounded bg-white text-black" />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" required minlength="8"
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

          <div class="mt-6 text-center">
            <AppButton type="submit" variant="primary" :disabled="loading">
              {{ loading ? 'Creating…' : 'Create Account' }}
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api' // axios instance (withCredentials: false)

const router = useRouter()
const loading = ref(false)
const error = ref('')
const success = ref('')

type RegisterForm = {
  name: string
  email: string
  password: string
  password_confirmation: string
  job: string | null
  salary: number | null
}

const form = ref<RegisterForm>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  job: null,
  salary: null,
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

async function submit() {
  error.value = ''
  success.value = ''

  // simple frontend check
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Password confirmation does not match.'
    return
  }

  loading.value = true
  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      job: form.value.job || null,
      salary: form.value.salary ?? null,
      role: 'user',
    }

    const res = await api.post('/api/register', payload)

    // If your API returns a token and you want auto-login, uncomment:
    // if (res.data?.token) {
    //   localStorage.setItem('token', res.data.token)
    //   api.defaults.headers.common.Authorization = `Bearer ${res.data.token}`
    //   return router.push('/dashboard')
    // }

    success.value = 'Account created successfully. Redirecting to login…'
    setTimeout(() => router.push('/login'), 900)
  } catch (e: any) {
    const msg = firstValidationError(e?.response?.data) || e?.message || 'Registration failed'
    error.value = msg
  } finally {
    loading.value = false
  }
}
</script>

<style>
.register-root {
  min-height: 100vh;
  background: oklch(97.5% 0.003 83.24);
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #547164;
  margin-bottom: 2rem;
}

.header h1 {
  color: white;
  font-size: 2rem;
  font-weight: bold;
  margin: 0;
}

.register-card {
  width: 100%;
  max-width: 600px;
  background: #547164;
  padding: 2rem;
  border-radius: 8px;
  color: white;
}

.register-card h2 {
  text-align: center;
  margin-bottom: 2rem;
  font-size: 1.5rem;
  font-weight: 600;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  background: white;
  color: #333;
}

.actions {
  margin-top: 2rem;
  text-align: center;
}

.error {
  margin-top: 1rem;
  color: #ff6b6b;
  background: rgba(255, 107, 107, 0.1);
  padding: 0.75rem;
  border-radius: 4px;
  text-align: center;
}

.success {
  margin-top: 1rem;
  color: #51cf66;
  background: rgba(81, 207, 102, 0.1);
  padding: 0.75rem;
  border-radius: 4px;
  text-align: center;
}
</style>
