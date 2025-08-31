<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-3xl font-extrabold">FinnTerest</h1>

      <nav class="flex items-center gap-3">
        <!-- EDIT PROFILE (replaces top-bar Savings) -->
        <button
          class="bg-white text-[#547164] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100"
          @click="goEditProfile"
        >
          Edit Profile
        </button>

        <button
          class="bg-white text-[#547164] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100"
          @click="go('CalcAndConverter')"
        >
          Calculator
        </button>

        <button
          class="bg-[#3a3a06] text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90"
          @click="logout"
        >
          Logout
        </button>
      </nav>
    </header>

    <!-- Content -->
    <main class="flex-1 px-6 py-8">
      <h2 class="text-2xl font-extrabold text-[#1d1d1f] mb-6">
        Welcome {{ userName }}!
      </h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left stats card -->
        <section class="bg-white rounded-lg p-8 shadow ring-1 ring-black/5">
          <dl class="space-y-6">
            <div class="flex items-center justify-between">
              <dt class="text-lg font-semibold text-[#1d1d1f]">Current Salary:</dt>
              <dd class="text-xl font-extrabold tracking-wide">
                {{ formatBDT(salary) }} BDT
              </dd>
            </div>

            <div class="flex items-center justify-between">
              <dt class="text-lg font-semibold text-[#1d1d1f]">Current Expenses:</dt>
              <dd class="text-xl font-extrabold tracking-wide">
                {{ formatBDT(expenses) }} BDT
              </dd>
            </div>

            <div class="flex items-center justify-between">
              <dt class="text-lg font-semibold text-[#1d1d1f]">Current Budget:</dt>
              <dd class="text-xl font-extrabold tracking-wide">
                {{ formatBDT(budget) }} BDT
              </dd>
            </div>
          </dl>
        </section>

        <!-- Right actions panel -->
        <section class="bg-[#f6f6f6] rounded-lg p-6 shadow ring-1 ring-black/5">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Row 1 -->
            <div class="flex flex-col gap-2">
              <span class="text-sm font-semibold text-[#1d1d1f]">Update Budget</span>
              <button
                class="h-10 rounded-md bg-[#4b5a6a] text-white font-semibold hover:opacity-90"
                @click="go('budget')"
                aria-label="Update Budget"
              >
                <span class="inline-block text-lg">></span>
              </button>
            </div>

            <div class="flex flex-col gap-2">
              <span class="text-sm font-semibold text-[#1d1d1f]">Update Allotment</span>
              <button
                class="h-10 rounded-md bg-[#4b5a6a] text-white font-semibold hover:opacity-90"
                @click="go('allotments.update')"
                aria-label="Update Allotment"
              >
                <span class="inline-block text-lg">></span>
              </button>
            </div>

            <!-- CHANGED: “Update Expenses” → “Savings” (moved downwards) -->
            <div class="flex flex-col gap-2">
              <span class="text-sm font-semibold text-[#1d1d1f]">Savings</span>
              <button
                class="h-10 rounded-md bg-[#4b5a6a] text-white font-semibold hover:opacity-90"
                @click="go('savings')"
                aria-label="Savings"
              >
                <span class="inline-block text-lg">></span>
              </button>
            </div>

            <!-- Row 2 -->
            <div class="flex flex-col gap-2">
              <span class="text-sm font-semibold text-[#1d1d1f]">See Graph</span>
              <button
                class="h-10 rounded-md bg-[#3a2f08] text-white font-semibold hover:opacity-90"
                @click="go('spending')"
                aria-label="See Graph"
              >
                <span class="inline-block text-lg">></span>
              </button>
            </div>

            <div class="flex flex-col gap-2">
              <span class="text-sm font-semibold text-[#1d1d1f]">My Goals</span>
              <button
                class="h-10 rounded-md bg-[#3a2f08] text-white font-semibold hover:opacity-90"
                @click="go('goals.index')"
                aria-label="My Goals"
              >
                <span class="inline-block text-lg">></span>
              </button>
            </div>

            <div class="flex flex-col gap-2">
              <span class="text-sm font-semibold text-[#1d1d1f]">Export PDF</span>
              <button
                class="h-10 rounded-md bg-[#3a2f08] text-white font-semibold hover:opacity-90"
                @click="exportPdf"
                aria-label="Export PDF"
              >
                <span class="inline-block text-lg">></span>
              </button>
            </div>
          </div>
        </section>
      </div>

      <!-- Banner / quote -->
      <section class="mt-8 bg-[#2a1f00] text-white rounded-lg p-8 shadow ring-1 ring-black/5">
        <p class="text-xl font-semibold mb-4">Thanks for joining FinnTerest!</p>
        <p class="italic text-lg leading-relaxed">
          “A budget is telling your money where to go, instead of wondering where it went”
          <br />
          <span class="not-italic">-Dave Ramsey</span>
        </p>
      </section>
      <section class="bg-white rounded-lg p-6 shadow ring-1 ring-black/5 mt-8">
        <h3 class="text-lg font-bold text-[#1d1d1f] mb-4">Top 5 Gainers</h3>
        <ul class="space-y-2">
          <li v-for="stock in top5Stocks" :key="stock['TRADING CODE']" class="flex justify-between">
            <span>{{ stock['TRADING CODE'] }}</span>
            <span>
              {{ parseFloat(stock['LTP*']).toFixed(2) }} BDT
              <span
                :class="parseFloat(stock.CHANGE) >= 0 ? 'text-green-600' : 'text-red-500'"
              >
                ({{ parseFloat(stock.CHANGE).toFixed(2) }})
              </span>
            </span>
          </li>
        </ul>
      </section>
    </main>
    <StockRibbon />
  </div>
</template>

<script setup lang="ts">
import StockRibbon from '../components/StockRibbon.vue'
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/api'

const router = useRouter()
const CACHE_KEY = 'approvedProfile'  // where we cache "last approved" profile

// fields used in the UI
const userName = ref('User')
const salary   = ref<number>(0)
const expenses = ref<number>(0)
const budget   = ref<number>(0)


//stocks
const stocks = ref([])

const fetchStockData = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/stock/latest')
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    const json = await response.json()
    stocks.value = json.data
  } catch (error) {
    console.error('Failed to fetch stock data:', error)
  }
}


const top5Stocks = computed(() => {
  return stocks.value
    .filter(s => s['LTP*'] && s.CHANGE) // only valid ones
    .sort((a, b) => parseFloat(b.CHANGE) - parseFloat(a.CHANGE)) // sort by gain
    .slice(0, 5) // take top 5
})




onMounted(() => {
  fetchStockData()
})



// formatter used in your templates
const formatBDT = (n: number) => new Intl.NumberFormat('en-BD').format(n) + ' BDT'

// types (optional)
type Profile = {
  name?: string
  email?: string
  job?: string | null
  salary?: number | null
  expenditure?: number | null
  budget?: number | null
  profile_pending_approval?: boolean
}

function useCacheGet(): Profile | null {
  try { return JSON.parse(localStorage.getItem(CACHE_KEY) || 'null') } catch { return null }
}
function useCacheSet(u: Profile) {
  const store: Profile = {
    name: u.name ?? '',
    email: u.email ?? '',
    job: u.job ?? null,
    salary: u.salary ?? 0,
    expenditure: u.expenditure ?? 0,
    budget: u.budget ?? 0,
  }
  localStorage.setItem(CACHE_KEY, JSON.stringify(store))
}

// Load profile then decide which values to show
onMounted(async () => {
  try {
    const { data } = await api.get('/api/me')
    const u: Profile = data?.user ?? {}

    if (u.profile_pending_approval) {
      // while pending, show the LAST APPROVED values from cache
      const cached = useCacheGet()
      if (cached) {
        applyToUI(cached)
      } else {
        // first ever edit (no cache yet): seed cache from current API,
        // then show those as "approved" until admin clears the flag
        useCacheSet(u)
        applyToUI(u)
      }
    } else {
      // not pending: accept new values as the "approved" source of truth
      applyToUI(u)
      useCacheSet(u)
    }
  } catch (e) {
    console.error('Failed to load /api/me:', e)
    // fallback: if API fails, try showing last approved from cache
    const cached = useCacheGet()
    if (cached) applyToUI(cached)
  }
})

function applyToUI(u: Profile) {
  userName.value = u.name ?? 'User'
  salary.value   = Number(u.salary ?? 0)
  expenses.value = Number(u.expenditure ?? 0)
  budget.value   = Number(u.budget ?? 0)
}

// NAV + actions (keep whatever you already had)
function logout () {
  // clear auth + approved cache (optional)
  localStorage.removeItem('token')
  sessionStorage.removeItem('token')
  localStorage.removeItem(CACHE_KEY)
  router.push({ name: 'login' })
}
function go (name: string) {
  const r = router.getRoutes().find(r => r.name === name || r.path === name)
  if (r?.name) router.push({ name: r.name })
  else if (r?.path) router.push({ path: r.path })
}
function goEditProfile () {
  const r = router.getRoutes().find(r => r.name === 'profile.edit' || r.path === '/profile/edit')
  if (r?.name) router.push({ name: r.name })
  else if (r?.path) router.push({ path: r.path })
}
async function exportPdf () {
  try {
    const res = await api.get('/api/export-pdf', { responseType: 'blob' })
    const blob = new Blob([res.data], { type: 'application/pdf' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'finnterest-summary.pdf'
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(url)
  } catch (e) {
    console.error('Export PDF failed:', e)
    alert('Could not export PDF yet.')
  }
}
</script>
