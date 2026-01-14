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

const residentCodeValue = computed(() => `BSM-QR-${residentIdPadded.value}`)

const residentCode = computed(() => {
  return status.value === 'approved' ? residentCodeValue.value : 'Locked until approval'
})

const qrPayload = computed(() => `BSM|RESIDENT|${residentIdPadded.value}|${residentCodeValue.value}`)
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

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Space+Grotesk:wght@400;500;600;700&display=swap");

.portal-shell {
  min-height: 100vh;
  --shell-pad: clamp(1.5rem, 3vw, 3.5rem);
  padding: 2.8rem var(--shell-pad) 4rem;
  font-family: "Space Grotesk", "Segoe UI", sans-serif;
  color: #0f172a;
  --ink: #111827;
  --ink-soft: #1f2937;
  --muted: #6b7280;
  --surface: rgba(255, 255, 255, 0.92);
  --surface-strong: #ffffff;
  --border: rgba(148, 163, 184, 0.45);
  --shadow: 0 30px 70px rgba(15, 23, 42, 0.15);
  --sky: #d7e7ff;
  --sun: #ffd87b;
  background:
    radial-gradient(circle at 15% 10%, rgba(255, 255, 255, 0.95), rgba(215, 231, 255, 0.65) 40%),
    radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.8), rgba(255, 216, 123, 0.45) 45%),
    linear-gradient(160deg, #edf2ff 10%, #f7f4ee 65%, #f1f5ff 100%);
  position: relative;
  overflow: hidden;
}

.portal-shell::before,
.portal-shell::after {
  content: "";
  position: absolute;
  border-radius: 50%;
  opacity: 0.5;
  pointer-events: none;
}

.portal-shell::before {
  width: 420px;
  height: 420px;
  background: radial-gradient(circle, rgba(19, 56, 122, 0.28), transparent 70%);
  top: -160px;
  right: -120px;
}

.portal-shell::after {
  width: 380px;
  height: 380px;
  background: radial-gradient(circle, rgba(245, 192, 88, 0.35), transparent 70%);
  bottom: -150px;
  left: -120px;
}

.portal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  position: relative;
  z-index: 2;
}

.brand {
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.brand-logo {
  width: 124px;
  height: 124px;
  border-radius: 0;
  object-fit: cover;
  box-shadow: none;
  background: transparent;
}

.brand-stack {
  display: grid;
  gap: 0.3rem;
}

.brand-title {
  margin: 0;
  font-size: clamp(2rem, 4vw, 3rem);
  font-family: "Trebuchet MS", "Gill Sans", "Noto Sans", sans-serif;
  color: #0b2c6f;
}

.brand-kicker {
  margin: 0;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #4b5563;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.header-help {
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  border-radius: 14px;
  background: #0b2c6f;
  border: 1px solid #0b2c6f;
  color: #ffffff;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.header-help svg {
  width: 18px;
  height: 18px;
}

.header-help:hover {
  transform: translateY(-1px);
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
}

.profile {
  position: relative;
}

.profile-toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  border-radius: 999px;
  padding: 0.45rem 0.95rem;
  border: 1px solid #0b2c6f;
  background: #0b2c6f;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
}

.profile-initial {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
  display: grid;
  place-items: center;
  font-size: 0.85rem;
  font-weight: 700;
}

.profile-label {
  font-size: 0.95rem;
  color: #ffffff;
}

.profile-caret {
  width: 16px;
  height: 16px;
  color: #ffffff;
}

.profile-menu {
  position: absolute;
  right: 0;
  top: calc(100% + 0.6rem);
  min-width: 180px;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: 0 18px 36px rgba(15, 23, 42, 0.15);
  padding: 0.4rem;
  display: grid;
  gap: 0.25rem;
  z-index: 20;
}

.profile-item {
  border: none;
  background: transparent;
  border-radius: 12px;
  padding: 0.6rem 0.8rem;
  text-align: left;
  font-weight: 600;
  color: var(--ink-soft);
  text-decoration: none;
  cursor: pointer;
}

.profile-item:hover {
  background: rgba(11, 44, 111, 0.08);
}

.hero {
  margin-top: 2.5rem;
  position: relative;
  z-index: 1;
  --building-height: clamp(120px, 12vw, 180px);
  padding-bottom: calc(var(--building-height) + 2rem);
  overflow: visible;
}

.hero-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
  gap: 2rem;
  align-items: stretch;
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.hero-card {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  gap: 2rem;
  background:
    linear-gradient(var(--surface), var(--surface)) padding-box,
    linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(11, 44, 111, 0.2), rgba(255, 216, 123, 0.35)) border-box;
  border-radius: 32px;
  padding: clamp(1.6rem, 3vw, 2.6rem);
  border: 1px solid transparent;
  box-shadow:
    0 34px 78px rgba(15, 23, 42, 0.2),
    inset 1px 1px 0 rgba(255, 255, 255, 0.9),
    inset -1px -1px 0 rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(16px);
  animation: floatIn 0.7s ease both;
  height: 100%;
  position: relative;
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.hero-card::before,
.hero-card::after {
  content: "";
  position: absolute;
  inset: -40% -30%;
  pointer-events: none;
  z-index: 0;
}

.hero-card::before {
  background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1px, transparent 0);
  background-size: 4px 4px;
  opacity: 0.35;
  mix-blend-mode: soft-light;
}

.hero-card::after {
  background: linear-gradient(120deg, transparent 20%, rgba(255, 255, 255, 0.65) 50%, transparent 80%);
  transform: translateX(-60%);
  animation: cardSheen 7s ease-in-out infinite;
  opacity: 0.6;
}

.hero-card > * {
  position: relative;
  z-index: 1;
}

.hero-card:hover {
  transform: translateY(-7px);
  box-shadow:
    0 44px 94px rgba(15, 23, 42, 0.28),
    inset 1px 1px 0 rgba(255, 255, 255, 0.92),
    inset -1px -1px 0 rgba(15, 23, 42, 0.1);
}

.hero-left {
  display: grid;
  gap: 1.3rem;
  align-content: start;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  padding: 0.35rem 1.2rem;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.3em;
  text-transform: uppercase;
  background: var(--tone, #f5c34a);
  color: var(--tone-ink, #111827);
  box-shadow: 0 12px 20px rgba(15, 23, 42, 0.12);
  width: fit-content;
}

.hero-title {
  margin: 0;
  font-size: clamp(1.8rem, 3vw, 2.4rem);
  font-weight: 700;
  color: #0b2c6f;
}

.hero-subtitle {
  margin: 0;
  color: var(--muted);
  font-size: 1.05rem;
  max-width: 520px;
}

.hero-highlights {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  gap: 1.2rem;
}

.highlight-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.6rem;
}

.highlight-copy {
  border: none;
  background: transparent;
  color: #0b2c6f;
  padding: 0;
  display: grid;
  place-items: center;
  cursor: pointer;
}

.highlight-copy svg {
  width: 30px;
  height: 30px;
}

.highlight-copy:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.highlight {
  padding: 0.9rem 1.1rem;
  border-radius: 18px;
  border: 1px solid rgba(11, 44, 111, 0.15);
  background: rgba(255, 255, 255, 0.85);
  display: grid;
  gap: 0.6rem;
}

.highlight.locked {
  opacity: 0.7;
}

.highlight-label {
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #64748b;
  font-weight: 700;
}

.highlight-value {
  font-size: 1.85rem;
  font-weight: 700;
  color: var(--ink-soft);
  word-break: break-word;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
}

.ghost-button,
.primary-button {
  padding: 0.6rem 1.4rem;
  border-radius: 999px;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.95rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ghost-button {
  border: 1px solid var(--border);
  color: var(--ink-soft);
  background: rgba(255, 255, 255, 0.9);
}

.primary-button {
  background: #0b2c6f;
  color: #ffffff;
  border: 1px solid #0b2c6f;
}

.ghost-button:hover,
.primary-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.15);
}

.hero-note {
  margin: 0;
  font-size: 0.95rem;
  color: #64748b;
}

.qr-showcase {
  margin: 0;
  max-width: 100%;
  width: 100%;
  padding: 1.6rem clamp(1.4rem, 2.6vw, 2.2rem);
  border-radius: 28px;
  border: 1px solid transparent;
  background:
    linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)) padding-box,
    linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(11, 44, 111, 0.18), rgba(37, 99, 235, 0.24)) border-box;
  box-shadow:
    0 28px 66px rgba(15, 23, 42, 0.2),
    inset 1px 1px 0 rgba(255, 255, 255, 0.9),
    inset -1px -1px 0 rgba(15, 23, 42, 0.08);
  text-align: left;
  display: grid;
  gap: 1.2rem;
  min-height: 100%;
  grid-template-rows: auto 1fr auto;
  height: 100%;
  position: relative;
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.qr-showcase::before,
.qr-showcase::after {
  content: "";
  position: absolute;
  inset: -40% -30%;
  pointer-events: none;
  z-index: 0;
}

.qr-showcase::before {
  background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1px, transparent 0);
  background-size: 4px 4px;
  opacity: 0.3;
  mix-blend-mode: soft-light;
}

.qr-showcase::after {
  background: linear-gradient(120deg, transparent 25%, rgba(255, 255, 255, 0.6) 50%, transparent 80%);
  transform: translateX(-60%);
  animation: cardSheen 8s ease-in-out infinite reverse;
  opacity: 0.55;
}

.qr-showcase > * {
  position: relative;
  z-index: 1;
}

.qr-showcase:hover {
  transform: translateY(-7px);
  box-shadow:
    0 42px 92px rgba(15, 23, 42, 0.28),
    inset 1px 1px 0 rgba(255, 255, 255, 0.92),
    inset -1px -1px 0 rgba(15, 23, 42, 0.1);
}

.sunray {
  position: fixed;
  inset: -60%;
  background-image:
    radial-gradient(circle at center, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0) 70%),
    repeating-conic-gradient(
      from 0deg,
      rgba(11, 44, 111, 0.12) 0deg,
      rgba(11, 44, 111, 0.12) 10deg,
      rgba(255, 255, 255, 0) 10deg,
      rgba(255, 255, 255, 0) 20deg
    );
  opacity: 0.6;
  filter: blur(0.8px);
  pointer-events: none;
  z-index: 0;
  transform-origin: 50% 50%;
  animation: raysPulse 8s ease-in-out infinite, raysRotate 180s linear infinite;
}

.buildings-back {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: var(--building-height);
  z-index: 0;
  background-image: url("/buildings2.png");
  background-repeat: repeat-x;
  background-size: auto 100%;
  background-position: center bottom;
  opacity: 0.9;
  pointer-events: none;
}

.buildings-back::after {
  content: "";
  position: absolute;
  inset: 0;
  background-color: rgba(255, 255, 255, 0.08);
  animation: shimmer 6s ease-in-out infinite;
  pointer-events: none;
}

.qr-showcase-head {
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 1rem;
  flex-wrap: wrap;
}

.qr-render {
  width: min(900px, 100%);
  aspect-ratio: 1 / 1;
  display: grid;
  place-items: center;
  margin: 0;
  background: linear-gradient(145deg, rgba(14, 116, 144, 0.2), rgba(37, 99, 235, 0.18));
  padding: 0.7rem;
  border-radius: 18px;
}

.qr-render svg {
  width: 100%;
  height: 100%;
  display: block;
  background: #ffffff;
  border-radius: 12px;
  padding: 0.6rem;
}

.qr-locked {
  font-size: 1.1rem;
  font-weight: 700;
  color: #94a3b8;
  min-height: 900px;
  display: grid;
  place-items: center;
  margin: 0;
  width: 100%;
  max-width: 900px;
}

.qr-glow {
  position: relative;
  z-index: 1;
  filter: drop-shadow(0 16px 28px rgba(37, 99, 235, 0.18));
}

.qr-glow::before,
.qr-glow::after {
  content: "";
  position: absolute;
  inset: -16px;
  border-radius: 32px;
  background: radial-gradient(circle at 30% 30%, rgba(59, 130, 246, 0.22), transparent 60%),
    radial-gradient(circle at 70% 70%, rgba(14, 116, 144, 0.16), transparent 65%);
  opacity: 0.6;
  z-index: -1;
  filter: blur(12px);
  animation: qrGlowDrift 6s ease-in-out infinite;
}

.qr-glow::after {
  inset: -28px;
  opacity: 0.35;
  filter: blur(20px);
  animation: qrGlowDrift 6s ease-in-out infinite reverse;
}

@keyframes qrGlowDrift {
  0% {
    transform: translate(0, 0) scale(1);
    filter: hue-rotate(0deg) blur(12px);
  }
  50% {
    transform: translate(10px, -8px) scale(1.03);
    filter: hue-rotate(18deg) blur(14px);
  }
  100% {
    transform: translate(-8px, 6px) scale(1);
    filter: hue-rotate(-12deg) blur(12px);
  }
}

@keyframes cardSheen {
  0% {
    transform: translateX(-60%);
  }
  50% {
    transform: translateX(60%);
  }
  100% {
    transform: translateX(-60%);
  }
}

@keyframes cardGlow {
  0%,
  100% {
    box-shadow:
      0 34px 78px rgba(15, 23, 42, 0.2),
      0 0 0 rgba(11, 44, 111, 0);
  }
  50% {
    box-shadow:
      0 40px 90px rgba(15, 23, 42, 0.25),
      0 0 28px rgba(11, 44, 111, 0.18);
  }
}

@keyframes orbDrift {
  0%,
  100% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(12px, -10px);
  }
}

.qr-actions {
  display: flex;
  flex-wrap: nowrap;
  gap: 0.8rem;
  justify-content: flex-start;
}

.card-reveal {
  opacity: 0.75;
  transform: translateY(8px);
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.hero-card:hover .card-reveal,
.qr-showcase:hover .card-reveal {
  opacity: 1;
  transform: translateY(0);
}

.card-corner {
  position: absolute;
  top: 0;
  right: 0;
  width: 90px;
  height: 90px;
  background: linear-gradient(135deg, rgba(11, 44, 111, 0.95), rgba(147, 197, 253, 0.85));
  clip-path: polygon(100% 0, 0 0, 100% 100%);
  opacity: 0.75;
  pointer-events: none;
  z-index: 2;
}

.card-orbs {
  position: absolute;
  inset: -10% -5%;
  background:
    radial-gradient(circle at 15% 20%, rgba(11, 44, 111, 0.12), transparent 55%),
    radial-gradient(circle at 80% 10%, rgba(245, 195, 74, 0.18), transparent 50%),
    radial-gradient(circle at 70% 80%, rgba(37, 99, 235, 0.14), transparent 60%);
  opacity: 0.6;
  filter: blur(2px);
  animation: orbDrift 10s ease-in-out infinite;
  pointer-events: none;
  z-index: 0;
}

.qr-actions .ghost-button,
.qr-actions .primary-button {
  flex: 1 1 0;
  text-align: center;
}

.copy-status {
  font-size: 0.85rem;
  color: #0b2c6f;
  font-weight: 600;
}

.copy-indicator {
  align-self: flex-start;
  background: rgba(11, 44, 111, 0.08);
  border-radius: 999px;
  padding: 0.35rem 0.9rem;
}

.status-alert {
  padding: 0.8rem 1.1rem;
  border-radius: 16px;
  background: rgba(248, 113, 113, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.4);
  color: #b91c1c;
  font-weight: 600;
  text-align: center;
}

.tone-approved {
  --tone: #2e7d32;
  --tone-ink: #f0fdf4;
  --tone-soft: rgba(46, 125, 50, 0.2);
}

.tone-rejected {
  --tone: #c0392b;
  --tone-ink: #fef2f2;
  --tone-soft: rgba(192, 57, 43, 0.2);
}

.tone-pending {
  --tone: #f5c34a;
  --tone-ink: #1f2937;
  --tone-soft: rgba(245, 195, 74, 0.22);
}

.tone-approved .hero-card,
.tone-approved .qr-showcase {
  animation: cardGlow 6s ease-in-out infinite;
}

@media (max-width: 960px) {
  .hero-grid {
    grid-template-columns: 1fr;
  }

  .qr-showcase {
    margin-top: 2rem;
  }

  .qr-actions {
    flex-wrap: wrap;
  }
}

@media (max-width: 640px) {
  .portal-shell {
    padding: 2.4rem 1.3rem 3rem;
  }

  .brand {
    flex-direction: column;
    align-items: flex-start;
  }

  .brand-logo {
    width: 82px;
    height: 82px;
  }

  .hero-highlights {
    grid-template-columns: 1fr;
  }

  .hero-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .qr-showcase-head {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (prefers-reduced-motion: reduce) {
  .hero-card,
  .hero-card::after,
  .sunray,
  .qr-showcase::after,
  .card-orbs,
  .tone-approved .hero-card,
  .tone-approved .qr-showcase,
  .qr-glow::before,
  .qr-glow::after {
    animation: none;
  }
}

@keyframes floatIn {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes riseIn {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes raysPulse {
  0%,
  100% {
    opacity: 0.45;
    transform: scale(1);
  }
  50% {
    opacity: 0.75;
    transform: scale(1.025);
  }
}

@keyframes raysRotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
