<template>
  <div class="stock-ribbon">
    <div class="ticker">
      <span v-for="(stock, index) in stocks" :key="index" class="mr-6">
        <strong>{{ stock['TRADING CODE'] }}:</strong>
        <span
          :class="parseFloat(stock.CHANGE) >= 0 ? 'text-green-400 font-semibold' : 'text-red-400 font-semibold'"
        >
          {{ parseFloat(stock.CHANGE) >= 0 ? '▲' : '▼' }}
          {{ stock['LTP*'] || 'N/A' }}
        </span>
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const stocks = ref([])

const fetchStockData = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/stock/latest')
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    const json = await response.json()
    console.log('✅ Stock API Response:', json)

    stocks.value = json.data
    console.log('Sample stock item:', json.data[0])
  } catch (error) {
    console.error('❌ Failed to fetch stock data:', error)
  }
}

onMounted(() => {
  fetchStockData()
  setInterval(fetchStockData, 60000)
})
</script>

<style scoped>
.stock-ribbon {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: #1e1e1e;
  color: white;
  font-size: 16px;
  padding: 8px 0;
  overflow: hidden;
  z-index: 9999;
}

.ticker {
  display: inline-block;
  white-space: nowrap;
  animation: scroll 800s linear infinite;
  padding-left: 100%;
}

@keyframes scroll {
  0% {
    transform: translateX(0%);
  }
  100% {
    transform: translateX(-100%);
  }
}
</style>
