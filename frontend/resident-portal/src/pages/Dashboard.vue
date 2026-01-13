<template>
  <div class="dash-shell">
    <header class="dash-top">
      <div class="dash-title-stack">
        <span class="dash-badge">Resident Portal</span>
        <h1 class="dash-title">
          Barangay San Miguel
          <span class="dash-title-accent">Registration overview</span>
        </h1>
        <p class="dash-subtitle">Track your verification status and prepare for barangay services.</p>
      </div>
      <div class="dash-actions">
        <a class="dash-help" :href="helpLink" aria-label="Help">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
            <path d="M9.5 9.5a2.5 2.5 0 1 1 4.1 2c-.9.5-1.6 1.1-1.6 2.2v.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            <circle cx="12" cy="17" r="1" fill="currentColor" />
          </svg>
        </a>
        <div class="dash-profile" @click.stop>
          <button class="dash-profile-toggle" type="button" @click="toggleProfileMenu">
            <span class="dash-profile-initial">{{ profileInitials }}</span>
            <span class="dash-profile-label">Profile</span>
            <svg class="dash-profile-caret" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <div v-if="isProfileMenuOpen" class="dash-profile-menu">
            <router-link class="dash-profile-item" to="/profile" @click="closeProfileMenu">
              View profile
            </router-link>
            <button class="dash-profile-item" type="button" @click="onLogout">
              Log out
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="dash-grid">
      <section class="dash-panel dash-hero" :class="statusTone">
        <div class="hero-header">
          <div class="status-pill">{{ statusCopy }}</div>
          <h2 class="welcome-title">Hello, {{ residentName }}</h2>
          <p class="welcome-subtitle">{{ statusMessage }}</p>
        </div>

        <div class="resident-focus">
          <div class="resident-pass">
            <div class="pass-top">
              <p class="pass-label">Resident ID</p>
              <p class="pass-id">#{{ resident?.id || '---' }}</p>
              <span class="pass-status">{{ statusCopy }}</span>
            </div>
            <div class="pass-qr" :class="{ 'is-locked': status !== 'approved' }">
              <p class="pass-qr-label">{{ status === 'approved' ? 'Scan QR' : 'Awaiting approval' }}</p>
              <div v-if="status === 'approved'" class="qr-code">RESIDENT #{{ resident?.id || '---' }}</div>
              <div v-else class="qr-locked">QR locked</div>
            </div>
            <div class="pass-code" :class="{ 'is-locked': status !== 'approved' }">
              <p class="pass-code-label">Resident code</p>
              <p class="pass-code-value">{{ residentCode }}</p>
              <button v-if="status === 'approved'" class="pass-code-action" type="button" @click="copyCode">
                Copy code
              </button>
              <span v-if="copyStatus" class="copy-status">{{ copyStatus }}</span>
            </div>
          </div>
        </div>

        <p class="pass-note">{{ statusDetail }}</p>

        <div v-if="errorMessage" class="status-alert">
          {{ errorMessage }}
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

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
  if (status.value === 'approved') return 'Approved'
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

const residentCodeValue = computed(() => `RESIDENT-${resident.value?.id || '---'}`)

const residentCode = computed(() => {
  return status.value === 'approved' ? residentCodeValue.value : 'Locked until approval'
})

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

const copyCode = async () => {
  if (!navigator?.clipboard?.writeText) {
    errorMessage.value = 'Copy is not supported in this browser.'
    return
  }
  try {
    await navigator.clipboard.writeText(residentCodeValue.value)
    copyStatus.value = 'Copied to clipboard.'
    window.setTimeout(() => {
      copyStatus.value = ''
    }, 2000)
  } catch (err) {
    errorMessage.value = 'Unable to copy the resident code.'
  }
}

onMounted(() => {
  hydrateFromCache()
  refreshStatus()
  window.addEventListener('click', closeProfileMenu)
})

onBeforeUnmount(() => {
  window.removeEventListener('click', closeProfileMenu)
})
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Fraunces:wght@600;700&display=swap");

.dash-shell {
  min-height: 100vh;
  padding: 3.5rem 2.75rem 4rem;
  position: relative;
  overflow: hidden;
  color: #0f172a;
  font-family: "Plus Jakarta Sans", "Trebuchet MS", "Noto Sans", sans-serif;
  --ink: #0b2c6f;
  --ink-deep: #0f172a;
  --gold: #f4c542;
  --surface: rgba(255, 255, 255, 0.92);
  --surface-strong: #ffffff;
  --border: rgba(148, 163, 184, 0.35);
  --shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
  --muted: #6b7280;
  background:
    radial-gradient(circle at 10% 5%, rgba(255, 255, 255, 0.9), rgba(233, 240, 255, 0.8) 45%),
    radial-gradient(circle at 90% 10%, rgba(255, 255, 255, 0.85), rgba(244, 246, 255, 0.9) 50%),
    linear-gradient(160deg, #eff3fb, #e4ebf8 60%, #f7f6f0);
}

.dash-shell::before,
.dash-shell::after {
  content: "";
  position: absolute;
  border-radius: 50%;
  filter: blur(0px);
  opacity: 0.6;
  pointer-events: none;
}

.dash-shell::before {
  width: 420px;
  height: 420px;
  background: radial-gradient(circle, rgba(11, 44, 111, 0.25), rgba(11, 44, 111, 0));
  top: -120px;
  right: -80px;
}

.dash-shell::after {
  width: 360px;
  height: 360px;
  background: radial-gradient(circle, rgba(244, 197, 66, 0.3), rgba(244, 197, 66, 0));
  bottom: -120px;
  left: -80px;
}

.dash-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.75rem;
  flex-wrap: wrap;
  position: relative;
  z-index: 1;
}

.dash-title-stack {
  position: relative;
  padding-left: 1.6rem;
  max-width: 640px;
}

.dash-title-stack::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0.3rem;
  bottom: 0.3rem;
  width: 4px;
  border-radius: 999px;
  background: linear-gradient(180deg, #0b2c6f, #f4c542, #0b2c6f);
  box-shadow: 0 0 16px rgba(242, 195, 0, 0.6);
}

.dash-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.3rem 0.85rem;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--ink);
  background: rgba(244, 197, 66, 0.18);
  border: 1px solid rgba(244, 197, 66, 0.4);
}

.dash-title {
  margin-top: 0.75rem;
  font-size: 2.8rem;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.05;
  font-family: "Fraunces", "Times New Roman", serif;
}

.dash-title-accent {
  display: block;
  font-size: 1.1rem;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #475569;
  margin-top: 0.6rem;
  font-family: "Plus Jakarta Sans", "Trebuchet MS", sans-serif;
}

.dash-subtitle {
  margin-top: 0.6rem;
  color: var(--muted);
  font-size: 1.05rem;
  max-width: 520px;
}

.dash-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.dash-help {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid var(--border);
  color: var(--ink);
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dash-help svg {
  width: 18px;
  height: 18px;
}

.dash-help:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
}

.dash-profile {
  position: relative;
}

.dash-profile-toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  border-radius: 999px;
  padding: 0.4rem 0.8rem;
  border: 1px solid var(--border);
  background: rgba(255, 255, 255, 0.95);
  font-weight: 600;
  color: var(--ink-deep);
  cursor: pointer;
}

.dash-profile-initial {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--ink);
  color: #ffffff;
  display: grid;
  place-items: center;
  font-size: 0.85rem;
  font-weight: 700;
}

.dash-profile-label {
  font-size: 0.95rem;
}

.dash-profile-caret {
  width: 16px;
  height: 16px;
  color: var(--muted);
}

.dash-profile-menu {
  position: absolute;
  right: 0;
  top: calc(100% + 0.6rem);
  min-width: 180px;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: 0 16px 36px rgba(15, 23, 42, 0.15);
  padding: 0.4rem;
  display: grid;
  gap: 0.25rem;
  z-index: 5;
}

.dash-profile-item {
  border: none;
  background: transparent;
  border-radius: 12px;
  padding: 0.6rem 0.8rem;
  text-align: left;
  font-weight: 600;
  color: var(--ink-deep);
  text-decoration: none;
  cursor: pointer;
}

.dash-profile-item:hover {
  background: rgba(11, 44, 111, 0.08);
}

.dash-grid {
  margin-top: 2.75rem;
  display: grid;
  gap: 1.5rem;
  grid-template-columns: minmax(0, 1fr);
  position: relative;
  z-index: 1;
}

.dash-panel {
  background: var(--surface);
  border-radius: 28px;
  padding: 2.25rem;
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  backdrop-filter: blur(16px);
  animation: dashRise 0.7s ease both;
}

.dash-grid > .dash-panel:nth-child(1) {
  animation-delay: 0.05s;
}

.dash-grid > .dash-panel:nth-child(2) {
  animation-delay: 0.12s;
}

.dash-grid > .dash-panel:nth-child(3) {
  animation-delay: 0.18s;
}

.dash-grid > .dash-panel:nth-child(4) {
  animation-delay: 0.24s;
}

.dash-hero {
  display: grid;
  gap: 2rem;
  justify-items: center;
  text-align: center;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(236, 242, 255, 0.85));
  position: relative;
}

.hero-header {
  display: grid;
  gap: 0.7rem;
  justify-items: center;
}

.status-pill {
  align-self: center;
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.4rem 1.2rem;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.28em;
  background: var(--tone, var(--gold));
  color: var(--tone-ink, #1f2937);
  box-shadow: 0 12px 20px rgba(15, 23, 42, 0.12);
}

.welcome-title {
  margin-top: 0.3rem;
  font-size: 2.1rem;
  color: var(--ink);
  font-weight: 700;
}

.welcome-subtitle {
  margin-top: 0.2rem;
  color: var(--muted);
  font-size: 1.05rem;
  max-width: 520px;
}

.status-alert {
  margin-top: 1.5rem;
  padding: 0.8rem 1.1rem;
  border-radius: 16px;
  background: rgba(248, 113, 113, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.4);
  color: #b91c1c;
  font-weight: 600;
  text-align: center;
  max-width: 520px;
}

.resident-focus {
  width: 100%;
  display: flex;
  justify-content: center;
}

.resident-pass {
  width: min(720px, 100%);
  background: #ffffff;
  border-radius: 28px;
  border: 2px solid rgba(11, 44, 111, 0.18);
  padding: 2.2rem 2.4rem;
  display: grid;
  gap: 1.6rem;
  box-shadow: 0 24px 48px rgba(15, 23, 42, 0.15);
}

.pass-top {
  display: grid;
  gap: 0.4rem;
  justify-items: center;
}

.pass-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.32em;
  color: #64748b;
  font-weight: 700;
}

.pass-id {
  font-size: 2.4rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: 0.06em;
}

.pass-status {
  padding: 0.35rem 1.1rem;
  border-radius: 999px;
  background: var(--tone, var(--gold));
  color: var(--tone-ink, #1f2937);
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.24em;
  font-weight: 700;
}

.pass-qr {
  border-radius: 22px;
  padding: 1.8rem;
  border: 1px solid rgba(148, 163, 184, 0.4);
  background: rgba(240, 244, 255, 0.85);
  display: grid;
  gap: 1rem;
  justify-items: center;
}

.pass-qr.is-locked {
  opacity: 0.8;
}

.pass-qr-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  color: #64748b;
  font-weight: 700;
}

.qr-code {
  font-size: 2rem;
  letter-spacing: 0.24em;
  font-weight: 700;
  color: var(--ink);
}

.qr-locked {
  font-size: 1.2rem;
  font-weight: 700;
  color: #94a3b8;
}

.pass-code {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 1.4rem;
  border: 1px dashed rgba(148, 163, 184, 0.55);
  display: grid;
  gap: 0.6rem;
  justify-items: center;
}

.pass-code.is-locked {
  opacity: 0.7;
}

.pass-code-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.28em;
  color: #64748b;
  font-weight: 700;
}

.pass-code-value {
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: 0.2em;
}

.pass-code-action {
  background: var(--ink);
  color: #ffffff;
  border: none;
  border-radius: 999px;
  padding: 0.5rem 1.4rem;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
}

.copy-status {
  font-size: 0.85rem;
  color: var(--ink);
  font-weight: 600;
}

.pass-note {
  font-size: 1rem;
  color: #64748b;
  max-width: 620px;
  text-align: center;
}

.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.panel-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--ink-deep);
}

.panel-tag {
  padding: 0.35rem 0.9rem;
  border-radius: 999px;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.22em;
  font-weight: 700;
  background: var(--tone, rgba(226, 232, 240, 0.9));
  color: var(--tone-ink, #1f2937);
}

.timeline {
  position: relative;
  margin-top: 1.6rem;
  display: grid;
  gap: 1.25rem;
  padding-left: 0.5rem;
}

.timeline::before {
  content: "";
  position: absolute;
  left: 5px;
  top: 0.2rem;
  bottom: 0.2rem;
  width: 2px;
  background: rgba(148, 163, 184, 0.35);
}

.timeline-step {
  display: grid;
  grid-template-columns: 18px 1fr;
  gap: 0.85rem;
  align-items: flex-start;
}

.timeline-dot {
  margin-top: 0.2rem;
  height: 12px;
  width: 12px;
  border-radius: 999px;
  background: rgba(148, 163, 184, 0.7);
}

.timeline-step.is-done .timeline-dot {
  background: var(--ink);
  box-shadow: 0 0 0 6px rgba(11, 44, 111, 0.18);
}

.timeline-step.is-active .timeline-dot {
  background: var(--tone, var(--gold));
  box-shadow: 0 0 0 6px var(--tone-soft, rgba(242, 195, 0, 0.2));
}

.timeline-step.is-rejected .timeline-dot {
  background: #c0392b;
  box-shadow: 0 0 0 6px rgba(192, 57, 43, 0.18);
}

.timeline-label {
  font-weight: 700;
  color: var(--ink-deep);
  font-size: 1rem;
}

.timeline-desc {
  margin-top: 0.2rem;
  color: #64748b;
  font-size: 0.95rem;
}

.details-grid {
  margin-top: 1.5rem;
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.detail-item {
  padding: 1rem 1.2rem;
  border-radius: 18px;
  background: rgba(241, 245, 249, 0.85);
  border: 1px solid rgba(226, 232, 240, 0.8);
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.detail-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.22em;
  font-weight: 700;
  color: #64748b;
}

.detail-value {
  font-size: 1rem;
  font-weight: 600;
  color: var(--ink-deep);
}

.detail-cta {
  margin-top: 1.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.6rem 1.2rem;
  border-radius: 999px;
  border: 1px solid var(--border);
  color: var(--ink);
  font-weight: 600;
  background: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.detail-cta:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
}

.next-steps {
  margin-top: 1.5rem;
  list-style: none;
  padding: 0;
  display: grid;
  gap: 1rem;
}

.next-item {
  padding: 1rem 1.2rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(226, 232, 240, 0.8);
}

.next-title {
  font-weight: 700;
  color: var(--ink-deep);
  font-size: 1rem;
}

.next-desc {
  margin-top: 0.3rem;
  color: #64748b;
  font-size: 0.95rem;
}

.support-card {
  margin-top: 1.6rem;
  padding: 1.2rem 1.4rem;
  border-radius: 20px;
  background: linear-gradient(120deg, rgba(11, 44, 111, 0.08), rgba(244, 197, 66, 0.18));
  border: 1px solid rgba(148, 163, 184, 0.4);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.support-title {
  font-weight: 700;
  color: var(--ink);
  font-size: 1rem;
}

.support-detail {
  margin-top: 0.25rem;
  color: #64748b;
  font-size: 0.9rem;
}

.support-contact {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  font-weight: 600;
  color: var(--ink-deep);
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
  --tone: #f2c300;
  --tone-ink: #1f2937;
  --tone-soft: rgba(242, 195, 0, 0.22);
}

@media (min-width: 1024px) {
  .dash-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .dash-hero {
    grid-column: 1 / -1;
  }
}

@media (max-width: 900px) {
  .dash-hero {
    grid-template-columns: minmax(0, 1fr);
  }
}

@media (max-width: 640px) {
  .dash-shell {
    padding: 2.75rem 1.25rem 3rem;
  }

  .dash-title {
    font-size: 2.2rem;
  }

  .dash-title-stack {
    padding-left: 1.2rem;
  }

  .dash-panel {
    padding: 1.7rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .dash-panel {
    animation: none;
  }
}

@keyframes dashRise {
  from {
    opacity: 0;
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
