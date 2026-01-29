<template>
  <div class="portal-shell" :class="statusTone">
    <header class="portal-header">
      <div class="brand">
        <img class="brand-logo" src="/logo.png" alt="Barangay logo" />
        <div class="brand-stack">
          <h1 class="brand-title">Baranggay Profile</h1>
          <p class="brand-kicker">Community verification dashboard</p>
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

    <section class="hero">
      <div class="sunray" aria-hidden="true"></div>
      <div class="hero-grid">
        <div class="hero-card">
          <span class="card-orbs" aria-hidden="true"></span>
          <span class="card-corner" aria-hidden="true"></span>
          <div class="hero-left">
            <h2 class="hero-title">Welcome, {{ residentName }}</h2>
            <p class="hero-subtitle">{{ statusMessage }}</p>

            <div class="hero-highlights">
              <div class="highlight">
                <div class="highlight-row">
                  <span class="highlight-label">Resident ID</span>
                  <button class="highlight-copy" type="button" aria-label="Copy resident ID" @click="copyResidentId">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <rect x="8" y="8" width="10" height="12" rx="2" stroke="currentColor" stroke-width="1.6" />
                      <rect x="5" y="4" width="10" height="12" rx="2" stroke="currentColor" stroke-width="1.6" />
                    </svg>
                  </button>
                </div>
                <span class="highlight-value">{{ residentIdDisplay }}</span>
              </div>
              <div class="highlight" :class="{ locked: status !== 'approved' }">
                <div class="highlight-row">
                  <span class="highlight-label">Resident code</span>
                  <button
                    class="highlight-copy"
                    type="button"
                    aria-label="Copy resident code"
                    :disabled="status !== 'approved'"
                    @click="copyResidentCode"
                  >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <rect x="8" y="8" width="10" height="12" rx="2" stroke="currentColor" stroke-width="1.6" />
                      <rect x="5" y="4" width="10" height="12" rx="2" stroke="currentColor" stroke-width="1.6" />
                    </svg>
                  </button>
                </div>
                <span class="highlight-value">{{ residentCode }}</span>
              </div>
            </div>

            <div class="hero-actions card-reveal">
              <router-link class="ghost-button" to="/profile">Update profile</router-link>
              <a class="primary-button" :href="helpLink">Contact support</a>
            </div>

            <span v-if="copyStatus" class="copy-status copy-indicator">{{ copyStatus }}</span>
            <p class="hero-note">{{ statusDetail }}</p>
          </div>
        </div>

        <div class="qr-showcase">
          <span class="card-orbs" aria-hidden="true"></span>
          <span class="card-corner" aria-hidden="true"></span>
          <div class="qr-showcase-head">
            <h2 class="hero-title">Resident QR</h2>
          </div>
          <div v-if="status === 'approved' && qrSvg" class="qr-render qr-glow" v-html="qrSvg"></div>
          <div v-else-if="status === 'approved'" class="qr-locked">Generating QR</div>
          <div v-else class="qr-locked">QR locked</div>
          <div class="qr-actions card-reveal">
            <button class="ghost-button" type="button" :disabled="status !== 'approved'" @click="downloadQr">
              Download QR
            </button>
            <button class="primary-button" type="button" :disabled="status !== 'approved'" @click="copyQr">
              Copy QR
            </button>
          </div>
          <div v-if="errorMessage" class="status-alert">
            {{ errorMessage }}
          </div>
        </div>
      </div>
      <div class="buildings-back" aria-hidden="true"></div>
    </section>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'
import QRCode from 'qrcode'

const router = useRouter()
const resident = ref(null)
const status = ref('pending')
const errorMessage = ref('')
const copyStatus = ref('')
const isProfileMenuOpen = ref(false)
const helpLink = 'mailto:helpdesk@barangay.local'

const residentName = computed(() => {
  if (!resident.value) return 'Resident'
  return `${resident.value.first_name} ${resident.value.last_name}`.trim() || 'Resident'
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
    return 'Use your resident code to start a queue at the kiosk.'
  }
  if (status.value === 'rejected') {
    return 'Review your profile details or visit the barangay office.'
  }
  return 'We will notify you once a decision is made.'
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
const qrSvg = ref('')
const qrPngUrl = ref('')

const generateQr = async () => {
  if (status.value !== 'approved') {
    qrSvg.value = ''
    qrPngUrl.value = ''
    return
  }
  try {
    qrSvg.value = await QRCode.toString(qrPayload.value, {
      type: 'svg',
      width: 220,
      margin: 1,
      errorCorrectionLevel: 'M',
      color: {
        dark: '#0b2c6f',
        light: '#ffffff',
      },
    })
    qrPngUrl.value = await QRCode.toDataURL(qrPayload.value, {
      width: 600,
      margin: 1,
      errorCorrectionLevel: 'M',
      color: {
        dark: '#0b2c6f',
        light: '#ffffff',
      },
    })
  } catch (err) {
    qrSvg.value = ''
    qrPngUrl.value = ''
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
      resident.value = data
      status.value = data.status || 'pending'
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
    resident.value = data.resident
    status.value = data.resident?.status || 'pending'
    localStorage.setItem('resident_profile', JSON.stringify(data.resident))
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

const downloadQr = () => {
  if (!qrPngUrl.value || status.value !== 'approved') {
    errorMessage.value = 'QR is not ready for download.'
    return
  }
  const link = document.createElement('a')
  link.href = qrPngUrl.value
  link.download = `resident-qr-${residentIdPadded.value}.png`
  link.click()
  copyStatus.value = 'QR downloaded.'
  window.setTimeout(() => {
    copyStatus.value = ''
  }, 2000)
}

const copyQr = async () => {
  if (status.value !== 'approved') {
    errorMessage.value = 'Resident QR is locked until approval.'
    return
  }
  if (!qrPngUrl.value) {
    errorMessage.value = 'QR is not ready to copy.'
    return
  }
  if (window.ClipboardItem && navigator?.clipboard?.write) {
    try {
      const blob = await fetch(qrPngUrl.value).then((res) => res.blob())
      await navigator.clipboard.write([new ClipboardItem({ [blob.type]: blob })])
      copyStatus.value = 'QR copied to clipboard.'
      window.setTimeout(() => {
        copyStatus.value = ''
      }, 2000)
      return
    } catch (err) {
      errorMessage.value = 'Unable to copy the QR image.'
    }
  }
  copyToClipboard(qrPayload.value, 'QR payload copied.', 'Unable to copy the QR payload.')
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
</script>
