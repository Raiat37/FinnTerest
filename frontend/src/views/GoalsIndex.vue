<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-2xl font-extrabold">FinnTerest</h1>
      <router-link
        class="bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-100"
        to="/"
      >
        Back
      </router-link>
    </header>

    <!-- Center card -->
    <main class="flex-1 grid place-items-center px-4 py-10">
      <div class="w-full max-w-3xl">
        <section
          class="bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-blue-400"
          style="border-radius:12px"
        >
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-extrabold">My Goals</h2>
            <router-link
              to="/goals/add"
              class="inline-flex items-center gap-2 bg-white text-[#547164] px-4 py-2 rounded-md font-semibold hover:bg-gray-100"
            >
              Add New
            </router-link>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="bg-white/10 rounded-md p-4">
            Loading goals…
          </div>

          <!-- Error -->
          <div v-else-if="error" class="bg-red-600/80 rounded-md p-4">
            {{ error }}
          </div>

          <!-- Empty -->
          <div v-else-if="goals.length === 0" class="bg-white/10 rounded-md p-6 text-center">
            <p class="font-semibold mb-2">No goals yet</p>
            <p class="opacity-80 mb-4">Create your first goal to get started.</p>
            <router-link
              to="/goals/add"
              class="inline-flex items-center gap-2 bg-white text-[#547164] px-4 py-2 rounded-md font-semibold hover:bg-gray-100"
            >
              Create Goal
            </router-link>
          </div>

          <!-- List -->
          <ul v-else class="space-y-4">
            <li
              v-for="g in goals"
              :key="g.id"
              class="bg-white text-[#1d1d1f] rounded-md p-4 shadow-sm"
            >
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-lg font-bold">{{ g.name }}</h3>
                  <p v-if="g.description" class="text-sm opacity-80 mt-1">
                    {{ g.description }}
                  </p>
                  <p class="text-sm mt-2">
                    <span class="font-semibold">Start:</span> {{ fmt(g.start_date) }}
                    &nbsp;•&nbsp;
                    <span class="font-semibold">End:</span> {{ fmt(g.end_date) }}
                  </p>
                  <p v-if="g.status" class="text-sm mt-1">
                    <span class="font-semibold">Status:</span> {{ g.status }}
                  </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                  <!-- Show mark complete for pending or non-complete goals -->
                  <button
                    v-if="g.status !== 'complete'"
                    @click="markComplete(g)"
                    :disabled="!!completing[g.id]"
                    class="bg-[#547164] text-white px-3 py-1.5 rounded-md font-semibold hover:opacity-90 disabled:opacity-50"
                  >
                    {{ completing[g.id] ? 'Saving…' : 'Mark complete' }}
                  </button>

                  <!-- Completed indicator -->
                  <span v-else class="px-3 py-1.5 rounded-md bg-green-100 text-green-800 font-semibold">Completed</span>
                </div>
              </div>
            </li>
          </ul>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
// Adjust this import to however you import your existing api.ts elsewhere.
// Example: if you currently do `import api from '@/api'`, keep it consistent.
import api from '@/lib/api' // <-- use your api.ts wrapper (not axios)

import { ref, onMounted } from 'vue'

type Goal = {
  id: number
  name: string
  description?: string | null
  start_date: string
  end_date: string
  status?: string | null
}

const goals = ref<Goal[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

// Track per-goal completion loading state
const completing = ref<Record<number, boolean>>({})

const fetchGoals = async () => {
  loading.value = true
  error.value = null
  try {
    // Your Laravel route is Route::get('/goals', ...).
    // In most setups this is exposed under /api/goals (api.php).
    // If your api.ts already prefixes /api, keep it as below.
    const res = await api.get('/api/goals')
    // Expecting either { goals: [...] } or just [...]
    goals.value = Array.isArray(res.data) ? res.data : (res.data.goals ?? [])
  } catch (e: any) {
    error.value = e?.message || 'Failed to load goals.'
  } finally {
    loading.value = false
  }
}

const fmt = (d: string) => {
  try {
    const dt = new Date(d)
    return dt.toLocaleDateString()
  } catch {
    return d
  }
}

async function markComplete(goal: Goal) {
  // guard
  if (!goal || !goal.id) return

  // Avoid double-submission
  completing.value = { ...completing.value, [goal.id]: true }
  error.value = null

  try {
    const payload = { status: 'complete' }
    const res = await api.patch(`/api/goals/${goal.id}/status`, payload)
    // Update local list: set status and optionally completion_date if returned
    const updated = res.data?.goal ?? res.data
    // If API returned updated object, use it; otherwise set status locally
    if (updated && typeof updated === 'object' && updated.id === goal.id) {
      const idx = goals.value.findIndex(g => g.id === goal.id)
      if (idx !== -1) goals.value[idx] = { ...goals.value[idx], ...updated }
    } else {
      const idx = goals.value.findIndex(g => g.id === goal.id)
      if (idx !== -1) goals.value[idx].status = 'complete'
    }
  } catch (e: any) {
    error.value = e?.response?.data?.message || e?.message || 'Failed to update goal status.'
  } finally {
    completing.value = { ...completing.value, [goal.id]: false }
  }
}

onMounted(fetchGoals)
</script>
