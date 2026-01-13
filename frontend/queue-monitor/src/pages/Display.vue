<template>
  <div class="display-shell min-h-screen px-6 py-10 sm:px-10 lg:px-14">
    <div class="display-orb display-orb--one" aria-hidden="true"></div>
    <div class="display-orb display-orb--two" aria-hidden="true"></div>
    <div class="display-grid-lines" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto flex max-w-6xl flex-col gap-10">
      <header class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="space-y-3 reveal">
          <p class="text-xs uppercase tracking-[0.4em] text-white/60">Barangay San Miguel</p>
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-4xl font-semibold text-white sm:text-5xl lg:text-6xl">Now Serving</h1>
            <span class="badge">Live Queue</span>
          </div>
          <p class="text-base text-white/70">Live queue monitor - Auto-refreshes every 2 seconds</p>
        </div>
        <div class="glass-panel flex items-center gap-4 rounded-2xl px-4 py-3 reveal reveal-delay-1">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-white/80">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12h18" />
              <path d="M3 6h18" />
              <path d="M3 18h10" />
            </svg>
          </div>
          <div class="space-y-1">
            <p class="text-xs uppercase tracking-[0.3em] text-white/60">Service Filter</p>
            <div class="relative">
              <select v-model="serviceFilter" class="service-select" aria-label="Filter by service">
                <option value="">All Services</option>
                <option v-for="service in services" :key="service.id" :value="service.id">
                  {{ service.name }}
                </option>
              </select>
              <svg
                class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-700"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>
      </header>

      <section class="now-card reveal reveal-delay-2">
        <div class="relative flex flex-col items-center gap-3 text-center">
          <p class="text-xs uppercase tracking-[0.4em] text-white/60">Now Serving</p>
          <div
            class="font-ticket now-glow text-[clamp(3.5rem,9vw,8.5rem)] leading-none tracking-[0.2em] text-[color:var(--secondary)] tabular-nums"
          >
            {{ nowServing || '---' }}
          </div>
          <p class="text-base text-white/70">Please proceed to the counter</p>
        </div>
      </section>

      <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3 reveal reveal-delay-3">
          <h2 class="text-2xl font-semibold text-white">Next in Line</h2>
          <span class="badge badge-muted">{{ tickets.length }} waiting</span>
        </div>
        <div v-if="tickets.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <div
            v-for="(ticket, index) in tickets"
            :key="ticket.ticket_no"
            class="ticket-card reveal"
            :style="{ animationDelay: `${index * 80}ms` }"
          >
            <p class="text-xs uppercase tracking-[0.35em] text-white/50">Ticket</p>
            <div class="font-ticket text-3xl text-white sm:text-4xl tabular-nums">
              {{ ticket.ticket_no }}
            </div>
          </div>
        </div>
        <div v-else class="empty-card reveal">
          <p class="text-base text-white/60">Queue is clear. New tickets will appear here.</p>
        </div>
      </section>
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
