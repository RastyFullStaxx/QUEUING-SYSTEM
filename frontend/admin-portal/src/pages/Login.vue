<template>
  <div class="auth-shell">
    <div class="auth-stage">
      <section class="auth-intro">
        <div class="auth-kicker">Admin portal</div>
        <h1 class="auth-title">
          Queue operations
          <span>command center</span>
        </h1>
        <p class="auth-subtitle">
          Orchestrate kiosks, services, and staff decisions from a single operational view built for speed and trust.
        </p>
        <div class="auth-stat-grid">
          <div class="auth-stat-card">
            <span>Service health</span>
            <strong>Live sync</strong>
            <small>Queue and kiosk heartbeat</small>
          </div>
          <div class="auth-stat-card is-gold">
            <span>Access control</span>
            <strong>RBAC ready</strong>
            <small>Role-aware actions</small>
          </div>
          <div class="auth-stat-card is-dark">
            <span>Audit trails</span>
            <strong>Always on</strong>
            <small>Compliance timeline</small>
          </div>
          <div class="auth-stat-card">
            <span>Daily ops</span>
            <strong>Shift aware</strong>
            <small>Balanced workload</small>
          </div>
        </div>
        <div class="auth-feature-card">
          <div class="auth-feature-header">
            <h3>What the admin team sees</h3>
            <span class="auth-chip">Real-time operations</span>
          </div>
          <div class="auth-feature-list">
            <div class="auth-feature-item">
              <span class="auth-feature-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M4 12h16M12 4v16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
              </span>
              <div>
                <strong>Command dashboard</strong>
                <p>Daily queue metrics, peak windows, and completion pace.</p>
              </div>
            </div>
            <div class="auth-feature-item">
              <span class="auth-feature-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M6 12l4 4 8-8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
              </span>
              <div>
                <strong>Service readiness</strong>
                <p>Active counters, service queue depth, and smart call priority.</p>
              </div>
            </div>
            <div class="auth-feature-item">
              <span class="auth-feature-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M5 5h14v14H5z" stroke="currentColor" stroke-width="1.6" />
                  <path d="M9 9h6v6H9z" stroke="currentColor" stroke-width="1.6" />
                </svg>
              </span>
              <div>
                <strong>Audit visibility</strong>
                <p>Every action recorded with actor trace and metadata.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="auth-signal-card">
          <div>
            <p class="auth-signal-title">Operations pulse</p>
            <p class="auth-signal-subtitle">Check-ins, device uptime, and SLA signals stay in sync.</p>
          </div>
          <div class="auth-signal-badges">
            <span>Verified residents</span>
            <span>Queue monitor</span>
            <span>Admin alerts</span>
          </div>
        </div>
      </section>

      <section class="auth-panel">
        <div class="auth-panel-card">
          <div class="auth-brand">
            <div class="auth-brand-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3l9 4.5v11L12 21l-9-4.5v-11L12 3z" />
                <path d="M12 7v5l4 2" />
              </svg>
            </div>
            <div>
              <p class="auth-brand-title">Barangay Service Hub</p>
              <p class="auth-brand-subtitle">Admin access only</p>
            </div>
          </div>

          <div class="auth-tabs" role="tablist">
            <button
              class="auth-tab"
              :class="{ 'is-active': mode === 'signin' }"
              type="button"
              @click="setMode('signin')"
            >
              Sign in
            </button>
            <button
              class="auth-tab"
              :class="{ 'is-active': mode === 'register' }"
              type="button"
              @click="setMode('register')"
            >
              Register
            </button>
          </div>

          <form v-if="mode === 'signin'" class="auth-form" @submit.prevent="submitLogin">
            <label class="auth-field">
              <span>Email address</span>
              <input
                v-model="loginEmail"
                type="email"
                placeholder="name@barangay.gov"
                class="auth-input"
                autocomplete="email"
                required
              />
            </label>
            <label class="auth-field">
              <span>Password</span>
              <input
                v-model="loginPassword"
                type="password"
                placeholder="Enter your password"
                class="auth-input"
                autocomplete="current-password"
                required
              />
            </label>
            <div class="auth-actions">
              <button class="auth-button" type="submit" :disabled="isSubmitting">Sign in</button>
              <button class="auth-ghost" type="button" @click="setMode('register')">Need access?</button>
            </div>
          </form>

          <form v-else class="auth-form" @submit.prevent="submitRegister">
            <label class="auth-field">
              <span>Full name</span>
              <input
                v-model="registerName"
                type="text"
                placeholder="First and last name"
                class="auth-input"
                autocomplete="name"
                required
              />
            </label>
            <label class="auth-field">
              <span>Email address</span>
              <input
                v-model="registerEmail"
                type="email"
                placeholder="you@barangay.gov"
                class="auth-input"
                autocomplete="email"
                required
              />
            </label>
            <label class="auth-field">
              <span>Password</span>
              <input
                v-model="registerPassword"
                type="password"
                placeholder="Minimum 8 characters"
                class="auth-input"
                autocomplete="new-password"
                required
              />
            </label>
            <label class="auth-field">
              <span>Confirm password</span>
              <input
                v-model="registerConfirm"
                type="password"
                placeholder="Re-enter password"
                class="auth-input"
                autocomplete="new-password"
                required
              />
            </label>
            <div class="auth-note">
              Registration is only open for the first super admin. If your system is already set up, ask a super admin
              to create your account.
            </div>
            <div class="auth-actions">
              <button class="auth-button" type="submit" :disabled="isSubmitting">Create admin</button>
              <button class="auth-ghost" type="button" @click="setMode('signin')">Back to sign in</button>
            </div>
          </form>

          <p v-if="error" class="auth-alert" role="alert" aria-live="polite">{{ error }}</p>
          <p class="auth-footnote">Need help? Contact your IT coordinator for access.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const mode = ref('signin')
const loginEmail = ref('')
const loginPassword = ref('')
const registerName = ref('')
const registerEmail = ref('')
const registerPassword = ref('')
const registerConfirm = ref('')
const error = ref('')
const isSubmitting = ref(false)

const setMode = (nextMode) => {
  if (mode.value !== nextMode) {
    mode.value = nextMode
    error.value = ''
  }
}

const submitLogin = async () => {
  if (isSubmitting.value) return
  error.value = ''
  isSubmitting.value = true
  try {
    const data = await request('/api/auth/admin/login', {
      method: 'POST',
      body: JSON.stringify({ email: loginEmail.value, password: loginPassword.value }),
    })
    localStorage.setItem('admin_token', data.token)
    localStorage.setItem('admin_profile', JSON.stringify(data.admin))
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message
  } finally {
    isSubmitting.value = false
  }
}

const submitRegister = async () => {
  if (isSubmitting.value) return
  error.value = ''
  if (!registerName.value.trim()) {
    error.value = 'Name is required'
    return
  }
  if (!registerEmail.value.trim()) {
    error.value = 'Email is required'
    return
  }
  if (registerPassword.value.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return
  }
  if (registerPassword.value !== registerConfirm.value) {
    error.value = 'Passwords do not match'
    return
  }
  isSubmitting.value = true
  try {
    const data = await request('/api/auth/admin/register', {
      method: 'POST',
      body: JSON.stringify({
        name: registerName.value.trim(),
        email: registerEmail.value.trim(),
        password: registerPassword.value,
      }),
    })
    localStorage.setItem('admin_token', data.token)
    localStorage.setItem('admin_profile', JSON.stringify(data.admin))
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.auth-shell {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3.5rem 1.5rem;
  background: radial-gradient(circle at top left, rgba(242, 195, 0, 0.2), transparent 45%),
    radial-gradient(circle at 85% 15%, rgba(11, 44, 111, 0.2), transparent 55%),
    linear-gradient(135deg, #f7f9ff 0%, #ffffff 50%, #fff8e4 100%);
  position: relative;
  overflow: hidden;
}

.auth-shell::before,
.auth-shell::after {
  content: '';
  position: absolute;
  border-radius: 999px;
  opacity: 0.35;
  filter: blur(0.5px);
}

.auth-shell::before {
  width: 320px;
  height: 320px;
  left: -120px;
  top: 120px;
  background: rgba(11, 44, 111, 0.2);
}

.auth-shell::after {
  width: 260px;
  height: 260px;
  right: -80px;
  bottom: 80px;
  background: rgba(242, 195, 0, 0.25);
}

.auth-stage {
  width: min(1100px, 100%);
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 2.5rem;
  position: relative;
  z-index: 1;
  align-items: stretch;
}

.auth-intro {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  color: #0b2c6f;
  animation: auth-rise 0.7s ease;
}

.auth-kicker {
  text-transform: uppercase;
  letter-spacing: 0.3em;
  font-size: 0.8rem;
  color: rgba(11, 44, 111, 0.6);
}

.auth-title {
  font-size: clamp(2.2rem, 3.2vw, 3.1rem);
  font-weight: 700;
  line-height: 1.1;
}

.auth-title span {
  display: block;
  color: #f2c300;
}

.auth-subtitle {
  font-size: 1.05rem;
  color: rgba(11, 44, 111, 0.75);
}

.auth-stat-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.auth-stat-card {
  background: #ffffff;
  border-radius: 18px;
  padding: 1rem 1.1rem;
  border: 1px solid rgba(11, 44, 111, 0.1);
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  color: #0b2c6f;
}

.auth-stat-card strong {
  font-size: 1.15rem;
  font-weight: 700;
}

.auth-stat-card small {
  color: rgba(11, 44, 111, 0.6);
}

.auth-stat-card.is-gold {
  background: rgba(242, 195, 0, 0.18);
  border-color: rgba(242, 195, 0, 0.4);
}

.auth-stat-card.is-dark {
  background: #0b2c6f;
  color: #ffffff;
}

.auth-stat-card.is-dark small {
  color: rgba(255, 255, 255, 0.7);
}

.auth-feature-card {
  border-radius: 22px;
  background: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.12);
  padding: 1.25rem;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
  display: grid;
  gap: 1rem;
}

.auth-feature-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.auth-feature-header h3 {
  font-size: 1.1rem;
  font-weight: 700;
}

.auth-chip {
  background: #f3f4f6;
  border-radius: 999px;
  padding: 0.25rem 0.8rem;
  font-size: 0.8rem;
  color: #0b2c6f;
  border: 1px solid rgba(11, 44, 111, 0.12);
}

.auth-feature-list {
  display: grid;
  gap: 1rem;
}

.auth-feature-item {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.9rem;
  align-items: start;
  color: #0f172a;
}

.auth-feature-item p {
  color: #64748b;
  margin-top: 0.2rem;
}

.auth-feature-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: rgba(11, 44, 111, 0.08);
  color: #0b2c6f;
}

.auth-signal-card {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.1rem 1.25rem;
  border-radius: 20px;
  background: rgba(11, 44, 111, 0.05);
  border: 1px dashed rgba(11, 44, 111, 0.2);
}

.auth-signal-title {
  font-weight: 700;
}

.auth-signal-subtitle {
  color: #64748b;
  font-size: 0.95rem;
}

.auth-signal-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  font-size: 0.85rem;
  font-weight: 600;
}

.auth-signal-badges span {
  padding: 0.3rem 0.8rem;
  border-radius: 999px;
  background: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.15);
  color: #0b2c6f;
}

.auth-panel {
  display: flex;
  align-items: center;
  animation: auth-rise 0.7s ease 0.1s both;
}

.auth-panel-card {
  width: 100%;
  background: #ffffff;
  border-radius: 26px;
  padding: 2rem;
  border: 1px solid rgba(11, 44, 111, 0.15);
  box-shadow: 0 24px 40px rgba(15, 23, 42, 0.12);
  display: grid;
  gap: 1.5rem;
}

.auth-brand {
  display: flex;
  align-items: center;
  gap: 0.9rem;
}

.auth-brand-icon {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: #0b2c6f;
  color: #ffffff;
  display: grid;
  place-items: center;
}

.auth-brand-icon svg {
  width: 28px;
  height: 28px;
}

.auth-brand-title {
  font-weight: 700;
  color: #0b2c6f;
}

.auth-brand-subtitle {
  color: #6b7280;
  font-size: 0.95rem;
}

.auth-tabs {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  background: #f8fafc;
  overflow: hidden;
}

.auth-tab {
  padding: 0.8rem 1rem;
  font-weight: 600;
  color: #6b7280;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: 0.2s ease;
}

.auth-tab.is-active {
  background: #0b2c6f;
  color: #ffffff;
}

.auth-form {
  display: grid;
  gap: 1rem;
}

.auth-field {
  display: grid;
  gap: 0.4rem;
  font-weight: 600;
  color: #1f2937;
}

.auth-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.75rem 0.9rem;
  font-size: 1rem;
  background: #f8fafc;
}

.auth-input:focus {
  outline: 2px solid rgba(11, 44, 111, 0.3);
  border-color: #0b2c6f;
}

.auth-note {
  font-size: 0.9rem;
  color: #6b7280;
  background: rgba(242, 195, 0, 0.16);
  padding: 0.75rem 0.9rem;
  border-radius: 12px;
  border: 1px solid rgba(242, 195, 0, 0.4);
}

.auth-actions {
  display: grid;
  gap: 0.7rem;
}

.auth-button {
  background: #f2c300;
  color: #111827;
  font-weight: 700;
  padding: 0.85rem 1rem;
  border-radius: 14px;
  border: none;
  cursor: pointer;
}

.auth-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.auth-ghost {
  background: transparent;
  border: 1px solid rgba(11, 44, 111, 0.2);
  color: #0b2c6f;
  font-weight: 600;
  padding: 0.7rem 1rem;
  border-radius: 14px;
  cursor: pointer;
}

.auth-alert {
  background: rgba(192, 57, 43, 0.1);
  border: 1px solid rgba(192, 57, 43, 0.35);
  color: #c0392b;
  border-radius: 12px;
  padding: 0.7rem 0.9rem;
  font-weight: 600;
}

.auth-footnote {
  font-size: 0.9rem;
  color: #6b7280;
  text-align: center;
}

@media (max-width: 960px) {
  .auth-stage {
    grid-template-columns: 1fr;
  }

  .auth-panel {
    order: -1;
  }
}

@media (max-width: 640px) {
  .auth-stat-grid {
    grid-template-columns: 1fr;
  }

  .auth-feature-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .auth-panel-card {
    padding: 1.5rem;
  }
}

@keyframes auth-rise {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (prefers-reduced-motion: reduce) {
  .auth-intro,
  .auth-panel {
    animation: none;
  }
}
</style>
