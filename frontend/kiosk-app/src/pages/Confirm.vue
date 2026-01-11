<template>
  <div class="min-h-screen px-6 py-10">
    <h1 class="text-3xl font-bold">Confirm Details</h1>
    <div class="mt-8 bg-white border rounded p-6 text-lg">
      <p><span class="font-semibold">Resident:</span> {{ residentName }}</p>
      <p class="mt-2"><span class="font-semibold">Service:</span> {{ service?.name }}</p>
    </div>
    <button
      class="mt-8 bg-blue-600 text-white text-2xl px-8 py-4 rounded"
      @click="createTicket"
    >
      Create Ticket
    </button>
    <p v-if="error" class="mt-4 text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const error = ref('')
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')
const service = JSON.parse(localStorage.getItem('kiosk_service') || 'null')

const residentName = computed(() => {
  if (!resident) return 'Unknown'
  return `${resident.first_name} ${resident.last_name}`.trim()
})

const createTicket = async () => {
  error.value = ''
  if (!resident || !service) {
    error.value = 'Missing resident or service information.'
    return
  }

  try {
    const idempotencyKey = crypto.randomUUID?.() || `${Date.now()}-${resident.id}-${service.id}`
    const data = await request('/api/kiosk/tickets', {
      method: 'POST',
      body: JSON.stringify({
        resident_id: resident.id,
        service_id: service.id,
        kiosk_device_id: 1,
        idempotency_key: idempotencyKey,
      }),
    })
    localStorage.setItem('kiosk_ticket', JSON.stringify(data.ticket))
    router.push('/ticket')
  } catch (err) {
    error.value = err.message
  }
}
</script>
