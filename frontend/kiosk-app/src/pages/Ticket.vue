<template>
  <div class="min-h-screen flex items-center justify-center px-6 py-10">
    <div class="kiosk-ticket-wrapper">
      <div class="kiosk-step-header">
        <div class="kiosk-pill">
          <span class="scan-dot"></span>
          Kiosk Scan - Step 3 of 3
        </div>
      </div>
      <div class="kiosk-ticket-card">
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

      <div class="kiosk-ticket-barcode" aria-hidden="true"></div>

      <div class="kiosk-ticket-meta">
        <div class="kiosk-ticket-row">
          <span>Resident</span>
          <span>{{ residentName }}</span>
        </div>
        <div class="kiosk-ticket-row">
          <span>Service</span>
          <span>{{ service?.name || '—' }}</span>
        </div>
        <div class="kiosk-ticket-row">
          <span>Issued</span>
          <span>{{ issuedAt }}</span>
        </div>
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
        <router-link class="kiosk-ticket-link" to="/welcome">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6" />
          </svg>
          Main Menu
        </router-link>
        <button class="kiosk-ticket-download" type="button" @click="downloadTicket">
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
import { computed } from 'vue'

const ticket = JSON.parse(localStorage.getItem('kiosk_ticket') || 'null')
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')
const service = JSON.parse(localStorage.getItem('kiosk_service') || 'null')
const requirements = JSON.parse(localStorage.getItem('kiosk_service_requirements') || '[]')

const residentName = computed(() => {
  if (!resident) return 'Unknown'
  return `${resident.first_name || ''} ${resident.last_name || ''}`.trim() || 'Unknown'
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
  const lines = [
    `Ticket No.: ${ticket?.ticket_no || '---'}`,
    `Resident: ${residentName.value}`,
    `Service: ${service?.name || '—'}`,
    `Issued: ${issuedAt.value}`,
  ]
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
</script>
