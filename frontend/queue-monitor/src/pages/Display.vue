<template>
  <div class="min-h-screen bg-slate-900 text-white p-10">
    <div class="flex items-center justify-between">
      <h1 class="text-4xl font-bold">Now Serving</h1>
      <select v-model="serviceFilter" class="bg-slate-800 border border-slate-700 px-3 py-2 rounded">
        <option value="">All Services</option>
        <option v-for="service in services" :key="service.id" :value="service.id">
          {{ service.name }}
        </option>
      </select>
    </div>

    <div class="mt-10 bg-slate-800 rounded p-8 text-center">
      <div class="text-6xl font-mono">{{ nowServing || '---' }}</div>
      <div class="mt-2 text-slate-300">Please proceed to the counter</div>
    </div>

    <div class="mt-10">
      <h2 class="text-2xl font-semibold">Next in Line</h2>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div
          v-for="ticket in tickets"
          :key="ticket.ticket_no"
          class="bg-slate-800 rounded p-6 text-center text-3xl font-mono"
        >
          {{ ticket.ticket_no }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { request } from '../api'

const nowServing = ref(null)
const tickets = ref([])
const services = ref([])
const serviceFilter = ref('')
let timer = null

const loadServices = async () => {
  try {
    const data = await request('/api/services')
    services.value = data.services || []
  } catch (err) {
    // Ignore for now
  }
}

const loadQueue = async () => {
  const query = serviceFilter.value ? `?service_id=${serviceFilter.value}` : ''
  try {
    const data = await request(`/api/queue/current${query}`)
    nowServing.value = data.now_serving
    tickets.value = data.tickets || []
  } catch (err) {
    // Ignore for now
  }
}

const startPolling = () => {
  loadQueue()
  timer = setInterval(loadQueue, 2000)
}

onMounted(() => {
  loadServices()
  startPolling()
})

watch(serviceFilter, () => {
  loadQueue()
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
