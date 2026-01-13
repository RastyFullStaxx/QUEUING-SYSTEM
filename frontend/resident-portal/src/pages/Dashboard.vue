<template>
  <div class="dash-shell">
    <header class="dash-top">
      <div class="dash-title-stack">
        <span class="dash-badge">Resident Portal</span>
        <h1 class="dash-title">
          Barangay San Miguel
          <span class="dash-title-accent">Registration Status</span>
        </h1>
        <p class="dash-subtitle">Simple, clear updates about your registration.</p>
      </div>
      <router-link class="dash-link" to="/profile">
        View profile
      </router-link>
    </header>

    <main class="dash-main">
      <section class="dash-card">
        <div class="status-pill" :class="statusClass">{{ statusCopy }}</div>
        <h2 class="welcome-title">Hello, {{ residentName }}</h2>
        <p class="welcome-subtitle">We check your documents and update this screen automatically.</p>

        <div class="status-row">
          <div class="status-icon" :class="statusClass"></div>
          <div>
            <p class="status-label">Current status</p>
            <p class="status-value">{{ statusCopy }}</p>
          </div>
        </div>

        <div v-if="status === 'approved'" class="qr-card">
          <p class="qr-label">Your resident code</p>
          <div class="qr-code">RESIDENT #{{ resident?.id || '---' }}</div>
        </div>
        <div v-else class="pending-note">
          Once approved, your resident code will appear here for quick scanning.
        </div>
      </section>

      <aside class="dash-side">
        <button class="dash-quiet" type="button" @click="onLogout">Log out</button>
      </aside>
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

const residentName = computed(() => {
  if (!resident.value) return 'Resident'
  return `${resident.value.first_name} ${resident.value.last_name}`.trim()
})

const statusClass = computed(() => {
  if (status.value === 'approved') return 'bg-[#2E7D32] text-white'
  if (status.value === 'rejected') return 'bg-[#C0392B] text-white'
  return 'bg-[#F2C300] text-black'
})

const statusCopy = computed(() => {
  if (status.value === 'approved') return 'Approved'
  if (status.value === 'rejected') return 'Not approved'
  return 'Pending review'
})

const onLogout = () => {
  localStorage.removeItem('resident_token')
  localStorage.removeItem('resident_profile')
  router.push('/')
}

onMounted(async () => {
  const cached = localStorage.getItem('resident_profile')
  if (cached) {
    resident.value = JSON.parse(cached)
    status.value = resident.value.status
  }

  const token = localStorage.getItem('resident_token')
  if (token) {
    try {
      const data = await request('/api/resident/me', {
        headers: { Authorization: `Bearer ${token}` },
      })
      resident.value = data.resident
      status.value = data.resident.status
      localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    } catch (err) {
      // Ignore refresh errors for now.
    }
  }
})
</script>

<style scoped>
.dash-shell {
  min-height: 100vh;
  padding: 3rem 2.5rem 4rem;
  background:
    radial-gradient(circle at top left, rgba(255, 255, 255, 0.9), rgba(238, 243, 255, 0.92)),
    radial-gradient(circle at 90% 10%, rgba(255, 255, 255, 0.8), rgba(245, 248, 255, 0.9)),
    linear-gradient(180deg, #f5f7fb, #eef2f8);
}

.dash-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.dash-title-stack {
  position: relative;
  padding-left: 1.5rem;
}

.dash-title-stack::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0.35rem;
  bottom: 0.35rem;
  width: 4px;
  border-radius: 999px;
  background: linear-gradient(180deg, #0b2c6f, #f2c300, #0b2c6f);
  box-shadow: 0 0 16px rgba(242, 195, 0, 0.6);
}

.dash-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #0b2c6f;
  background: rgba(242, 195, 0, 0.18);
  border: 1px solid rgba(242, 195, 0, 0.4);
}

.dash-title {
  margin-top: 0.75rem;
  font-size: 2.4rem;
  font-weight: 700;
  color: #0b2c6f;
  line-height: 1.1;
}

.dash-title-accent {
  display: block;
  font-size: 1.2rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: #475569;
  margin-top: 0.5rem;
}

.dash-subtitle {
  margin-top: 0.5rem;
  color: #6b7280;
  font-size: 1rem;
}

.dash-link {
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.5);
  padding: 0.5rem 1.25rem;
  background: rgba(255, 255, 255, 0.85);
  font-weight: 600;
  color: #0b2c6f;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dash-link:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
}

.dash-main {
  margin-top: 2.75rem;
  display: grid;
  gap: 1.5rem;
  grid-template-columns: minmax(0, 1fr);
}

@media (min-width: 1024px) {
  .dash-main {
    grid-template-columns: minmax(0, 1.3fr) minmax(0, 0.7fr);
  }
}

.dash-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 32px;
  padding: 2.5rem;
  border: 1px solid rgba(226, 232, 240, 0.7);
  box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  padding: 0.35rem 1.1rem;
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.18em;
}

.welcome-title {
  margin-top: 1.5rem;
  font-size: 2rem;
  color: #0b2c6f;
  font-weight: 700;
}

.welcome-subtitle {
  margin-top: 0.5rem;
  color: #6b7280;
  font-size: 1rem;
}

.status-row {
  margin-top: 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(241, 245, 249, 0.8);
  border-radius: 20px;
  padding: 1.25rem 1.5rem;
}

.status-icon {
  height: 14px;
  width: 14px;
  border-radius: 999px;
  box-shadow: 0 0 0 6px rgba(148, 163, 184, 0.15);
}

.status-label {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #64748b;
  font-weight: 600;
}

.status-value {
  margin-top: 0.25rem;
  font-size: 1.4rem;
  font-weight: 700;
  color: #0f172a;
}

.qr-card {
  margin-top: 2rem;
  border-radius: 24px;
  padding: 1.75rem;
  background: rgba(240, 244, 255, 0.85);
  border: 1px dashed rgba(100, 116, 139, 0.4);
  text-align: center;
}

.qr-label {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  color: #64748b;
  font-weight: 600;
}

.qr-code {
  margin-top: 1rem;
  font-size: 1.75rem;
  letter-spacing: 0.25em;
  font-weight: 700;
  color: #0b2c6f;
}

.pending-note {
  margin-top: 1.75rem;
  color: #6b7280;
  font-size: 1rem;
}

.dash-side {
  display: flex;
  align-items: flex-start;
  justify-content: flex-end;
}

.dash-quiet {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  cursor: pointer;
  transition: color 0.2s ease;
}

.dash-quiet:hover {
  color: #0b2c6f;
}

@media (max-width: 640px) {
  .dash-shell {
    padding: 2.5rem 1.25rem 3rem;
  }

  .dash-title {
    font-size: 2.1rem;
  }

  .dash-title-stack {
    padding-left: 1.2rem;
  }

  .dash-card {
    padding: 2rem 1.75rem;
  }
}
</style>
