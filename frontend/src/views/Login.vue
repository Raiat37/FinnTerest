<template>
  <StockRibbon />
  <div
    class="fixed inset-0 grid grid-cols-[1fr_1px_1fr] items-center justify-items-center p-12 bg-[oklch(92.951%_0.002_17.197)] z-10 overflow-auto font-montserrat"
  >
    <!-- left panel -->
    <div
      class="bg-[oklch(56.273%_0.054_154.39)] rounded-xl px-8 py-9 text-white w-[55%] max-w-[760px] min-w-[320px] max-h-[76vh] flex flex-col justify-center items-start"
    >
      <div class="flex items-center justify-start">
        <h1 class="text-4xl font-bold text-gray-100">FinnTerest</h1>
      </div>

      <form
        class="bg-gray-300 p-5 rounded-xl text-gray-900 w-full font-bold"
        @submit.prevent="doLogin"
        novalidate
      >
        <label for="email" class="block mt-2.5 mb-1.5 font-bold">Email</label>
        <input
          id="email"
          v-model="email"
          type="email"
          autocomplete="email"
          required
          class="w-full py-3 px-4 rounded-lg border border-gray-200 bg-white outline-none focus:border-gray-400"
        />

        <label for="password" class="block mt-2.5 mb-1.5 font-bold">Password</label>
        <input
          id="password"
          v-model="password"
          type="password"
          autocomplete="current-password"
          required
          class="w-full py-3 px-4 rounded-lg border border-gray-200 bg-white outline-none focus:border-gray-400"
        />

        <div class="flex flex-col items-center gap-2 mt-3.5">
          <button
            type="submit"
            :disabled="loading"
            class="py-2.5 px-6 rounded-lg bg-[#556070] text-white border-none cursor-pointer text-[15px] font-bold min-w-[160px] disabled:opacity-70"
          >
            <span v-if="!loading">Login</span>
            <span v-else>Logging in…</span>
          </button>

          <div class="mt-0">
            <router-link to="/register" class="text-gray-900 no-underline font-semibold">
              New here? Create an account
            </router-link>
          </div>
        </div>
      </form>
    </div>

    <!-- divider -->
    <div class="w-px bg-black/15 h-[60vh] justify-self-center"></div>

    <!-- right panel -->
    <div
      class="bg-[oklch(24.155%_0.049_89.07)] text-white rounded-2xl px-12 py-14 w-[75%] max-w-[1100px] min-w-[420px] max-h-[86vh] flex flex-col justify-center items-start"
    >
      <h2 class="text-3xl mb-3.5 font-bold">About us</h2>
      <p class="max-w-[620px] text-base leading-relaxed">
        FinnTerest is an application to track expenses, generate spending insights, and suggest savings strategies using data patterns.
        FinnTerest aims at being an efficient income-expenditure tracker to track and manage the finance needs of an individual at all stages of life.
      </p>

      <div class="grid grid-cols-2 gap-x-64 gap-y-10 mt-8 px-35">
        <div class="flex flex-col items-center">
          <div class="text-6xl mb-3">
            <!-- Dollar SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 1v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12 21v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M17 5.5A3.5 3.5 0 0 0 12 2a3.5 3.5 0 0 0-5 3.5c0 3 5 3.5 5 6.5s-5 3.5-5 6.5A3.5 3.5 0 0 0 12 22a3.5 3.5 0 0 0 5-3.5c0-3-5-3.5-5-6.5s5-3.5 5-6.5z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="opacity-95 text-sm">Manage budget</div>
        </div>
        <div class="flex flex-col items-center">
          <div class="text-6xl mb-3">
            <!-- Filter SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M3 5h18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              <path d="M6 12h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              <path d="M10 19h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="opacity-95 text-sm">Filter expenses</div>
        </div>
        <div class="flex flex-col items-center">
          <div class="text-6xl mb-3">
            <!-- Graph SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M3 17h18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              <path d="M6 13v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              <path d="M12 9v8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              <path d="M18 5v12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="opacity-95 text-sm">Generate Graphs</div>
        </div>
        <div class="flex flex-col items-center">
          <div class="text-6xl mb-3">
            <!-- File/PDF SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14 2v6h6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="opacity-95 text-sm">Show PDFs</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import StockRibbon from '../components/StockRibbon.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

type LoginResp = {
  token?: string
  user?: { id: number; name: string; role?: 'admin' | 'user' }
}

async function doLogin () {
  error.value = ''
  loading.value = true
  try {
    const { data } = await api.post<LoginResp>('/api/login', {
      email: email.value,
      password: password.value,
    })

    // store token (if you use tokens)
    if (data.token) {
      localStorage.setItem('token', data.token)
      // set default header for the api instance so subsequent requests use the token immediately
      api.defaults.headers.common.Authorization = `Bearer ${data.token}`
    }
    
    // 🔑 store role & redirect by role
    const role = data.user?.role || 'user'
    localStorage.setItem('role', role)
    router.replace({ name: role === 'admin' ? 'admin.dashboard' : 'dashboard' })
  } catch (e: any) {
  error.value = e?.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>
