<template>
  <div class="min-h-screen bg-[#e7e7e9] flex flex-col">
    <!-- Top bar -->
    <header class="w-full bg-[#547164] px-6 py-4 flex items-center justify-between">
      <h1 class="text-white text-2xl font-extrabold">FinnTerest</h1>
      <router-link
        class="bg-[oklch(45.229%_0.035_264.131)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-10"
        to="/"
      >
        Back
      </router-link>
    </header>

    <main class="flex-1 grid place-items-center px-4 py-8">
      <div class="w-full max-w-3xl space-y-8">
        <!-- Card: Apply -->
        <section
          class="bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-black-400"
        >
          <h2 class="text-2xl font-extrabold mb-6 text-center">Create Savings Application</h2>

          <div class="grid md:grid-cols-2 gap-6">
            <!-- Mode -->
            <div>
              <label class="block font-semibold mb-1">Mode</label>
              <div class="space-y-2">
                <label class="flex items-center gap-2">
                  <input type="radio" value="auto" v-model="form.mode" />
                  Auto (salary − budget)
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" value="manual" v-model="form.mode" />
                  Manual
                </label>
              </div>
              <p v-if="form.mode==='auto'" class="mt-2 text-sm">
                Surplus this month: <b>{{ surplusDisplay }}</b>
              </p>
            </div>

            <!-- Manual amount -->
            <div v-if="form.mode==='manual'">
              <label class="block font-semibold mb-1">Amount (BDT)</label>
              <input
                type="number"
                v-model.number="form.amount"
                placeholder="5000"
                class="w-full rounded-lg border p-2 text-black"
              />
            </div>

            <!-- Bank select -->
            <SavingsBankSelect v-model="form.bank_id" :disabled="submitting" />

            <!-- Month -->
            <div>
              <label class="block font-semibold mb-1">Month</label>
              <input
                type="month"
                v-model="monthInput"
                class="w-full rounded-lg border p-2 text-black"
              />
              <p class="text-xs mt-1">Defaults to current month</p>
            </div>
          </div>

          <div class="mt-6 flex items-center gap-3">
            <button
              class="px-5 py-2 rounded-lg font-semibold bg-white text-[#547164] hover:bg-gray-100 disabled:opacity-60"
              :disabled="!canSubmit || submitting"
              @click="submit"
            >
              {{ submitting ? 'Applying…' : 'Apply' }}
            </button>
            <span v-if="error" class="text-red-300 text-sm">{{ error }}</span>
            <span v-if="message" class="text-green-300 text-sm">{{ message }}</span>
          </div>
        </section>

        <!-- Card: Applications -->
        <section
          class="bg-[#547164] text-white rounded-lg p-8 shadow ring-2 ring-black-400"
        >
          <h2 class="text-2xl font-extrabold mb-6 text-center">My Applications</h2>

          <div v-if="loadingApps" class="text-sm">Loading…</div>
          <div v-else-if="apps.length === 0" class="text-sm">No applications yet.</div>

          <ul class="divide-y divide-white/30">
            <li
              v-for="a in apps"
              :key="a.id"
              class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3"
            >
              <div>
                <div class="font-bold">{{ a.bank?.name }} • {{ a.period_month }}</div>
                <div class="text-sm opacity-90">
                  {{ a.mode.toUpperCase() }} • {{ Number(a.amount).toFixed(2) }} BDT
                  • <span :class="statusClass(a.status)">{{ a.status }}</span>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <button
                  v-if="a.status==='pending'"
                  class="px-3 py-1.5 rounded-lg bg-gray-300 text-gray-700"
                  disabled
                >Applied</button>

                <button
                  v-else-if="a.status==='approved'"
                  class="px-3 py-1.5 rounded-lg bg-white text-[#547164] font-semibold"
                  @click="goToBank(a)"
                >Go to Bank</button>
              </div>
            </li>
          </ul>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import api from '@/lib/api';
import SavingsBankSelect from '@/components/SavingsBankSelect.vue';

// Minimal user info to compute surplus; replace with your store if you have one
interface User { id:number; name:string; salary?:number; budget?:number; role?:string; }
interface Bank { id:number; name:string; website_url:string }
interface AppItem {
  id:number; bank_id:number; amount:string|number; mode:'auto'|'manual';
  status:'pending'|'approved'|'rejected'|'canceled';
  period_month:string; bank?:Bank
}

const user   = ref<User | null>(null);
const banks  = ref<Bank[]>([]);
const apps   = ref<AppItem[]>([]);
const loadingApps = ref(false);

const form = ref<{ mode:'auto'|'manual'; amount:number|null; bank_id:number|null; }>(
  { mode:'auto', amount:null, bank_id:null }
);

const monthInput = ref<string>(''); // yyyy-mm
const submitting = ref(false);
const error      = ref<string | null>(null);
const message    = ref<string | null>(null);


async function fetchMe() {
  // assuming your axios baseURL ends with /api
  const { data } = await api.get<User>('/api/mesave');   // <-- real call now
  user.value = data;
}

const surplus = computed(() => {
  const s = Number(user.value?.salary ?? 0);
  const b = Number(user.value?.budget ?? 0);
  return Math.max(0, s - b);
});
const surplusDisplay = computed(() => `${surplus.value.toFixed(2)} BDT`);

const canSubmit = computed(() => {
  if (!form.value.bank_id) return false;
  if (form.value.mode === 'manual') {
    return !!form.value.amount && Number(form.value.amount) > 0;
  }
  // auto mode: allow submit; backend will validate surplus > 0
  return true;
});

function statusClass(s: AppItem['status']) {
  return {
    'text-yellow-700': s === 'pending',
    'text-green-700': s === 'approved',
    'text-red-700': s === 'rejected',
    'text-gray-700': s === 'canceled',
  };
}

function firstOfMonthFromInput() {
  if (!monthInput.value) return null;
  const [y, m] = monthInput.value.split('-');
  if (!y || !m) return null;
  return `${y}-${m}-01`;
}

async function loadApps() {
  loadingApps.value = true;
  try {
    const { data } = await api.get<AppItem[]>('/api/savings/applications');
    apps.value = data;
  } finally {
    loadingApps.value = false;
  }
}

async function submit() {
  error.value = null;
  message.value = null;
  submitting.value = true;
  try {
    const payload:any = {
      bank_id: form.value.bank_id,
      mode: form.value.mode,
    };
    const period = firstOfMonthFromInput();
    if (period) payload.period_month = period;
    if (form.value.mode === 'manual') payload.amount = form.value.amount;

    const { data } = await api.post('/api/savings/applications', payload);
    message.value = data?.message || 'Application submitted';
    await loadApps();
  } catch (e:any) {
    error.value = e?.response?.data?.message || 'Failed to submit';
  } finally {
    submitting.value = false;
  }
}

function goToBank(a: AppItem) {
  if (a.bank?.website_url) {
    window.open(a.bank.website_url, '_blank', 'noopener');
  }
}

onMounted(async () => {
  await fetchMe();
  await loadApps();
});
</script>
