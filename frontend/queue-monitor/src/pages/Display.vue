<template>
  <div class="min-h-screen p-10">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      <div>
        <p class="text-sm uppercase tracking-[0.3em] text-white/70">Barangay San Miguel</p>
        <h1 class="text-5xl font-semibold text-[#F2C300]">Now Serving</h1>
      </div>
      <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-white/10 text-white flex items-center justify-center">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 12h18" />
            <path d="M3 6h18" />
            <path d="M3 18h10" />
          </svg>
        </div>
        <select v-model="serviceFilter" class="bg-white text-black border border-white px-4 py-3 rounded-xl text-lg">
          <option value="">All Services</option>
          <option v-for="service in services" :key="service.id" :value="service.id">
            {{ service.name }}
          </option>
        </select>
      </div>
    </div>

    <div class="mt-12 border-4 border-[#F2C300] rounded-3xl p-10 text-center">
      <div class="text-7xl md:text-8xl font-mono tracking-widest text-white">{{ nowServing || '---' }}</div>
      <div class="mt-3 text-[#F2C300] text-xl">Please proceed to the counter</div>
    </div>

    <div class="mt-12">
      <h2 class="text-2xl font-semibold">Next in Line</h2>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div
          v-for="ticket in tickets"
          :key="ticket.ticket_no"
          class="bg-white text-black border border-white rounded-2xl p-6 text-center text-3xl font-mono"
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
