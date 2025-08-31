<!-- src/views/CalcAndConverter.vue -->
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
        class="w-full max-w-3xl bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-blue-400"
        style="border-radius:12px"
      >
        <h2 class="text-2xl font-extrabold text-center mb-8">Calculator & Currency Converter</h2>

        <!-- Two sections side by side (stack on mobile) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- BODMAS Calculator -->
          <section>
            <h3 class="text-lg font-bold mb-4">Simple Calculator</h3>
            <div class="space-y-4">
              <input
                v-model="expression"
                @keydown.enter.prevent="doCalculate"
                type="text"
                placeholder="e.g., (10+20)/5"
                class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
              />

              <div class="flex gap-3">
                <AppButton :disabled="calcLoading || !expression.trim()" @click="doCalculate">= Evaluate</AppButton>
                <AppButton variant="secondary" @click="clearCalc">Clear</AppButton>
              </div>

              <p v-if="calcError" class="text-red-200 text-sm">{{ calcError }}</p>

              <div class="mt-2">
                <p class="text-sm opacity-90">Result</p>
                <p class="text-2xl font-semibold tabular-nums">
                  {{ calcResultDisplay }}
                </p>
              </div>
            </div>
          </section>

          <!-- Currency Converter -->
          <section>
            <h3 class="text-lg font-bold mb-4">Currency Converter</h3>
            <div class="space-y-4">
              <input
                v-model.number="amount"
                type="number"
                step="any"
                placeholder="Enter amount"
                class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
              />

              <div class="grid grid-cols-2 gap-4">
                <select
                  v-model="from"
                  class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
                >
                  <option v-for="c in codes" :key="c" :value="c">{{ c }}</option>
                </select>

                <select
                  v-model="to"
                  class="rounded-md bg-white text-gray-800 px-4 py-2.5 outline-none w-full"
                >
                  <option v-for="c in codes" :key="c" :value="c">{{ c }}</option>
                </select>
              </div>

              <div class="flex gap-3">
                <AppButton :disabled="convLoading || amount === null" @click="doConvert">Convert</AppButton>
                <AppButton variant="secondary" @click="clearConv">Reset</AppButton>
              </div>

              <p v-if="convError" class="text-red-200 text-sm">{{ convError }}</p>

              <div class="mt-2">
                <p class="text-sm opacity-90">Converted</p>
                <p class="text-2xl font-semibold tabular-nums">
                  {{ convResultDisplay }}
                </p>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api'

const router = useRouter()
function goBack() { router.back() }

/* ---------- Calculator ---------- */
const expression = ref('')
const calcLoading = ref(false)
const calcError = ref<string | null>(null)
const calcResult = ref<number | null>(null)

const calcResultDisplay = computed(() =>
  calcResult.value !== null ? String(calcResult.value) : '—'
)

async function doCalculate() {
  calcError.value = null
  calcLoading.value = true
  calcResult.value = null
  try {
    const { data } = await api.post('/api/calculate', { expression: expression.value })
    calcResult.value = data.result
  } catch (e: any) {
    calcError.value = e?.response?.data?.error || e?.message || 'Invalid expression'
  } finally {
    calcLoading.value = false
  }
}
function clearCalc() {
  expression.value = ''
  calcResult.value = null
  calcError.value = null
}

/* ---------- Converter ---------- */
const codes = ['BDT', 'USD', 'GBP', 'EUR', 'INR']
const amount = ref<number | null>(null)
const from = ref<'BDT' | 'USD' | 'GBP' | 'EUR' | 'INR'>('BDT')
const to   = ref<'BDT' | 'USD' | 'GBP' | 'EUR' | 'INR'>('USD')

const convLoading = ref(false)
const convError = ref<string | null>(null)
const convResult = ref<number | null>(null)

const convResultDisplay = computed(() =>
  convResult.value !== null ? String(convResult.value) : '—'
)

async function doConvert() {
  convError.value = null
  convLoading.value = true
  convResult.value = null
  try {
    const { data } = await api.post('/api/convert', {
      amount: Number(amount.value ?? 0),
      from: from.value,
      to: to.value
    })
    convResult.value = data.result
  } catch (e: any) {
    convError.value = e?.response?.data?.error || e?.message || 'Conversion error'
  } finally {
    convLoading.value = false
  }
}
function clearConv() {
  amount.value = null
  from.value = 'BDT'
  to.value = 'USD'
  convError.value = null
  convResult.value = null
}
</script>
