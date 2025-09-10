<template>
  <div class="space-y-6">
    <section>
      <h4 class="bg-[#547164] text-white px-4 py-2 rounded-t-lg font-semibold">Exchange Rates (to BDT)</h4>
      <div class="bg-[#e7e7e9] rounded-b-lg p-4 space-y-3">
        <div v-for="(rate, code) in rates" :key="code" class="flex items-center gap-3">
          <span class="w-28 font-semibold">{{ code }}</span>
          <input v-model.number="rates[code]" type="number" step="0.000001" min="0" class="flex-1 rounded px-3 py-2 bg-white" />
          <button @click="postRate(code)" class="bg-[#4b5a6a] text-white px-4 py-2 rounded hover:opacity-90">Save</button>
        </div>
        <p v-if="!Object.keys(rates).length" class="text-center text-sm text-gray-600">No rates loaded yet.</p>
      </div>
    </section>

    <section>
      <h4 class="bg-[#547164] text-white px-4 py-2 rounded-t-lg font-semibold">Derived (1 / rate)</h4>
      <div class="bg-[#e7e7e9] rounded-b-lg p-4 space-y-3">
        <div v-for="(rate, code) in rates" :key="code + '-inv'" class="flex items-center gap-3">
          <span class="w-28 font-semibold">{{ code }}</span>
          <input :value="inverse(rate)" type="text" disabled class="flex-1 rounded px-3 py-2 bg-white/70 text-gray-700" />
          <button class="bg-[#4b5a6a] text-white px-4 py-2 rounded opacity-60 cursor-not-allowed">—</button>
        </div>
      </div>
    </section>

    <p v-if="message" class="text-center font-medium" :class="ok ? 'text-green-700' : 'text-red-700'">{{ message }}</p>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import api from '@/lib/api'

type RateMap = Record<string, number>

const rates = reactive<RateMap>({})
const message = ref('')
const ok = ref(true)

function inverse(r: number) {
  const n = Number(r || 0)
  return n ? (1 / n).toFixed(6) : '—'
}

async function loadRates() {
  try {
    const { data } = await api.get('/api/admin/exchange-rates')
    const r: RateMap = data?.rates || {}
    // API returns lowercase keys (e.g. "usd"), convert to uppercase for display
    for (const k of Object.keys(r)) {
      rates[k.toUpperCase()] = Number(r[k])
    }
  } catch (e) {
    console.error('Failed loading rates', e)
    message.value = 'Failed to load rates'
    ok.value = false
  }
}

async function postRate(code: string) {
  message.value = ''
  try {
    // send the currency code (DB/controller will canonicalize) and the numeric rate
    const rate = Number(rates[code])
    await api.post('/api/admin/exchange-rates', { currency: code, rate })
    ok.value = true
    message.value = `Updated ${code} rate`
  } catch (e: any) {
    ok.value = false
    message.value = e?.response?.data?.message || e?.message || 'Failed to update rate'
  }
}

onMounted(loadRates)
</script>
