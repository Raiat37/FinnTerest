<template>
  <div class="space-y-6">
    <!-- To Taka -->
    <section>
      <h4 class="bg-[#547164] text-white px-4 py-2 rounded-t-lg font-semibold">To Taka</h4>
      <div class="bg-[#e7e7e9] rounded-b-lg p-4 space-y-3">
        <div class="flex items-center gap-3">
          <span class="w-24">Dollar</span>
          <input v-model.number="toTaka.dollar" type="number" step="0.0001" min="0"
                 class="flex-1 rounded px-3 py-2 bg-white" />
          <button class="bg-[#4b5a6a] text-white px-4 py-2 rounded hover:opacity-90"
                  @click="postRate('dollar')">
            Post
          </button>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-24">Pound</span>
          <input v-model.number="toTaka.pound" type="number" step="0.0001" min="0"
                 class="flex-1 rounded px-3 py-2 bg-white" />
          <button class="bg-[#4b5a6a] text-white px-4 py-2 rounded hover:opacity-90"
                  @click="postRate('pound')">
            Post
          </button>
        </div>
      </div>
    </section>

    <!-- From Taka (derived as 1 / rate) -->
    <section>
      <h4 class="bg-[#547164] text-white px-4 py-2 rounded-t-lg font-semibold">From Taka</h4>
      <div class="bg-[#e7e7e9] rounded-b-lg p-4 space-y-3">
        <div class="flex items-center gap-3">
          <span class="w-24">Dollar</span>
          <input :value="formatInverse(toTaka.dollar)" type="text" disabled
                 class="flex-1 rounded px-3 py-2 bg-white/70 text-gray-700" />
          <button class="bg-[#4b5a6a] text-white px-4 py-2 rounded opacity-60 cursor-not-allowed">
            Post
          </button>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-24">Pound</span>
          <input :value="formatInverse(toTaka.pound)" type="text" disabled
                 class="flex-1 rounded px-3 py-2 bg-white/70 text-gray-700" />
          <button class="bg-[#4b5a6a] text-white px-4 py-2 rounded opacity-60 cursor-not-allowed">
            Post
          </button>
        </div>
      </div>
    </section>

    <p v-if="message" class="text-center font-medium"
       :class="ok ? 'text-green-700' : 'text-red-700'">
      {{ message }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/lib/api'

type Rates = { dollar: number; pound: number }

const toTaka = ref<Rates>({ dollar: 0, pound: 0 })
const message = ref('')
const ok = ref(true)

const formatInverse = (v?: number) => {
  const n = Number(v || 0)
  if (!n) return '—'
  return (1 / n).toFixed(6) // 1 BDT in USD/GBP
}

async function loadRates() {
  try {
    const { data } = await api.get('/api/admin/exchange-rates')
    const r = data?.rates || {}
    toTaka.value.dollar = Number(r.dollar ?? 0)
    toTaka.value.pound  = Number(r.pound ?? 0)
  } catch (e) {
    console.error(e)
  }
}

async function postRate(currency: 'dollar' | 'pound') {
  message.value = ''
  try {
    const rate = toTaka.value[currency]
    await api.post('/api/admin/exchange-rates', { currency, rate })
    ok.value = true
    message.value = `Updated ${currency} to Taka rate`
  } catch (e: any) {
    ok.value = false
    message.value = e?.response?.data?.message || 'Failed to update rate'
  }
}

onMounted(loadRates)
</script>
