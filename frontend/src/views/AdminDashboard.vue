<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-[32px] leading-none font-extrabold tracking-tight">
        FinnTerest
      </h1>
      <button
        class="bg-[#3a3a06] text-white px-5 py-2 rounded-lg font-semibold hover:opacity-90"
        @click="logout"
      >
        Logout
      </button>
    </header>

    <main class="flex-1 px-6 py-8">
      <h2 class="text-[28px] font-extrabold text-[#1d1d1f] mb-6">Welcome Admin!</h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Currency Exchange Rates (large card) -->
        <section class="rounded-[12px] bg-[#261c03] text-white p-6 shadow-lg">
          <h3 class="text-[20px] font-extrabold mb-4">Currency Exchange Rates</h3>

          <div class="grid grid-cols-1 gap-4">
            <div class="rounded-md overflow-hidden">
              <div class="bg-[#dfe6e2] p-4 text-[#1d1d1f]">
                <AdminCurrencyRates />
              </div>
            </div>
          </div>
        </section>

        <!-- Right column: two stacked cards -->
        <div class="space-y-6">
          <section class="rounded-[12px] bg-white text-[#1d1d1f] p-4 shadow-lg">
            <h4 class="text-lg font-semibold mb-3">User Profile changes</h4>
            <div class="bg-[#dfe6e2] p-3 rounded">
              <AdminPendingUsers />
            </div>
          </section>

          <section class="rounded-[12px] bg-white text-[#1d1d1f] p-4 shadow-lg">
            <h4 class="text-lg font-semibold mb-3">Savings Requests</h4>
            <div class="bg-[#dfe6e2] p-3 rounded">
              <AdminSavings />
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import AdminSavings from '@/views/AdminSavings.vue'
import AdminCurrencyRates from '@/views/AdminCurrencyRates.vue'
import AdminPendingUsers from '@/views/AdminPendingUsers.vue'
import api from '@/lib/api' // keep if AdminSavings uses the same api instance somewhere

function logout() {
  localStorage.removeItem('token')
  sessionStorage.removeItem('token')
  window.location.href = '/login'
}

onMounted(() => {
  // no-op for now
})
</script>
