<template>
  <div class="portal-shell" :class="statusTone">
    <header class="portal-header">
      <div class="brand">
        <img class="brand-logo" src="/logo.png" alt="Barangay logo" />
        <div class="brand-stack">
          <h1 class="brand-title">Barangay Resident Portal</h1>
          <p class="brand-kicker">Resident dashboard</p>
        </div>
      </div>
      <div class="header-actions">
        <a class="header-help" :href="helpLink" aria-label="Help">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
            <path d="M9.5 9.5a2.5 2.5 0 1 1 4.1 2c-.9.5-1.6 1.1-1.6 2.2v.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            <circle cx="12" cy="17" r="1" fill="currentColor" />
          </svg>
        </a>
        <div class="profile" @click.stop>
          <button class="profile-toggle" type="button" @click="toggleProfileMenu">
            <span class="profile-initial">{{ profileInitials }}</span>
            <span class="profile-label">Profile</span>
            <svg class="profile-caret" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <div v-if="isProfileMenuOpen" class="profile-menu">
            <router-link class="profile-item" to="/profile" @click="closeProfileMenu">
              View profile
            </router-link>
            <button class="profile-item" type="button" @click="onLogout">
              Log out
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="dashboard-body">
      <div class="sunray" aria-hidden="true"></div>
      <section class="dashboard-hero">
        <div class="hero-copy">
          <span class="status-pill" :class="`status-${status}`">{{ statusCopy }}</span>
          <h2 class="hero-title">Welcome, {{ residentName }}</h2>
          <p class="hero-subtitle">{{ statusMessage }}</p>
          <p class="hero-note">{{ statusDetail }}</p>
          <div class="hero-actions">
            <button
              class="primary-button"
              type="button"
              :disabled="status !== 'approved' || !idCardPngUrl || isIdExporting"
              @click="downloadResidentId"
            >
              {{ isIdExporting ? 'Preparing ID...' : 'Download ID' }}
            </button>
            <router-link class="ghost-button" to="/profile">Update profile</router-link>
            <button class="ghost-button" type="button" @click="refreshStatus">Refresh status</button>
          </div>
          <span v-if="copyStatus" class="copy-status copy-indicator">{{ copyStatus }}</span>
          <div v-if="errorMessage" class="status-alert">
            {{ errorMessage }}
          </div>
        </div>

        <div class="hero-progress">
          <h3>Verification journey</h3>
          <div class="status-steps">
            <div class="status-step is-complete">
              <span>Registration submitted</span>
            </div>
            <div class="status-step" :class="{ 'is-complete': status !== 'pending' }">
              <span>Admin verification</span>
            </div>
            <div class="status-step" :class="{ 'is-complete': status === 'approved' }">
              <span>Resident ID ready</span>
            </div>
          </div>
          <p class="hero-meta-note">
            Need changes? Update your profile and re-submit if requested by the barangay.
          </p>
        </div>
      </section>

      <section class="dashboard-grid">
        <section class="dashboard-card id-card-panel">
          <span class="card-orbs" aria-hidden="true"></span>
          <span class="card-corner" aria-hidden="true"></span>
          <div class="card-header">
            <div>
              <p class="card-kicker">Resident ID</p>
              <h3 class="card-title">Digital Resident ID</h3>
              <p class="card-subtitle">Your QR is embedded for kiosk validation.</p>
            </div>
            <span class="status-pill" :class="`status-${status}`">{{ statusCopy }}</span>
          </div>
          <div class="id-preview" :class="{ locked: status !== 'approved' }">
            <img
              v-if="status === 'approved' && idCardPngUrl"
              class="id-preview-image"
              :src="idCardPngUrl"
              alt="Resident ID preview"
            />
            <div v-else class="id-placeholder">
              <p v-if="status === 'approved'">Generating your Resident ID...</p>
              <p v-else>Your Resident ID will appear after verification.</p>
            </div>
          </div>
          <div class="id-actions">
            <button class="ghost-button" type="button" @click="copyResidentId">
              Copy ID
            </button>
            <button
              class="ghost-button"
              type="button"
              :disabled="status !== 'approved'"
              @click="copyResidentCode"
            >
              Copy code
            </button>
            <button
              class="primary-button"
              type="button"
              :disabled="status !== 'approved' || !idCardPngUrl || isIdExporting"
              @click="downloadResidentId"
            >
              {{ isIdExporting ? 'Preparing ID...' : 'Download ID' }}
            </button>
          </div>
          <div class="id-meta-grid">
            <div class="id-meta">
              <span class="meta-label">Resident ID</span>
              <span class="meta-value">{{ residentIdDisplay }}</span>
            </div>
            <div class="id-meta">
              <span class="meta-label">Resident code</span>
              <span class="meta-value">{{ residentCode }}</span>
            </div>
            <div class="id-meta">
              <span class="meta-label">Issued</span>
              <span class="meta-value">{{ idIssuedOn || 'Pending approval' }}</span>
            </div>
          </div>
          <p class="id-note">
            Keep a copy on your phone or print the card for faster service.
          </p>
          <div v-if="idCardError" class="status-alert">
            {{ idCardError }}
          </div>
        </section>

        <div class="dashboard-stack">
          <section class="dashboard-card">
            <div class="card-header">
              <div>
                <p class="card-kicker">Profile overview</p>
                <h3 class="card-title">Resident details</h3>
                <p class="card-subtitle">Make sure your contact info stays current.</p>
              </div>
            </div>
            <div class="detail-list">
              <div class="detail-row">
                <span class="detail-label">Full name</span>
                <span class="detail-value">{{ residentName }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ resident?.email || 'Not provided' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Mobile</span>
                <span class="detail-value">{{ resident?.mobile_number || 'Not provided' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Address</span>
                <span class="detail-value">{{ resident?.address || 'Not provided' }}</span>
              </div>
            </div>
          </section>

          <section class="dashboard-card">
            <div class="card-header">
              <div>
                <p class="card-kicker">Transactions</p>
                <h3 class="card-title">Recent activity</h3>
                <p class="card-subtitle">Completed kiosk requests and barangay services.</p>
              </div>
            </div>
            <ul v-if="transactions.length" class="transaction-list">
              <li v-for="(item, index) in transactions" :key="item.id || index" class="transaction-item">
                <div>
                  <p class="transaction-title">{{ item.title || item.service || 'Service request' }}</p>
                  <p class="transaction-meta">{{ item.date || item.issued_at || 'Date not available' }}</p>
                </div>
                <span class="transaction-status" :class="`status-${item.status}`">
                  {{ item.status || 'pending' }}
                </span>
              </li>
            </ul>
            <div v-else class="empty-state">
              No transactions yet. Your completed kiosk requests will show here.
            </div>
          </section>

          <section class="dashboard-card">
            <div class="card-header">
              <div>
                <p class="card-kicker">Support</p>
                <h3 class="card-title">Need assistance?</h3>
                <p class="card-subtitle">Contact the barangay for updates or corrections.</p>
              </div>
            </div>
            <div class="support-panel">
              <p class="support-text">We respond during office hours and will guide you on next steps.</p>
              <a class="primary-button" :href="helpLink">Contact support</a>
            </div>
          </section>
        </div>
      </section>
      <div class="buildings-back" aria-hidden="true"></div>
    </main>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { baseUrl, request } from '../api'
import QRCode from 'qrcode'

const router = useRouter()
const resident = ref(null)
const status = ref('pending')
const errorMessage = ref('')
const idCardError = ref('')
const copyStatus = ref('')
const isProfileMenuOpen = ref(false)
const idCardPngUrl = ref('')
const isIdExporting = ref(false)
const idIssuedOn = ref('')
const helpLink = 'mailto:helpdesk@barangay.local'

const residentName = computed(() => {
  if (!resident.value) return 'Resident'
  return [resident.value.first_name, resident.value.middle_name, resident.value.last_name]
    .filter(Boolean)
    .join(' ')
    .trim() || 'Resident'
})

const profileInitials = computed(() => {
  if (!resident.value) return 'RS'
  return residentName.value
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('')
})

const statusTone = computed(() => {
  if (status.value === 'approved') return 'tone-approved'
  if (status.value === 'rejected') return 'tone-rejected'
  return 'tone-pending'
})

const statusCopy = computed(() => {
  if (status.value === 'approved') return 'Verified'
  if (status.value === 'rejected') return 'Not approved'
  return 'Pending review'
})

const statusMessage = computed(() => {
  if (status.value === 'approved') {
    return 'You are verified and ready to access barangay services.'
  }
  if (status.value === 'rejected') {
    return 'Your registration needs an update before approval.'
  }
  return 'Your registration is being reviewed by the barangay team.'
})

const statusDetail = computed(() => {
  if (status.value === 'approved') {
    return 'Download your Resident ID and use the QR code at the kiosk.'
  }
  if (status.value === 'rejected') {
    return 'Review your profile details or visit the barangay office.'
  }
  return 'We will notify you once a decision is made.'
})

const transactions = computed(() => {
  const items = resident.value?.transactions
  return Array.isArray(items) ? items : []
})

const residentIdPadded = computed(() => {
  const id = resident.value?.id
  if (!id && id !== 0) return '000000'
  return String(id).padStart(6, '0')
})

const residentIdDisplay = computed(() => `BSM-RES-${residentIdPadded.value}`)

const qrTokenValue = computed(() => {
  const token = resident.value?.qr_token
  if (token) {
    return String(token).toUpperCase()
  }
  return residentIdPadded.value
})

const residentCodeValue = computed(() => `BSM-QR-${qrTokenValue.value}`)

const residentCode = computed(() => {
  return status.value === 'approved' ? residentCodeValue.value : 'Locked until approval'
})

const qrPayload = computed(() => `BSM|RESIDENT|${residentIdPadded.value}|${qrTokenValue.value}`)
const qrPngUrl = ref('')
const resolveAssetUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('data:') || url.startsWith('http://') || url.startsWith('https://')) return url
  return `${baseUrl}${url}`
}
const profilePhotoSrc = computed(() => {
  const url = resident.value?.profile_photo_url
  return resolveAssetUrl(url) || '/favicon.png'
})

const generateQr = async () => {
  if (status.value !== 'approved') {
    qrPngUrl.value = ''
    idCardPngUrl.value = ''
    idCardError.value = ''
    return
  }
  try {
    qrPngUrl.value = await QRCode.toDataURL(qrPayload.value, {
      width: 600,
      margin: 1,
      errorCorrectionLevel: 'M',
      color: {
        dark: '#0b2c6f',
        light: '#ffffff',
      },
    })
    await buildResidentIdImage()
  } catch (err) {
    qrPngUrl.value = ''
    idCardPngUrl.value = ''
  }
}

const toggleProfileMenu = () => {
  isProfileMenuOpen.value = !isProfileMenuOpen.value
}

const closeProfileMenu = () => {
  isProfileMenuOpen.value = false
}

const onLogout = () => {
  localStorage.removeItem('resident_token')
  localStorage.removeItem('resident_profile')
  router.push('/')
}

const hydrateFromCache = () => {
  const cached = localStorage.getItem('resident_profile')
  if (!cached) return
  try {
    const data = JSON.parse(cached)
    if (data) {
      resident.value = { ...resident.value, ...data }
      status.value = data.status || status.value || 'pending'
    }
  } catch (err) {
    return
  }
}

const refreshStatus = async () => {
  errorMessage.value = ''
  const token = localStorage.getItem('resident_token')
  if (!token) {
    errorMessage.value = 'Please sign in again to refresh your status.'
    return
  }

  try {
    const data = await request('/api/resident/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
    resident.value = { ...resident.value, ...data.resident }
    status.value = data.resident?.status || status.value || 'pending'
    localStorage.setItem('resident_profile', JSON.stringify(resident.value))
  } catch (err) {
    errorMessage.value = err?.message || 'Unable to refresh your status.'
  }
}

const copyToClipboard = async (value, successMessage, errorMessageText) => {
  if (!navigator?.clipboard?.writeText) {
    errorMessage.value = 'Copy is not supported in this browser.'
    return
  }
  try {
    await navigator.clipboard.writeText(value)
    copyStatus.value = successMessage
    window.setTimeout(() => {
      copyStatus.value = ''
    }, 2000)
  } catch (err) {
    errorMessage.value = errorMessageText
  }
}

const copyResidentId = () => {
  copyToClipboard(residentIdDisplay.value, 'Resident ID copied.', 'Unable to copy the resident ID.')
}

const copyResidentCode = () => {
  if (status.value !== 'approved') {
    errorMessage.value = 'Resident code is locked until approval.'
    return
  }
  copyToClipboard(residentCodeValue.value, 'Resident code copied.', 'Unable to copy the resident code.')
}

const loadImage = (src) => new Promise((resolve, reject) => {
  const img = new Image()
  img.crossOrigin = 'anonymous'
  img.onload = () => resolve(img)
  img.onerror = reject
  img.src = src
})

const formatDate = (date) => {
  return date.toLocaleDateString('en-PH', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
  })
}

const buildResidentIdImage = async () => {
  if (status.value !== 'approved' || !qrPngUrl.value) {
    idCardPngUrl.value = ''
    return
  }
  isIdExporting.value = true
  idCardError.value = ''
  try {
    const canvas = document.createElement('canvas')
    const width = 1000
    const height = 630
    canvas.width = width
    canvas.height = height
    const ctx = canvas.getContext('2d')
    if (!ctx) {
      throw new Error('Canvas is not supported.')
    }

    const background = ctx.createLinearGradient(0, 0, width, height)
    background.addColorStop(0, '#f8fafc')
    background.addColorStop(1, '#eef2ff')
    ctx.fillStyle = background
    ctx.fillRect(0, 0, width, height)

    const bandWidth = 340
    const band = ctx.createLinearGradient(0, 0, bandWidth, height)
    band.addColorStop(0, '#0b2c6f')
    band.addColorStop(1, '#1e40af')
    ctx.fillStyle = band
    ctx.fillRect(0, 0, bandWidth, height)

    ctx.fillStyle = 'rgba(255, 255, 255, 0.18)'
    ctx.beginPath()
    ctx.moveTo(bandWidth - 80, 0)
    ctx.lineTo(bandWidth, 0)
    ctx.lineTo(bandWidth - 120, height)
    ctx.lineTo(bandWidth - 200, height)
    ctx.closePath()
    ctx.fill()

    ctx.fillStyle = '#ffffff'
    const titleText = 'Barangay San Miguel'
    let titleFontSize = 26
    ctx.font = `700 ${titleFontSize}px "Space Grotesk", "Trebuchet MS", sans-serif`
    const maxTitleWidth = bandWidth - 60
    if (ctx.measureText(titleText).width > maxTitleWidth) {
      titleFontSize = 22
      ctx.font = `700 ${titleFontSize}px "Space Grotesk", "Trebuchet MS", sans-serif`
    }
    ctx.fillText(titleText, 28, 64)
    ctx.font = '600 13px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText('Official Resident ID', 28, 90)

    const photoCenterX = 170
    const photoCenterY = 235
    const photoRadius = 86
    ctx.fillStyle = 'rgba(255, 255, 255, 0.2)'
    ctx.beginPath()
    ctx.arc(photoCenterX, photoCenterY, photoRadius, 0, Math.PI * 2)
    ctx.fill()
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)'
    ctx.lineWidth = 2
    ctx.stroke()
    try {
      const photo = await loadImage(profilePhotoSrc.value)
      ctx.save()
      ctx.beginPath()
      ctx.arc(photoCenterX, photoCenterY, photoRadius - 4, 0, Math.PI * 2)
      ctx.clip()
      const targetSize = (photoRadius - 4) * 2
      const scale = Math.max(targetSize / photo.width, targetSize / photo.height)
      const drawW = photo.width * scale
      const drawH = photo.height * scale
      const drawX = photoCenterX - drawW / 2
      const drawY = photoCenterY - drawH / 2
      ctx.drawImage(photo, drawX, drawY, drawW, drawH)
      ctx.restore()
    } catch (err) {
      // keep placeholder if image fails to load
    }
    // No caption under profile photo.

    const rightX = bandWidth + 30
    ctx.fillStyle = '#64748b'
    ctx.font = '700 14px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText('RESIDENT ID', rightX, 70)
    ctx.fillStyle = '#0b2c6f'
    ctx.font = '700 28px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText(residentIdDisplay.value, rightX, 105)

    ctx.fillStyle = '#64748b'
    ctx.font = '600 13px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText('RESIDENT CODE', rightX, 138)
    ctx.fillStyle = '#0f172a'
    ctx.font = '600 18px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText(residentCodeValue.value, rightX, 162)

    const detailStartY = 210
    const lineGap = 52
    const drawDetail = (label, value, x, y, maxWidth = 360) => {
      ctx.fillStyle = '#64748b'
      ctx.font = '600 12px "Space Grotesk", "Trebuchet MS", sans-serif'
      ctx.fillText(label, x, y)
      ctx.fillStyle = '#0f172a'
      ctx.font = '600 18px "Space Grotesk", "Trebuchet MS", sans-serif'
      const clipped = value.length > 26 ? `${value.slice(0, 25)}...` : value
      ctx.fillText(clipped, x, y + 22, maxWidth)
    }

    drawDetail('FULL NAME', residentName.value.toUpperCase(), rightX, detailStartY)
    drawDetail('USERNAME', (resident.value?.username || 'Not provided').toString(), rightX, detailStartY + lineGap)
    drawDetail('DATE OF BIRTH', resident.value?.date_of_birth || 'Not provided', rightX, detailStartY + lineGap * 2)
    drawDetail('GENDER', resident.value?.gender || 'Not provided', rightX, detailStartY + lineGap * 3)

    const rightColX = 640
    drawDetail('MOBILE', resident.value?.mobile_number || 'Not provided', rightColX, detailStartY)
    drawDetail('CIVIL STATUS', resident.value?.civil_status || 'Not provided', rightColX, detailStartY + lineGap)

    ctx.fillStyle = '#64748b'
    ctx.font = '600 12px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText('ADDRESS', rightColX, detailStartY + lineGap * 2)
    ctx.fillStyle = '#0f172a'
    ctx.font = '600 16px "Space Grotesk", "Trebuchet MS", sans-serif'
    const address = resident.value?.address || 'Not provided'
    const addressLines = []
    const words = address.split(' ')
    let line = ''
    words.forEach((word) => {
      const testLine = line ? `${line} ${word}` : word
      if (ctx.measureText(testLine).width > 240 && line) {
        addressLines.push(line)
        line = word
      } else {
        line = testLine
      }
    })
    if (line) addressLines.push(line)
    addressLines.slice(0, 2).forEach((text, index) => {
      ctx.fillText(text, rightColX, detailStartY + lineGap * 2 + 24 + index * 18)
    })

    const qrImage = await loadImage(qrPngUrl.value)
    const qrBoxX = 740
    const qrBoxY = 400
    const qrBoxSize = 190
    const qrInset = 8
    ctx.fillStyle = '#ffffff'
    ctx.shadowColor = 'rgba(15, 23, 42, 0.15)'
    ctx.shadowBlur = 20
    ctx.fillRect(qrBoxX, qrBoxY, qrBoxSize, qrBoxSize)
    ctx.shadowBlur = 0
    ctx.drawImage(
      qrImage,
      qrBoxX + qrInset,
      qrBoxY + qrInset,
      qrBoxSize - qrInset * 2,
      qrBoxSize - qrInset * 2
    )

    idIssuedOn.value = formatDate(new Date())
    ctx.fillStyle = '#64748b'
    ctx.font = '600 12px "Space Grotesk", "Trebuchet MS", sans-serif'
    ctx.fillText(`Issued: ${idIssuedOn.value}`, 350, 590)
    ctx.fillText('Status: Verified', 350, 612)

    idCardPngUrl.value = canvas.toDataURL('image/png')
  } catch (err) {
    idCardPngUrl.value = ''
    idCardError.value = 'Unable to generate the Resident ID image.'
  } finally {
    isIdExporting.value = false
  }
}

const downloadResidentId = () => {
  if (!idCardPngUrl.value || status.value !== 'approved') {
    idCardError.value = 'Resident ID is not ready for download.'
    return
  }
  const link = document.createElement('a')
  link.href = idCardPngUrl.value
  link.download = `resident-id-${residentIdPadded.value}.png`
  link.click()
  copyStatus.value = 'Resident ID downloaded.'
  window.setTimeout(() => {
    copyStatus.value = ''
  }, 2000)
}

onMounted(() => {
  hydrateFromCache()
  refreshStatus()
  window.addEventListener('click', closeProfileMenu)
})

onBeforeUnmount(() => {
  window.removeEventListener('click', closeProfileMenu)
})

watch([qrPayload, status], () => {
  generateQr()
}, { immediate: true })

watch(resident, () => {
  if (status.value === 'approved' && qrPngUrl.value) {
    buildResidentIdImage()
  }
}, { deep: true })
</script>
