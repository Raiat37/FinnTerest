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
      <h2 class="text-[28px] font-extrabold text-[#1d1d1f] mb-6 text-center">
        Savings Applications
      </h2>

      <!-- Centered single card -->
      <section
        class="mx-auto max-w-3xl rounded-[14px] bg-[#261c03] text-white shadow-[0_2px_10px_rgba(0,0,0,.12)] ring-1 ring-black/10 p-5"
      >
        <h3 class="text-center text-[22px] font-extrabold mb-4">
          Savings Requests
        </h3>

        <div class="rounded-xl overflow-hidden border border-black/10">
          <!-- header bar -->
          <div
            class="bg-[#547164] px-4 py-3 text-[15px] font-semibold grid grid-cols-[64px_1fr_1fr_110px] gap-3"
          >
            <div>ID</div>
            <div>Name</div>
            <div>Bank</div>
            <div class="text-right pr-1">Action</div>
          </div>

          <!-- content -->
          <div class="bg-[#f1f2f4] p-4">
            <AdminSavings />
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import AdminSavings from '@/views/AdminSavings.vue'
import api from '@/lib/api' // keep if AdminSavings uses the same api instance somewhere

function logout() {
  localStorage.removeItem('token')
  sessionStorage.removeItem('token')
  window.location.href = '/login'
}

// If you previously fetched pending profiles/currency here, we no longer need that.
// Keep onMounted if you want to do any generic checks:
onMounted(() => {
  // no-op for now
})
</script>
