<template>
  <div class="min-h-screen flex items-center justify-center px-6 py-10">
    <div v-if="showStepFlash" class="kiosk-step-flash" aria-hidden="true">
      <span class="kiosk-step-flash-text">STEP 3</span>
    </div>
    <div v-if="showStageReveal" class="kiosk-stage-reveal" aria-hidden="true"></div>
    <div v-if="!showStepFlash" class="kiosk-ticket-wrapper">
      <div class="kiosk-step-header">
        <div class="kiosk-pill">
          <span class="scan-dot"></span>
          Kiosk Scan - Step 3 of 3
        </div>
      </div>
      <div class="kiosk-ticket-card">
      <div class="kiosk-ticket-luxe" aria-hidden="true"></div>
      <div class="kiosk-ticket-grain" aria-hidden="true"></div>
      <div class="kiosk-ticket-watermark" aria-hidden="true"></div>
      <div class="kiosk-ticket-header">
        <div class="kiosk-ticket-icon">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 7h16" />
            <path d="M4 12h16" />
            <path d="M4 17h10" />
          </svg>
        </div>
        <div class="kiosk-ticket-headline">
          <p class="kiosk-ticket-kicker">Digital Ticket</p>
          <h1 class="kiosk-ticket-title">Your queue number is ready</h1>
        </div>
      </div>

      <div class="kiosk-ticket-number">
        <div class="kiosk-ticket-number-label">
          {{ hasMultiple ? 'Ticket Nos.' : 'Ticket No.' }}
        </div>
        <div class="kiosk-ticket-number-value">
          {{ hasMultiple ? 'Multiple' : primaryTicket?.ticket_no || '---' }}
        </div>
      </div>

      <div v-if="hasMultiple" class="kiosk-ticket-multi">
        <p class="kiosk-ticket-multi-title">Tickets</p>
        <ul class="kiosk-ticket-multi-list">
          <li v-for="(ticketItem, index) in tickets" :key="ticketItem?.ticket_no || index">
            <span>{{ ticketItem?.ticket_no || '---' }}</span>
            <span>{{ services[index]?.name || '—' }}</span>
          </li>
        </ul>
      </div>

      <div class="kiosk-ticket-barcode" aria-hidden="true"></div>
      <div class="kiosk-ticket-holo" aria-hidden="true"></div>
      <div class="kiosk-ticket-divider" aria-hidden="true"></div>

      <div class="kiosk-ticket-meta">
        <div class="kiosk-ticket-row">
          <span>Resident</span>
          <span>{{ residentName }}</span>
        </div>
        <div class="kiosk-ticket-row">
          <span>Service</span>
          <span>{{ serviceLabel }}</span>
        </div>
        <div class="kiosk-ticket-row">
          <span>Issued</span>
          <span>{{ issuedAt }}</span>
        </div>
      </div>
      <div class="kiosk-ticket-stamp" aria-hidden="true">
        Issued by Kiosk
      </div>

      <div class="kiosk-ticket-reqs">
        <p class="kiosk-ticket-reqs-title">Requirements</p>
        <ul class="kiosk-ticket-reqs-list">
          <li v-for="item in requirements" :key="item">
            <span class="kiosk-ticket-reqs-dot"></span>
            <span>{{ item }}</span>
          </li>
        </ul>
      </div>

      <p class="kiosk-ticket-note">Please wait for your number to be called.</p>

      <div class="kiosk-ticket-actions">
        <router-link class="kiosk-ticket-link kiosk-action" to="/welcome">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6" />
          </svg>
          Main Menu
        </router-link>
        <button class="kiosk-ticket-download kiosk-action" type="button" @click="downloadTicket">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3v12" />
            <path d="M7 10l5 5 5-5" />
            <path d="M5 21h14" />
          </svg>
          <span>Download</span>
        </button>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const storedTicket = JSON.parse(localStorage.getItem('kiosk_ticket') || 'null')
const storedTickets = JSON.parse(localStorage.getItem('kiosk_tickets') || 'null')
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')
const storedService = JSON.parse(localStorage.getItem('kiosk_service') || 'null')
const storedServices = JSON.parse(localStorage.getItem('kiosk_services') || 'null')
const storedRequirements = JSON.parse(localStorage.getItem('kiosk_service_requirements') || '[]')

const tickets = Array.isArray(storedTickets) && storedTickets.length ? storedTickets : storedTicket ? [storedTicket] : []
const services = Array.isArray(storedServices) && storedServices.length ? storedServices : storedService ? [storedService] : []
const requirementsList = Array.isArray(storedRequirements)
  ? Array.isArray(storedRequirements[0])
    ? storedRequirements
    : [storedRequirements]
  : []
const showStepFlash = ref(true)
const stepFlashTimer = ref(null)
const showStageReveal = ref(false)
const stageRevealTimer = ref(null)

const residentName = computed(() => {
  if (!resident) return 'Unknown'
  return `${resident.first_name || ''} ${resident.last_name || ''}`.trim() || 'Unknown'
})

const primaryTicket = computed(() => tickets[0] || null)
const hasMultiple = computed(() => tickets.length > 1)
const serviceLabel = computed(() => {
  if (hasMultiple.value) {
    return `${services.length} services selected`
  }
  return services[0]?.name || '—'
})
const requirements = computed(() => {
  const merged = requirementsList.flat().filter(Boolean)
  return Array.from(new Set(merged))
})

const issuedAt = computed(() => {
  const raw = primaryTicket.value?.created_at || primaryTicket.value?.issued_at
  const date = raw ? new Date(raw) : new Date()
  return date.toLocaleString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})

const downloadTicket = () => {
  const lines = []
  if (hasMultiple.value) {
    lines.push('Tickets:')
    tickets.forEach((ticketItem, index) => {
      const serviceName = services[index]?.name || '—'
      lines.push(`- ${ticketItem?.ticket_no || '---'} (${serviceName})`)
    })
  } else {
    lines.push(`Ticket No.: ${primaryTicket.value?.ticket_no || '---'}`)
  }
  lines.push(`Resident: ${residentName.value}`)
  lines.push(`Service: ${serviceLabel.value}`)
  lines.push(`Issued: ${issuedAt.value}`)
  const blob = new Blob([lines.join('\n')], { type: 'text/plain' })
  const url = URL.createObjectURL(blob)
  const anchor = document.createElement('a')
  anchor.href = url
  anchor.download = `kiosk-ticket-${primaryTicket.value?.ticket_no || 'queue'}.txt`
  document.body.appendChild(anchor)
  anchor.click()
  anchor.remove()
  URL.revokeObjectURL(url)
}

const triggerStepFlash = () => {
  if (stepFlashTimer.value) {
    clearTimeout(stepFlashTimer.value)
  }
  if (stageRevealTimer.value) {
    clearTimeout(stageRevealTimer.value)
  }
  showStepFlash.value = true
  showStageReveal.value = false
  stepFlashTimer.value = setTimeout(() => {
    showStepFlash.value = false
    showStageReveal.value = true
    stageRevealTimer.value = setTimeout(() => {
      showStageReveal.value = false
    }, 1400)
  }, 3600)
}

onMounted(() => {
  triggerStepFlash()
})

onBeforeUnmount(() => {
  if (stepFlashTimer.value) {
    clearTimeout(stepFlashTimer.value)
  }
  if (stageRevealTimer.value) {
    clearTimeout(stageRevealTimer.value)
  }
})
</script>
