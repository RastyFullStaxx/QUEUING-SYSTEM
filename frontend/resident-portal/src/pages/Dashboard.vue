<template>
  <div class="max-w-3xl mx-auto py-12 px-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-semibold">Resident Dashboard</h1>
      <router-link class="text-blue-600" to="/profile">Profile</router-link>
    </div>
    <div class="mt-8 p-6 bg-white rounded shadow">
      <p class="text-lg">Welcome, {{ residentName }}</p>
      <p class="mt-2">
        Status:
        <span class="font-semibold" :class="statusClass">{{ status }}</span>
      </p>
      <div v-if="status === 'approved'" class="mt-6">
        <p class="text-sm text-slate-600">QR Code placeholder:</p>
        <div class="mt-2 p-4 border rounded text-center text-xl font-mono">
          RESIDENT #{{ resident?.id || '---' }}
        </div>
      </div>
      <div v-else class="mt-6 text-sm text-slate-600">
        Your account is awaiting approval. We will notify you once verified.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { request } from '../api'

const resident = ref(null)
const status = ref('pending')

const residentName = computed(() => {
  if (!resident.value) return 'Resident'
  return `${resident.value.first_name} ${resident.value.last_name}`.trim()
})

const statusClass = computed(() => {
  if (status.value === 'approved') return 'text-green-600'
  if (status.value === 'rejected') return 'text-red-600'
  return 'text-yellow-600'
})

onMounted(async () => {
  const cached = localStorage.getItem('resident_profile')
  if (cached) {
    resident.value = JSON.parse(cached)
    status.value = resident.value.status
  }

  const token = localStorage.getItem('resident_token')
  if (token) {
    try {
      const data = await request('/api/resident/me', {
        headers: { Authorization: `Bearer ${token}` },
      })
      resident.value = data.resident
      status.value = data.resident.status
      localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    } catch (err) {
      // Ignore refresh errors for now.
    }
  }
})
</script>
