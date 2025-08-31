<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-4xl font-extrabold tracking-wide">FinnTerest</h1>
      <AppButton variant="primary" @click="goBack">Back</AppButton>
    </header>

    <main class="flex-1 grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-10 p-10">
      <!-- Left: Title + Chart -->
      <section class="flex flex-col items-center">
        <h2 class="text-3xl font-extrabold mb-6 text-[#1f2937]">Category wise spending chart</h2>

        <div class="bg-white rounded-xl shadow-md p-6 inline-flex items-center justify-center transform transition hover:scale-105">
          <!-- SVG Pie -->
          <svg :width="svgSize" :height="svgSize" :viewBox="`0 0 ${svgSize} ${svgSize}`" role="img" aria-label="Spending pie chart">
            <g :transform="`translate(${center} ${center})`">
              <circle :r="radius" fill="#f3f4f6" />

              <!-- Slices -->
              <template v-for="(seg, i) in segments" :key="i">
                <path
                  :d="arcPath(radius, seg.startAngle, seg.endAngle)"
                  :fill="seg.color"
                  stroke="#000"
                  stroke-width="2"
                />
              </template>

              <!-- Labels (skip very small slices to avoid overlap) -->
              <template v-for="(seg, i) in labeledSegments" :key="`label-${i}`">
                <g :transform="`translate(${seg.cx} ${seg.cy})`">
                  <!-- text with outline for contrast -->
                  <text
                    text-anchor="middle"
                    dominant-baseline="middle"
                    font-size="16"
                    font-weight="700"
                    fill="#111827"
                    
                  >
                    {{ seg.label }}
                  </text>
                </g>
              </template>
            </g>
          </svg>
        </div>
      </section>

      <!-- Right: Cards -->
      <aside class="flex flex-col gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 text-lg">
          <div class="flex items-center justify-between py-2">
            <div class="text-gray-700 font-semibold">Total Budget:</div>
            <div class="text-gray-900 font-bold text-xl">{{ fmtBDT(totalBudget) }}</div>
          </div>
          <div class="flex items-center justify-between py-2">
            <div class="text-gray-700 font-semibold">Current Budget:</div>
            <div class="text-gray-900 font-bold text-xl">{{ fmtBDT(remainingBudget) }}</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-lg">
          <h3 class="text-center font-bold text-gray-800 mb-3 text-xl">Active Allotment</h3>
          <div v-if="activeList.length === 0" class="text-center text-gray-500 text-base">None</div>
          <div v-for="row in activeList" :key="`a-${row.category}`" class="flex items-center justify-between py-2">
            <div class="text-gray-800">{{ row.category }}</div>
            <div class="text-gray-900 font-semibold">{{ fmtBDT(row.spent) }}</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-lg">
          <h3 class="text-center font-bold text-gray-800 mb-3 text-xl">Expired Allotment</h3>
          <div v-if="expiredList.length === 0" class="text-center text-gray-500 text-base">None</div>
          <div v-for="row in expiredList" :key="`e-${row.category}`" class="flex items-center justify-between py-2">
            <div class="text-gray-800">{{ row.category }}</div>
            <div class="text-gray-900 font-semibold">{{ fmtBDT(row.spent) }}</div>
          </div>
        </div>
      </aside>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppButton from '@/components/AppButton.vue'
import api from '@/lib/api'

const router = useRouter()
function goBack() { router.back() }
const fmtBDT = (n: number) => new Intl.NumberFormat('en-BD').format(n) + ' BDT'

type Item = { category: string; budget: number; spent: number; status: 'active' | 'overdue' }
const rows = ref<Item[]>([])
const totalBudget = computed(() => rows.value[0]?.budget ?? 0)
const totalSpent = computed(() => rows.value.reduce((s, r) => s + (r.spent || 0), 0))
const remainingBudget = computed(() => Math.max(0, totalBudget.value - totalSpent.value))
const activeList = computed(() => rows.value.filter(r => r.status === 'active'))
const expiredList = computed(() => rows.value.filter(r => r.status === 'overdue'))

onMounted(async () => {
  try {
    const { data } = await api.get('/api/category-spending')
    rows.value = Array.isArray(data) ? data : []
  } catch { rows.value = [] }
})

// SVG Pie settings
const svgSize = 520
const radius = 200
const labelRadius = 130 // where labels sit (slightly inside the slice)
const center = svgSize / 2
const COLOR_ACTIVE = '#facc15'   // yellow
const COLOR_EXPIRED = '#fb7185'  // red
const COLOR_REMAIN = '#10b981'   // green

type Part = { value: number; color: string; name: string }
type Segment = { startAngle: number; endAngle: number; color: string; name: string; value: number; percent: number }
const SMALL_PCT = 0.04 // hide labels under 4%

const parts = computed<Part[]>(() => {
  const arr: Part[] = []
  activeList.value.forEach(r => arr.push({ value: r.spent, color: COLOR_ACTIVE, name: r.category }))
  expiredList.value.forEach(r => arr.push({ value: r.spent, color: COLOR_EXPIRED, name: r.category }))
  const rem = remainingBudget.value
  if (rem > 0) arr.push({ value: rem, color: COLOR_REMAIN, name: 'Remaining' })
  return arr
})

const totalValue = computed(() => parts.value.reduce((s, p) => s + p.value, 0))

const segments = computed<Segment[]>(() => {
  const total = totalValue.value
  if (total <= 0) return []
  let acc = -Math.PI / 2
  const segs: Segment[] = []
  for (const p of parts.value) {
    const angle = (p.value / total) * Math.PI * 2
    const startAngle = acc
    const endAngle = acc + angle
    segs.push({
      startAngle,
      endAngle,
      color: p.color,
      name: p.name,
      value: p.value,
      percent: p.value / total
    })
    acc = endAngle
  }
  return segs
})

const labeledSegments = computed(() => {
  return segments.value
    .filter(s => s.percent >= SMALL_PCT)
    .map(s => {
      const mid = (s.startAngle + s.endAngle) / 2
      const cx = labelRadius * Math.cos(mid)
      const cy = labelRadius * Math.sin(mid)
      const pct = Math.round(s.percent * 100)
      return {
        cx, cy,
        label: `${s.name} ${pct}%`
      }
    })
})

function polarToCartesian(r: number, angle: number) {
  return { x: r * Math.cos(angle), y: r * Math.sin(angle) }
}
function arcPath(r: number, start: number, end: number) {
  if (Math.abs(end - start) >= Math.PI * 2 - 1e-6) {
    const mid = start + Math.PI
    const s = polarToCartesian(r, start)
    const m = polarToCartesian(r, mid)
    const e = polarToCartesian(r, end)
    return `M 0 0 L ${s.x} ${s.y} A ${r} ${r} 0 1 1 ${m.x} ${m.y} A ${r} ${r} 0 1 1 ${e.x} ${e.y} Z`
  }
  const s = polarToCartesian(r, start)
  const e = polarToCartesian(r, end)
  const largeArc = end - start > Math.PI ? 1 : 0
  return `M 0 0 L ${s.x} ${s.y} A ${r} ${r} 0 ${largeArc} 1 ${e.x} ${e.y} Z`
}
</script>
