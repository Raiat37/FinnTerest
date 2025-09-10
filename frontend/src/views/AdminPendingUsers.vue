<template>
  <div class="space-y-4">
    <div class="rounded-xl overflow-hidden border border-black/10">
      <div class="bg-[#547164] px-4 py-3 text-[15px] font-semibold grid grid-cols-[64px_1fr_1fr_1fr_120px] gap-3 text-white">
        <div>ID</div>
        <div>Name</div>
        <div>Email</div>
        <div>Job</div>
        <div class="text-right pr-1">Action</div>
      </div>

      <div class="bg-[#f1f2f4] divide-y">
        <div v-if="loading" class="p-4 text-center text-sm text-gray-600">Loading...</div>
        <div v-else-if="!users.length" class="p-4 text-center text-sm text-gray-600">No pending users</div>
        <div v-else v-for="u in users" :key="u.id" class="px-4 py-3 grid grid-cols-[64px_1fr_1fr_1fr_120px] gap-3 items-center text-[15px]">
          <div>{{ u.id }}</div>
          <div class="truncate">{{ u.name }}</div>
          <div class="truncate">{{ u.email }}</div>
          <div class="truncate">{{ u.job || '—' }}</div>
          <div class="text-right">
            <button
              class="bg-[#3a3a06] text-white px-3 py-1.5 rounded hover:opacity-90 disabled:opacity-50"
              :disabled="approvingId === u.id"
              @click="approve(u.id)"
            >
              {{ approvingId === u.id ? 'Approving...' : 'Approve' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <p v-if="message" class="text-center font-medium" :class="ok ? 'text-green-700' : 'text-red-700'">
      {{ message }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/lib/api'

type PendingUser = {
  id: number
  name: string
  email: string
  job?: string
}

const users = ref<PendingUser[]>([])
const loading = ref(false)
const approvingId = ref<number | null>(null)
const message = ref('')
const ok = ref(true)

async function loadPending() {
  loading.value = true
  message.value = ''
  try {
    const { data } = await api.get('/api/admin/users/pending')
    users.value = Array.isArray(data) ? data : []
  } catch (e: any) {
    ok.value = false
    message.value = e?.response?.data?.message || 'Failed to load pending users'
  } finally {
    loading.value = false
  }
}

async function approve(id: number) {
  approvingId.value = id
  message.value = ''
  try {
    await api.post(`/api/admin/users/approve/${id}`)
    ok.value = true
    message.value = 'Approved user successfully'
    await loadPending()
  } catch (e: any) {
    ok.value = false
    message.value = e?.response?.data?.message || 'Approval failed'
  } finally {
    approvingId.value = null
  }
}

onMounted(loadPending)
</script>


