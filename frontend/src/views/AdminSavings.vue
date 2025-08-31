<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-2xl font-extrabold">Admin • Savings Requests</h1>
      <router-link
        class="bg-white text-[#547164] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100"
        to="/"
      >
        Back
      </router-link>
    </header>

    <main class="flex-1 grid place-items-center px-4 py-8">
      <div class="w-full max-w-5xl">
        <section
          class="bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-blue-400"
        >
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-2xl font-extrabold">Applications</h2>
            <select v-model="status" class="rounded-lg border p-2 text-black">
              <option value="">All</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>

          <div v-if="loading" class="text-sm">Loading…</div>

          <ul v-else class="divide-y divide-white/30">
            <li
              v-for="a in apps"
              :key="a.id"
              class="py-4 grid md:grid-cols-12 items-center gap-3"
            >
              <div class="md:col-span-4">
                <div class="font-bold">{{ a.user?.name }} ({{ a.user?.email }})</div>
                <div class="text-sm opacity-90">{{ a.bank?.name }} • {{ a.period_month }}</div>
              </div>

              <div class="md:col-span-2">
                <div class="font-semibold">{{ Number(a.amount).toFixed(2) }}</div>
                <div class="text-sm opacity-90">BDT</div>
              </div>

              <div class="md:col-span-2 font-semibold" :class="statusClass(a.status)">
                {{ a.status }}
              </div>

              <div class="md:col-span-4 flex items-center gap-2 md:justify-end">
                <a
                  v-if="a.bank?.website_url"
                  :href="a.bank.website_url"
                  target="_blank"
                  class="underline text-sm"
                >Bank site</a>

                <button
                  v-if="a.status==='pending'"
                  class="px-3 py-1.5 rounded-lg bg-white text-[#547164] font-semibold"
                  @click="approve(a.id)"
                >Approve</button>

                <button
                  v-if="a.status==='pending'"
                  class="px-3 py-1.5 rounded-lg bg-red-600 text-white"
                  @click="reject(a.id)"
                >Reject</button>
              </div>
            </li>
          </ul>

          <p v-if="error" class="mt-3 text-red-300 text-sm">{{ error }}</p>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import api from '@/lib/api';

interface Bank { id:number; name:string; website_url:string }
interface User { id:number; name:string; email:string }
interface AdminAppItem {
  id:number; amount:number|string; mode:'auto'|'manual';
  status:'pending'|'approved'|'rejected'|'canceled';
  period_month:string; bank?:Bank; user?:User
}

const status = ref<string>('');
const apps   = ref<AdminAppItem[]>([]);
const loading = ref(false);
const error   = ref<string | null>(null);

function statusClass(s: AdminAppItem['status']) {
  return {
    'text-yellow-700': s === 'pending',
    'text-green-700': s === 'approved',
    'text-red-700': s === 'rejected',
    'text-gray-700': s === 'canceled',
  };
}

async function load() {
  loading.value = true;
  error.value = null;
  try {
    const url = status.value
      ? `/api/admin/savings?status=${encodeURIComponent(status.value)}`
      : `/api/admin/savings`; // you can change to /admin/savings if you made that route
    const { data } = await api.get<AdminAppItem[]>(url);
    apps.value = data;
  } catch (e:any) {
    error.value = e?.response?.data?.message || 'Failed to load';
  } finally {
    loading.value = false;
  }
}

async function approve(id:number) {
  try {
    await api.post(`/api/admin/savings/${id}/approve`);
    await load();
  } catch (e:any) {
    error.value = e?.response?.data?.message || 'Approve failed';
  }
}

async function reject(id:number) {
  try {
    // If you created /admin/savings/reject/{id} use that
    await api.post(`/api/admin/savings/${id}/reject`);
    await load();
  } catch (e:any) {
    error.value = e?.response?.data?.message || 'Reject failed';
  }
}

watch(status, load);
onMounted(load);
</script>
