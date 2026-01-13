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
        <div class="dash-sync">
          <p class="dash-sync-label">Last update</p>
          <p class="dash-sync-value">{{ lastSyncedLabel }}</p>
        </div>
        <button class="dash-action is-primary" type="button" @click="refreshStatus" :disabled="isRefreshing">
          {{ isRefreshing ? 'Refreshing...' : 'Refresh status' }}
        </button>
        <router-link class="dash-action is-ghost" to="/profile">
          Edit profile
        </router-link>
        <button class="dash-action is-text" type="button" @click="onLogout">
          Log out
        </button>
      </div>
    </header>

    <main class="dash-grid">
      <section class="dash-panel dash-hero" :class="statusTone">
        <div class="hero-main">
          <div class="status-pill">{{ statusCopy }}</div>
          <h2 class="welcome-title">Hello, {{ residentName }}</h2>
          <p class="welcome-subtitle">{{ statusMessage }}</p>

          <div class="status-row">
            <div class="status-icon"></div>
            <div>
              <p class="status-label">Current status</p>
              <p class="status-value">{{ statusCopy }}</p>
              <p class="status-meta">{{ statusDetail }}</p>
            </div>
          </div>

          <div v-if="errorMessage" class="status-alert">
            {{ errorMessage }}
          </div>
        </div>

        <div class="hero-side">
          <div class="resident-card">
            <div class="resident-card-top">
              <div>
                <p class="resident-label">Resident ID</p>
                <p class="resident-id">#{{ resident?.id || '---' }}</p>
              </div>
              <span class="resident-status">{{ statusCopy }}</span>
            </div>
            <div class="resident-code" :class="{ 'is-locked': status !== 'approved' }">
              <p class="resident-code-label">Resident code</p>
              <p class="resident-code-value">{{ residentCode }}</p>
              <button v-if="status === 'approved'" class="resident-code-action" type="button" @click="copyCode">
                Copy code
              </button>
              <span v-if="copyStatus" class="copy-status">{{ copyStatus }}</span>
            </div>
          </div>

          <div class="qr-card">
            <p class="qr-label">{{ qrLabel }}</p>
            <div v-if="status === 'approved'" class="qr-code">RESIDENT #{{ resident?.id || '---' }}</div>
            <p class="qr-note">{{ qrNote }}</p>
          </div>
        </div>
      </section>

      <section class="dash-panel">
        <div class="panel-head">
          <h3 class="panel-title">Verification timeline</h3>
          <span class="panel-tag" :class="statusTone">{{ statusCopy }}</span>
        </div>
        <div class="timeline">
          <div v-for="step in timelineSteps" :key="step.label" class="timeline-step" :class="`is-${step.state}`">
            <div class="timeline-dot"></div>
            <div class="timeline-body">
              <p class="timeline-label">{{ step.label }}</p>
              <p class="timeline-desc">{{ step.detail }}</p>
            </div>
          </div>
        </div>
      </section>

      <section class="dash-panel">
        <div class="panel-head">
          <h3 class="panel-title">Resident details</h3>
          <span class="panel-tag">Profile</span>
        </div>
        <div class="details-grid">
          <div class="detail-item">
            <span class="detail-label">Full name</span>
            <span class="detail-value">{{ residentName }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ residentEmail }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Registered</span>
            <span class="detail-value">{{ registeredDate }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">{{ statusCopy }}</span>
          </div>
        </div>
        <router-link class="detail-cta" to="/profile">
          Review profile details
        </router-link>
      </section>

      <section class="dash-panel">
        <div class="panel-head">
          <h3 class="panel-title">Next steps and help</h3>
          <span class="panel-tag">Support</span>
        </div>
        <ul class="next-steps">
          <li v-for="item in nextSteps" :key="item.title" class="next-item">
            <p class="next-title">{{ item.title }}</p>
            <p class="next-desc">{{ item.detail }}</p>
          </li>
        </ul>
        <div class="support-card">
          <div>
            <p class="support-title">Barangay help desk</p>
            <p class="support-detail">Mon to Fri, 8:00 AM to 5:00 PM</p>
          </div>
          <div class="support-contact">
            <span>helpdesk@barangay.local</span>
            <span>+63 912 345 6789</span>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const resident = ref(null)
const status = ref('pending')
const isRefreshing = ref(false)
const lastSynced = ref(null)
const errorMessage = ref('')
const copyStatus = ref('')

const residentName = computed(() => {
  if (!resident.value) return 'Resident'
  return `${resident.value.first_name} ${resident.value.last_name}`.trim() || 'Resident'
})

const residentEmail = computed(() => resident.value?.email || 'Not provided')

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

const qrLabel = computed(() => (status.value === 'approved' ? 'Quick scan code' : 'Queue access'))

const qrNote = computed(() => {
  return status.value === 'approved'
    ? 'Present this code at the kiosk to start a queue.'
    : 'Your queue code will appear once your registration is approved.'
})

const registeredDate = computed(() => formatDate(resident.value?.created_at))

const lastSyncedLabel = computed(() => {
  if (!lastSynced.value) return 'Sync pending'
  return formatDateTime(lastSynced.value)
})

const timelineSteps = computed(() => {
  const isApproved = status.value === 'approved'
  const isRejected = status.value === 'rejected'
  return [
    {
      label: 'Submission received',
      detail: 'Your registration was received by the barangay office.',
      state: 'done',
    },
    {
      label: 'Review in progress',
      detail: 'We verify your information and documents.',
      state: isApproved || isRejected ? 'done' : 'active',
    },
    {
      label: isRejected ? 'Not approved' : 'Decision issued',
      detail: isRejected
        ? 'Please update your profile or contact the help desk.'
        : 'Your approval status is posted here.',
      state: isApproved ? 'done' : isRejected ? 'rejected' : 'upcoming',
    },
    {
      label: 'Resident code ready',
      detail: 'Your code is available for queue access.',
      state: isApproved ? 'done' : 'upcoming',
    },
  ]
})

const nextSteps = computed(() => {
  if (status.value === 'approved') {
    return [
      {
        title: 'Bring your resident code',
        detail: 'Present your code at the kiosk to start a queue.',
      },
      {
        title: 'Keep your profile updated',
        detail: 'Check that your contact details are accurate.',
      },
      {
        title: 'Prepare your documents',
        detail: 'Bring a valid ID when visiting the barangay office.',
      },
    ]
  }
  if (status.value === 'rejected') {
    return [
      {
        title: 'Review your profile',
        detail: 'Confirm your name and contact details are correct.',
      },
      {
        title: 'Prepare a valid ID',
        detail: 'Bring a valid ID to complete verification.',
      },
      {
        title: 'Contact the help desk',
        detail: 'We can guide you through the next steps.',
      },
    ]
  }
  return [
    {
      title: 'Check your email',
      detail: 'We send updates when your review status changes.',
    },
    {
      title: 'Prepare your documents',
      detail: 'Keep a valid ID ready for verification.',
    },
    {
      title: 'Need to update details?',
      detail: 'You can edit your profile while we review.',
    },
  ]
})

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

  isRefreshing.value = true
  try {
    const data = await request('/api/resident/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
    resident.value = data.resident
    status.value = data.resident?.status || 'pending'
    localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    lastSynced.value = new Date()
  } catch (err) {
    errorMessage.value = err?.message || 'Unable to refresh your status.'
  } finally {
    isRefreshing.value = false
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

const formatDate = (value) => {
  if (!value) return '---'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return '---'
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  }).format(date)
}

const formatDateTime = (value) => {
  if (!value) return '---'
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(value)
}

onMounted(() => {
  hydrateFromCache()
  refreshStatus()
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

.dash-sync {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  padding: 0.65rem 1rem;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid var(--border);
  min-width: 150px;
}

.dash-sync-label {
  font-size: 0.65rem;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  font-weight: 700;
  color: #64748b;
}

.dash-sync-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--ink-deep);
}

.dash-action {
  border-radius: 999px;
  padding: 0.55rem 1.3rem;
  font-size: 0.95rem;
  font-weight: 600;
  border: 1px solid transparent;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  cursor: pointer;
}

.dash-action.is-primary {
  background: var(--ink);
  color: #ffffff;
  box-shadow: 0 16px 30px rgba(11, 44, 111, 0.25);
}

.dash-action.is-primary:disabled {
  opacity: 0.7;
  cursor: default;
  box-shadow: none;
}

.dash-action.is-ghost {
  border-color: var(--border);
  background: rgba(255, 255, 255, 0.9);
  color: var(--ink);
}

.dash-action.is-text {
  background: transparent;
  color: #64748b;
  border-color: transparent;
}

.dash-action.is-text:hover {
  transform: none;
  box-shadow: none;
  color: var(--ink);
}

.dash-action:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
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
  grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(236, 242, 255, 0.85));
  position: relative;
}

.hero-main {
  display: flex;
  flex-direction: column;
}

.hero-side {
  display: grid;
  gap: 1.25rem;
}

.status-pill {
  align-self: flex-start;
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
  margin-top: 1.5rem;
  font-size: 2.1rem;
  color: var(--ink);
  font-weight: 700;
}

.welcome-subtitle {
  margin-top: 0.5rem;
  color: var(--muted);
  font-size: 1.05rem;
  max-width: 460px;
}

.status-row {
  margin-top: 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(241, 245, 249, 0.85);
  border-radius: 20px;
  padding: 1.2rem 1.4rem;
  border: 1px solid rgba(226, 232, 240, 0.8);
}

.status-icon {
  height: 14px;
  width: 14px;
  border-radius: 999px;
  background: var(--tone, var(--gold));
  box-shadow: 0 0 0 6px var(--tone-soft, rgba(242, 195, 0, 0.2));
}

.status-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  color: #64748b;
  font-weight: 600;
}

.status-value {
  margin-top: 0.25rem;
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--ink-deep);
}

.status-meta {
  margin-top: 0.3rem;
  font-size: 0.95rem;
  color: #64748b;
}

.status-alert {
  margin-top: 1.5rem;
  padding: 0.8rem 1.1rem;
  border-radius: 16px;
  background: rgba(248, 113, 113, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.4);
  color: #b91c1c;
  font-weight: 600;
}

.resident-card {
  border-radius: 22px;
  background: linear-gradient(160deg, rgba(255, 255, 255, 0.9), rgba(233, 240, 255, 0.92));
  border: 1px solid rgba(191, 219, 254, 0.8);
  padding: 1.5rem;
  display: grid;
  gap: 1.1rem;
}

.resident-card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.resident-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.22em;
  color: #64748b;
  font-weight: 600;
}

.resident-id {
  margin-top: 0.4rem;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--ink);
}

.resident-status {
  align-self: flex-start;
  padding: 0.3rem 0.9rem;
  border-radius: 999px;
  background: var(--tone, var(--gold));
  color: var(--tone-ink, #1f2937);
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-weight: 700;
}

.resident-code {
  background: rgba(255, 255, 255, 0.85);
  border-radius: 18px;
  padding: 1rem 1.1rem;
  border: 1px dashed rgba(148, 163, 184, 0.5);
}

.resident-code.is-locked {
  opacity: 0.7;
}

.resident-code-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.24em;
  color: #64748b;
  font-weight: 600;
}

.resident-code-value {
  margin-top: 0.6rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: 0.12em;
}

.resident-code-action {
  margin-top: 0.8rem;
  background: var(--ink);
  color: #ffffff;
  border: none;
  border-radius: 999px;
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
}

.copy-status {
  display: block;
  margin-top: 0.6rem;
  font-size: 0.8rem;
  color: var(--ink);
  font-weight: 600;
}

.qr-card {
  border-radius: 22px;
  padding: 1.5rem;
  background: rgba(240, 244, 255, 0.85);
  border: 1px solid rgba(100, 116, 139, 0.3);
  text-align: center;
}

.qr-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.28em;
  color: #64748b;
  font-weight: 600;
}

.qr-code {
  margin-top: 1rem;
  font-size: 1.6rem;
  letter-spacing: 0.25em;
  font-weight: 700;
  color: var(--ink);
}

.qr-note {
  margin-top: 0.8rem;
  font-size: 0.95rem;
  color: #64748b;
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

  .details-grid {
    grid-template-columns: minmax(0, 1fr);
  }
}

@media (prefers-reduced-motion: reduce) {
  .dash-panel {
    animation: none;
  }

  .dash-action {
    transition: none;
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
