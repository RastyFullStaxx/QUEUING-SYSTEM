<template>
  <div class="min-h-screen flex items-stretch justify-center px-6 py-8">
    <div v-if="showStepFlash" class="kiosk-step-flash" aria-hidden="true">
      <span class="kiosk-step-flash-text">STEP 3</span>
    </div>
    <div v-if="showStageReveal" class="kiosk-stage-reveal" aria-hidden="true"></div>
    <div v-if="!showStepFlash" class="kiosk-step-shell kiosk-ticket-shell">
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
        <div class="kiosk-ticket-number-label">Ticket No.</div>
        <div class="kiosk-ticket-number-value">{{ ticket?.ticket_no || '---' }}</div>
      </div>

      <div v-if="selectedServices.length > 1" class="kiosk-ticket-multi">
        <p class="kiosk-ticket-multi-title">Services</p>
        <ul class="kiosk-ticket-multi-list">
          <li v-for="serviceItem in selectedServices" :key="serviceItem.id || serviceItem.name">
            <span>{{ serviceItem.name }}</span>
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
      </div>
      <div class="kiosk-step-actions">
        <router-link class="kiosk-arrow-button kiosk-arrow-button--back kiosk-action" to="/welcome">
          <span>Main menu</span>
        </router-link>
        <button class="kiosk-arrow-button kiosk-arrow-button--proceed kiosk-action" type="button" @click="downloadTicket">
          <span>Download</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const ticket = JSON.parse(localStorage.getItem('kiosk_ticket') || 'null')
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')
const storedService = JSON.parse(localStorage.getItem('kiosk_service') || 'null')
const storedServices = JSON.parse(localStorage.getItem('kiosk_services') || 'null')
const storedRequirements = JSON.parse(localStorage.getItem('kiosk_service_requirements') || '[]')

const selectedServices = Array.isArray(storedServices) && storedServices.length
  ? storedServices
  : storedService
    ? [storedService]
    : []
const showStepFlash = ref(true)
const stepFlashTimer = ref(null)
const showStageReveal = ref(false)
const stageRevealTimer = ref(null)

const residentName = computed(() => {
  if (!resident) return 'Unknown'
  return `${resident.first_name || ''} ${resident.last_name || ''}`.trim() || 'Unknown'
})

const serviceLabel = computed(() => {
  if (selectedServices.length > 1) {
    return `${selectedServices.length} services selected`
  }
  return selectedServices[0]?.name || '—'
})
const requirements = computed(() => {
  const merged = Array.isArray(storedRequirements) ? storedRequirements.flat().filter(Boolean) : []
  return Array.from(new Set(merged))
})

const issuedAt = computed(() => {
  const raw = ticket?.created_at || ticket?.issued_at
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
  const lines = [`Ticket No.: ${ticket?.ticket_no || '---'}`]
  if (selectedServices.length) {
    lines.push('Services:')
    selectedServices.forEach((serviceItem) => {
      lines.push(`- ${serviceItem.name}`)
    })
  }
  lines.push(`Resident: ${residentName.value}`)
  lines.push(`Service: ${serviceLabel.value}`)
  lines.push(`Issued: ${issuedAt.value}`)
  const blob = new Blob([lines.join('\n')], { type: 'text/plain' })
  const url = URL.createObjectURL(blob)
  const anchor = document.createElement('a')
  anchor.href = url
  anchor.download = `kiosk-ticket-${ticket?.ticket_no || 'queue'}.txt`
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
