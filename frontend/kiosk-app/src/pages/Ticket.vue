<template>
  <div class="min-h-screen kiosk-ticket-stage">
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

        <div class="kiosk-ticket-body">
          <div class="kiosk-ticket-left">
            <div class="kiosk-ticket-number kiosk-ticket-number--hero">
              <div class="kiosk-ticket-number-label">Ticket No.</div>
              <div class="kiosk-ticket-number-value">{{ ticket?.ticket_no || '---' }}</div>
              <div class="kiosk-ticket-number-sub">Please wait for your number to be called.</div>
            </div>

            <div class="kiosk-ticket-services-panel">
              <div class="kiosk-ticket-services-title">Services in this ticket</div>
              <div class="kiosk-ticket-services-grid">
                <span
                  v-for="(serviceItem, index) in selectedServices"
                  :key="serviceItem.id || serviceItem.name"
                  class="kiosk-ticket-service-pill"
                >
                  <span class="kiosk-ticket-service-index">{{ index + 1 }}</span>
                  <span class="kiosk-ticket-service-name">{{ serviceItem.name }}</span>
                </span>
              </div>
            </div>

            <div class="kiosk-ticket-barcode" aria-hidden="true"></div>
            <div class="kiosk-ticket-holo" aria-hidden="true"></div>
          </div>

          <div class="kiosk-ticket-right">
            <div class="kiosk-ticket-meta kiosk-ticket-meta--panel">
              <div class="kiosk-ticket-row">
                <span>Resident</span>
                <span>{{ residentName }}</span>
              </div>
              <div class="kiosk-ticket-row">
                <span>Service summary</span>
                <span>{{ serviceLabel }}</span>
              </div>
              <div class="kiosk-ticket-row">
                <span>Issued</span>
                <span>{{ issuedAt }}</span>
              </div>
            </div>

            <div class="kiosk-ticket-reqs kiosk-ticket-reqs--panel">
              <p class="kiosk-ticket-reqs-title">Requirements to prepare</p>
              <ul class="kiosk-ticket-reqs-list">
                <li v-for="item in requirements" :key="item">
                  <span class="kiosk-ticket-reqs-dot"></span>
                  <span>{{ item }}</span>
                </li>
              </ul>
            </div>

            <div class="kiosk-ticket-stamp" aria-hidden="true">
              Issued by Kiosk
            </div>
          </div>
        </div>
      </div>
      <div class="kiosk-step-actions">
        <button
          class="kiosk-arrow-button kiosk-arrow-button--back kiosk-action"
          type="button"
          @click="handleMainMenu"
        >
          <span>Main menu</span>
        </button>
        <button class="kiosk-arrow-button kiosk-arrow-button--proceed kiosk-action" type="button" @click="downloadTicket">
          <span>Download</span>
        </button>
      </div>
    </div>
  </div>
  <transition name="kiosk-modal">
    <div v-if="showThanks" class="kiosk-modal">
      <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card thank-you-modal">
        <span class="modal-orb orb-one" aria-hidden="true"></span>
        <span class="modal-orb orb-two" aria-hidden="true"></span>
        <div class="kiosk-modal-header">
          <p class="kiosk-modal-kicker">Thank you</p>
          <h2 class="kiosk-modal-title">Thank you for using our services</h2>
          <p class="kiosk-modal-subtitle">Redirecting you to the main menu...</p>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
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
const showThanks = ref(false)
const thanksTimer = ref(null)

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
  const canvas = document.createElement('canvas')
  const width = 1200
  const height = 700
  const scale = window.devicePixelRatio || 1
  canvas.width = width * scale
  canvas.height = height * scale
  const ctx = canvas.getContext('2d')
  if (!ctx) return
  ctx.scale(scale, scale)

  const drawRoundedRect = (x, y, w, h, r) => {
    const radius = Math.min(r, w / 2, h / 2)
    ctx.beginPath()
    ctx.moveTo(x + radius, y)
    ctx.arcTo(x + w, y, x + w, y + h, radius)
    ctx.arcTo(x + w, y + h, x, y + h, radius)
    ctx.arcTo(x, y + h, x, y, radius)
    ctx.arcTo(x, y, x + w, y, radius)
    ctx.closePath()
  }

  const wrapText = (text, x, y, maxWidth, lineHeight) => {
    const words = text.split(' ')
    let line = ''
    let currentY = y
    words.forEach((word, index) => {
      const testLine = line + word + ' '
      if (ctx.measureText(testLine).width > maxWidth && index > 0) {
        ctx.fillText(line.trim(), x, currentY)
        line = word + ' '
        currentY += lineHeight
      } else {
        line = testLine
      }
    })
    ctx.fillText(line.trim(), x, currentY)
    return currentY
  }

  ctx.fillStyle = '#f3f7ff'
  ctx.fillRect(0, 0, width, height)

  const cardX = 40
  const cardY = 40
  const cardW = width - 80
  const cardH = height - 80
  drawRoundedRect(cardX, cardY, cardW, cardH, 30)
  ctx.fillStyle = '#ffffff'
  ctx.fill()
  ctx.strokeStyle = 'rgba(11, 44, 111, 0.12)'
  ctx.lineWidth = 2
  ctx.stroke()

  ctx.font = '700 13px "Sora", sans-serif'
  ctx.fillStyle = '#7b879b'
  ctx.textAlign = 'center'
  ctx.fillText('DIGITAL TICKET', width / 2, 88)

  ctx.font = '700 30px "Space Grotesk", "Sora", sans-serif'
  ctx.fillStyle = '#0b2c6f'
  ctx.fillText('Your queue number is ready', width / 2, 128)

  const leftX = 80
  const rightX = 700
  const colGap = 20
  const leftW = rightX - leftX - colGap
  const rightW = cardX + cardW - rightX - 40
  const topY = 170

  const numberH = 150
  const numberW = leftW
  drawRoundedRect(leftX, topY, numberW, numberH, 22)
  const gradient = ctx.createLinearGradient(leftX, topY, leftX + numberW, topY + numberH)
  gradient.addColorStop(0, '#0b2c6f')
  gradient.addColorStop(0.6, '#1542a0')
  gradient.addColorStop(1, '#1d4ed8')
  ctx.fillStyle = gradient
  ctx.fill()
  ctx.fillStyle = 'rgba(255, 255, 255, 0.8)'
  ctx.font = '700 12px "Sora", sans-serif'
  ctx.textAlign = 'center'
  ctx.fillText('TICKET NO.', leftX + numberW / 2, topY + 30)
  ctx.font = '800 48px "Space Grotesk", "Sora", sans-serif'
  ctx.fillStyle = '#ffffff'
  ctx.fillText(ticket?.ticket_no || '---', leftX + numberW / 2, topY + 92)
  ctx.font = '500 14px "Sora", sans-serif'
  ctx.fillStyle = 'rgba(255, 255, 255, 0.85)'
  ctx.fillText('Please wait for your number to be called.', leftX + numberW / 2, topY + 124)

  const servicesY = topY + numberH + 20
  const servicesH = 150
  drawRoundedRect(leftX, servicesY, numberW, servicesH, 18)
  ctx.fillStyle = '#f7f9ff'
  ctx.fill()
  ctx.strokeStyle = 'rgba(11, 44, 111, 0.1)'
  ctx.lineWidth = 1
  ctx.stroke()

  ctx.textAlign = 'left'
  ctx.font = '700 12px "Sora", sans-serif'
  ctx.fillStyle = '#5b6b82'
  ctx.fillText('SERVICES IN THIS TICKET', leftX + 16, servicesY + 26)

  let pillX = leftX + 16
  let pillY = servicesY + 44
  const pillHeight = 28
  const pillGap = 10
  ctx.font = '600 13px "Sora", sans-serif'
  selectedServices.forEach((serviceItem, index) => {
    const label = serviceItem.name || 'Service'
    const number = String(index + 1)
    const numberWidth = ctx.measureText(number).width + 16
    const textWidth = ctx.measureText(label).width + 16
    const pillWidth = numberWidth + textWidth + 8
    if (pillX + pillWidth > leftX + numberW - 16) {
      pillX = leftX + 16
      pillY += pillHeight + 10
    }
    drawRoundedRect(pillX, pillY, pillWidth, pillHeight, 999)
    ctx.fillStyle = 'rgba(11, 44, 111, 0.08)'
    ctx.fill()
    ctx.fillStyle = '#0b2c6f'
    drawRoundedRect(pillX + 6, pillY + 4, 20, 20, 999)
    ctx.fillStyle = 'rgba(242, 195, 0, 0.4)'
    ctx.fill()
    ctx.fillStyle = '#0b2c6f'
    ctx.textAlign = 'center'
    ctx.fillText(number, pillX + 16, pillY + 18)
    ctx.textAlign = 'left'
    ctx.fillText(label, pillX + 32, pillY + 18)
    pillX += pillWidth + pillGap
  })

  const barcodeY = servicesY + servicesH + 20
  drawRoundedRect(leftX, barcodeY, numberW, 26, 12)
  ctx.fillStyle = 'rgba(11, 44, 111, 0.08)'
  ctx.fill()
  ctx.fillStyle = 'rgba(11, 44, 111, 0.4)'
  for (let i = 0; i < numberW - 12; i += 8) {
    ctx.fillRect(leftX + 6 + i, barcodeY + 5, 3, 16)
  }

  const rightPanelX = rightX
  const rightPanelW = rightW
  const metaH = 140
  drawRoundedRect(rightPanelX, topY, rightPanelW, metaH, 18)
  ctx.fillStyle = '#f8fbff'
  ctx.fill()
  ctx.strokeStyle = 'rgba(11, 44, 111, 0.1)'
  ctx.stroke()

  ctx.font = '600 14px "Sora", sans-serif'
  ctx.fillStyle = '#4b5b73'
  ctx.textAlign = 'left'
  const metaStartY = topY + 30
  const metaLine = 36
  ctx.fillText('Resident', rightPanelX + 16, metaStartY)
  ctx.fillText('Service summary', rightPanelX + 16, metaStartY + metaLine)
  ctx.fillText('Issued', rightPanelX + 16, metaStartY + metaLine * 2)
  ctx.fillStyle = '#0b1f3a'
  ctx.textAlign = 'right'
  ctx.fillText(residentName.value, rightPanelX + rightPanelW - 16, metaStartY)
  ctx.fillText(serviceLabel.value, rightPanelX + rightPanelW - 16, metaStartY + metaLine)
  ctx.fillText(issuedAt.value, rightPanelX + rightPanelW - 16, metaStartY + metaLine * 2)

  const reqY = topY + metaH + 18
  const reqH = 190
  drawRoundedRect(rightPanelX, reqY, rightPanelW, reqH, 18)
  ctx.fillStyle = '#f9fbff'
  ctx.fill()
  ctx.strokeStyle = 'rgba(11, 44, 111, 0.1)'
  ctx.stroke()

  ctx.textAlign = 'left'
  ctx.font = '700 12px "Sora", sans-serif'
  ctx.fillStyle = '#5b6b82'
  ctx.fillText('REQUIREMENTS TO PREPARE', rightPanelX + 16, reqY + 26)

  const reqColWidth = (rightPanelW - 32) / 2
  const reqStartY = reqY + 50
  ctx.font = '600 13px "Sora", sans-serif'
  ctx.fillStyle = '#475569'
  requirements.value.forEach((item, index) => {
    const col = index % 2
    const row = Math.floor(index / 2)
    const x = rightPanelX + 16 + col * reqColWidth
    const y = reqStartY + row * 28
    ctx.beginPath()
    ctx.arc(x + 5, y - 4, 3, 0, Math.PI * 2)
    ctx.fillStyle = 'rgba(11, 44, 111, 0.4)'
    ctx.fill()
    ctx.fillStyle = '#475569'
    wrapText(item, x + 14, y, reqColWidth - 20, 16)
  })

  ctx.textAlign = 'right'
  ctx.font = '700 11px "Sora", sans-serif'
  ctx.fillStyle = '#6b7da3'
  ctx.fillText('ISSUED BY KIOSK', rightPanelX + rightPanelW - 16, reqY + reqH - 16)

  canvas.toBlob((blob) => {
    if (!blob) return
    const url = URL.createObjectURL(blob)
    const anchor = document.createElement('a')
    anchor.href = url
    anchor.download = `kiosk-ticket-${ticket?.ticket_no || 'queue'}.png`
    document.body.appendChild(anchor)
    anchor.click()
    anchor.remove()
    URL.revokeObjectURL(url)
  }, 'image/png')
}

const handleMainMenu = () => {
  if (showThanks.value) return
  showThanks.value = true
  if (thanksTimer.value) {
    clearTimeout(thanksTimer.value)
  }
  thanksTimer.value = setTimeout(() => {
    router.push('/welcome')
  }, 1600)
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
  if (thanksTimer.value) {
    clearTimeout(thanksTimer.value)
  }
  if (stepFlashTimer.value) {
    clearTimeout(stepFlashTimer.value)
  }
  if (stageRevealTimer.value) {
    clearTimeout(stageRevealTimer.value)
  }
})
</script>
