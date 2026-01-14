<template>
  <div
    class="display-shell min-h-screen py-10"
    :class="{ 'is-idle': isIdle, 'is-contrast': isHighContrast, 'is-large-text': isLargeText }"
  >
    <div class="display-orb display-orb--one" aria-hidden="true"></div>
    <div class="display-orb display-orb--two" aria-hidden="true"></div>
    <div class="display-grid-lines" aria-hidden="true"></div>

    <div class="relative z-10 flex w-full flex-col gap-10 px-6 sm:px-10 lg:px-14">
      <header class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="space-y-3 reveal">
          <p class="text-xs uppercase tracking-[0.4em] text-white/60">Barangay San Miguel</p>
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-4xl font-semibold text-white sm:text-5xl lg:text-6xl">Now Serving</h1>
          </div>
        </div>
        <div class="tool-panel rounded-2xl px-4 py-3 reveal reveal-delay-1" :class="{ 'is-open': isToolsOpen }">
          <button class="toggle-button is-icon tool-trigger" type="button" @click="toggleTools" aria-label="Toggle display tools">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M3 7h18" stroke-linecap="round" />
              <path d="M3 12h18" stroke-linecap="round" />
              <path d="M3 17h10" stroke-linecap="round" />
            </svg>
          </button>
          <div class="tool-menu" :class="{ 'is-open': isToolsOpen }">
            <div class="tool-section">
              <div class="select-wrap">
                <select v-model="serviceFilter" class="service-select" aria-label="Filter by service">
                  <option value="">All Services</option>
                  <option v-for="service in services" :key="service.id" :value="service.id">
                    {{ service.name }}
                  </option>
                </select>
                <svg
                  class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/70"
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
            <div class="tool-section tool-actions">
              <button class="toggle-button" type="button" :class="{ active: isLargeText }" @click="toggleLargeText">
                A+
              </button>
              <button class="toggle-button" type="button" :class="{ active: isHighContrast }" @click="toggleContrast">
                High contrast
              </button>
              <button class="toggle-button" type="button" @click="toggleFullscreen">
                {{ isFullscreen ? 'Exit full' : 'Fullscreen' }}
              </button>
            </div>
          </div>
        </div>
      </header>

      <section class="now-card reveal reveal-delay-2">
        <div class="relative flex flex-col items-center gap-3 text-center">
          <p class="text-xs uppercase tracking-[0.4em] text-white/60">Now Serving</p>
          <div
            :key="nowServingKey"
            class="font-ticket now-serving-value now-glow text-[clamp(3.5rem,9vw,8.5rem)] leading-none tracking-[0.2em] text-[color:var(--secondary)] tabular-nums"
          >
            {{ nowServing || '---' }}
          </div>
          <p class="text-base text-white/70">Please proceed to the counter</p>
        </div>
      </section>

      <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3 reveal reveal-delay-3">
          <h2 class="text-2xl font-semibold text-white">Next in Line</h2>
          <div class="queue-stats">
            <span class="badge badge-strong">{{ tickets.length }} waiting</span>
            <span class="badge badge-primary badge-strong">{{ estimatedWaitLabel }}</span>
          </div>
        </div>
        <div v-if="tickets.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <div
            v-for="(ticket, index) in tickets"
            :key="ticket.ticket_no"
            class="ticket-card reveal"
            :style="{ animationDelay: `${index * 80}ms` }"
          >
            <span class="ticket-rank" :class="{ 'is-next': index === 0 }">
              {{ index === 0 ? 'Next' : `#${index + 1}` }}
            </span>
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
    <div v-if="isIdle" class="idle-overlay" @click="exitIdle">
      <div class="idle-card">
        <p class="text-xs uppercase tracking-[0.35em] text-white/60">Idle mode</p>
        <p class="text-2xl font-semibold text-white">Tap to resume display</p>
        <button v-if="!isFullscreen" class="idle-button" type="button" @click.stop="toggleFullscreen">
          Enter fullscreen
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { request } from '../api'

const nowServing = ref(null)
const nowServingKey = ref(0)
const tickets = ref([])
const services = ref([])
const serviceFilter = ref('')
const avgWaitMinutes = ref(null)
const isIdle = ref(false)
const isHighContrast = ref(false)
const isLargeText = ref(false)
const isFullscreen = ref(false)
const isToolsOpen = ref(false)
const lastSnapshot = ref('')
const lastActivityAt = ref(Date.now())
const idleTimeoutMs = 30000
let timer = null
let idleTimer = null

const estimatedWaitMinutes = computed(() => {
  if (!avgWaitMinutes.value || tickets.value.length === 0) return null
  return Math.round(avgWaitMinutes.value * tickets.value.length)
})

const formatMinutes = (value) => {
  if (!value && value !== 0) return '--'
  if (value < 60) return `${value} min`
  const hours = Math.floor(value / 60)
  const minutes = value % 60
  if (!minutes) return `${hours} hr`
  return `${hours} hr ${minutes} min`
}

const estimatedWaitLabel = computed(() => {
  if (!estimatedWaitMinutes.value) return 'Est wait: --'
  return `Est wait: ~${formatMinutes(estimatedWaitMinutes.value)}`
})

const updateIdleState = () => {
  isIdle.value = Date.now() - lastActivityAt.value > idleTimeoutMs
}

const exitIdle = () => {
  lastActivityAt.value = Date.now()
  isIdle.value = false
}

const toggleContrast = () => {
  isHighContrast.value = !isHighContrast.value
  localStorage.setItem('display_high_contrast', isHighContrast.value ? '1' : '0')
}

const toggleLargeText = () => {
  isLargeText.value = !isLargeText.value
  localStorage.setItem('display_large_text', isLargeText.value ? '1' : '0')
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen?.()
  } else {
    document.exitFullscreen?.()
  }
}

const toggleTools = () => {
  isToolsOpen.value = !isToolsOpen.value
}

const handleFullscreenChange = () => {
  isFullscreen.value = Boolean(document.fullscreenElement)
}

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
    avgWaitMinutes.value = data.avg_wait_minutes ?? null
    lastActivityAt.value = Date.now()
    isIdle.value = false
    const snapshot = JSON.stringify({
      nowServing: data.now_serving,
      tickets: (data.tickets || []).map((ticket) => ticket.ticket_no),
      avgWaitMinutes: data.avg_wait_minutes,
    })
    if (snapshot !== lastSnapshot.value) {
      lastSnapshot.value = snapshot
      lastActivityAt.value = Date.now()
      isIdle.value = false
      nowServingKey.value += 1
    }
  } catch (err) {
    // Ignore for now
  }
}

const startPolling = () => {
  loadQueue()
  timer = setInterval(loadQueue, 2000)
}

onMounted(() => {
  isHighContrast.value = localStorage.getItem('display_high_contrast') === '1'
  isLargeText.value = localStorage.getItem('display_large_text') === '1'
  document.addEventListener('fullscreenchange', handleFullscreenChange)
  loadServices()
  startPolling()
  idleTimer = setInterval(updateIdleState, 4000)
})

watch(serviceFilter, () => {
  loadQueue()
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
  if (idleTimer) clearInterval(idleTimer)
  document.removeEventListener('fullscreenchange', handleFullscreenChange)
})
</script>
