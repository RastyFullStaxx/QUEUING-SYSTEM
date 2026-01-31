<template>
  <div class="landing-shell">
    <section
      class="landing-screen admin-landing-screen"
      role="button"
      tabindex="0"
      @click="openAuth"
      @keydown.enter.prevent="openAuth"
      @keydown.space.prevent="openAuth"
    >
      <div class="landing-sunray admin-sunray" aria-hidden="true"></div>
      <div class="landing-content">
        <img class="landing-logo" src="/logo.png" alt="Barangay San Miguel" />
        <p class="landing-kicker">Admin command center</p>
        <h1 class="landing-title">Barangay San Miguel</h1>
        <div class="landing-tap">
          <span class="landing-line"></span>
          <span class="landing-text">Tap anywhere to begin</span>
          <span class="landing-line"></span>
        </div>
      </div>
      <div class="landing-skyline admin-skyline" aria-hidden="true"></div>
    </section>

    <transition name="auth-fade">
      <div v-if="showAuth" class="auth-overlay">
        <div class="auth-dialog">
          <div class="pop-shell" :class="{ 'pop-shell--static': mode === 'register' }">
            <div class="pop-card" :class="{ 'pop-card--static': mode === 'register' }">
              <button class="auth-close" type="button" aria-label="Close" @click="closeAuth">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M6 6l12 12M18 6l-12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
              </button>

              <div class="auth-layout" :class="{ 'auth-layout--single': mode === 'register' }">
                <aside v-if="mode === 'signin'" class="auth-aside admin-auth-aside">
                  <div class="auth-kicker">Admin portal</div>
                  <h1 class="auth-title">
                    Queue operations
                    <span>command center</span>
                  </h1>
                  <p class="auth-subtitle">
                    Orchestrate kiosks, services, and staff decisions from a single operational view built for speed and trust.
                  </p>
                  <div class="auth-support-note">
                    <p class="auth-support-title">Admin access only</p>
                    <p class="auth-support-subtitle">Use your authorized barangay account to continue.</p>
                  </div>
                </aside>

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
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const mode = ref('signin')
const showAuth = ref(false)
const loginEmail = ref('')
const loginPassword = ref('')
const registerName = ref('')
const registerEmail = ref('')
const registerPassword = ref('')
const registerConfirm = ref('')
const error = ref('')
const isSubmitting = ref(false)

const openAuth = () => {
  mode.value = 'signin'
  error.value = ''
  showAuth.value = true
}

const closeAuth = () => {
  showAuth.value = false
  error.value = ''
}

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
.landing-shell {
  min-height: 100vh;
  width: 100%;
}

.landing-screen {
  position: fixed;
  inset: 0;
  display: grid;
  place-items: center;
  text-align: center;
  cursor: pointer;
  overflow: hidden;
  background: radial-gradient(circle at 15% 10%, rgba(255, 255, 255, 0.9), rgba(226, 244, 255, 0.6) 42%),
    radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.85), rgba(191, 219, 254, 0.35) 45%),
    linear-gradient(165deg, #f3f7ff 0%, #eef3ff 45%, #f7f3ea 100%);
}

.landing-sunray {
  position: absolute;
  inset: -60%;
  background-image: radial-gradient(circle, rgba(255, 255, 255, 0.7), transparent 70%),
    repeating-conic-gradient(rgba(14, 116, 144, 0.22) 0deg 12deg, transparent 12deg 24deg);
  opacity: 0.6;
  filter: blur(0.8px);
  animation: adminRaysPulse 8s ease-in-out infinite, adminRaysRotate 160s linear infinite;
}

.admin-sunray {
  background-image: radial-gradient(circle, rgba(255, 255, 255, 0.7), transparent 70%),
    repeating-conic-gradient(rgba(14, 116, 144, 0.22) 0deg 12deg, transparent 12deg 24deg);
}

.landing-content {
  position: relative;
  z-index: 1;
  display: grid;
  justify-items: center;
  gap: 1rem;
}

.landing-logo {
  width: 150px;
  height: 150px;
  object-fit: contain;
  filter: drop-shadow(0 16px 30px rgba(14, 116, 144, 0.35));
}

.landing-kicker {
  text-transform: uppercase;
  letter-spacing: 0.35em;
  font-size: 0.72rem;
  font-weight: 700;
  color: rgba(15, 23, 42, 0.6);
}

.landing-title {
  font-size: clamp(2.4rem, 4vw, 3.4rem);
  font-weight: 800;
  color: #0b2c6f;
}

.landing-tap {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(15, 23, 42, 0.6);
  animation: adminTapPulse 2.4s ease-in-out infinite;
}

.landing-line {
  width: 100px;
  height: 1px;
  background: rgba(14, 116, 144, 0.3);
}

.landing-skyline {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: min(26vh, 220px);
  background: url("/buildings-admin.png") repeat-x bottom center;
  background-size: contain;
  opacity: 0.9;
  pointer-events: none;
}

.auth-overlay {
  position: fixed;
  inset: 0;
  z-index: 40;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3.5rem 1.5rem;
  background: rgba(7, 14, 28, 0.75);
  backdrop-filter: blur(18px);
  overflow-y: auto;
}

.auth-dialog {
  width: 100%;
  max-width: 72rem;
  position: relative;
}

.auth-close {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  border-radius: 999px;
  background: rgba(11, 44, 111, 0.08);
  color: #0b2c6f;
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
  z-index: 2;
}

.auth-close:hover {
  background: rgba(11, 44, 111, 0.16);
  transform: scale(1.02);
}

.auth-fade-enter-active,
.auth-fade-leave-active {
  transition: opacity 0.35s ease;
}

.auth-fade-enter-from,
.auth-fade-leave-to {
  opacity: 0;
}

.pop-shell {
  position: relative;
  padding: 3px;
  border-radius: 36px;
  background: transparent;
  box-shadow: 0 28px 80px rgba(10, 25, 60, 0.35);
  animation: popIn 0.4s ease-out;
}

.pop-shell::before {
  content: "";
  position: absolute;
  inset: -12px;
  border-radius: 44px;
  background: conic-gradient(
    from 0deg,
    rgba(255, 255, 255, 0.9),
    rgba(255, 255, 255, 0.5),
    rgba(14, 116, 144, 0.7),
    rgba(255, 255, 255, 0.9),
    rgba(59, 130, 246, 0.65),
    rgba(255, 255, 255, 0.9)
  );
  filter: blur(16px);
  opacity: 0.85;
  z-index: 0;
  animation: glowFlow 10s linear infinite;
  transition: opacity 0.35s ease;
  pointer-events: none;
}

.pop-shell::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 36px;
  background: conic-gradient(
    from 0deg,
    rgba(255, 255, 255, 0.95),
    rgba(14, 116, 144, 0.85),
    rgba(255, 255, 255, 0.95),
    rgba(59, 130, 246, 0.85),
    rgba(255, 255, 255, 0.95)
  );
  padding: 3px;
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  z-index: 0;
  animation: glowFlow 12s linear infinite;
  transition: opacity 0.35s ease;
  pointer-events: none;
}

.pop-shell--static::before,
.pop-shell--static::after {
  opacity: 0;
  animation: none;
}

.pop-card--static {
  backdrop-filter: none;
}

.pop-card {
  position: relative;
  z-index: 1;
  border-radius: 34px;
  padding: 2rem 2.75rem;
  background:
    radial-gradient(circle at top center, rgba(255, 255, 255, 0.98), rgba(246, 249, 255, 0.86)),
    linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(242, 245, 252, 0.95));
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.auth-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: 2rem;
  align-items: stretch;
}

.auth-layout--single {
  grid-template-columns: 1fr;
}

.auth-aside {
  border-radius: 24px;
  padding: 2rem;
  background: linear-gradient(150deg, #0b2c6f 0%, #1e3a8a 55%, #0e7490 100%);
  color: #ffffff;
  display: grid;
  gap: 1.2rem;
}

.admin-auth-aside .auth-kicker {
  color: rgba(255, 255, 255, 0.7);
}

.admin-auth-aside .auth-title {
  color: #ffffff;
}

.admin-auth-aside .auth-title span {
  color: #facc15;
}

.admin-auth-aside .auth-subtitle {
  color: rgba(255, 255, 255, 0.75);
}

.auth-panel {
  display: flex;
  align-items: center;
}

.auth-support-note {
  border-radius: 18px;
  padding: 1rem 1.2rem;
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: rgba(255, 255, 255, 0.85);
}

.auth-support-title {
  margin: 0;
  font-weight: 700;
}

.auth-support-subtitle {
  margin: 0.35rem 0 0;
  font-size: 0.95rem;
  color: rgba(255, 255, 255, 0.7);
}

@keyframes adminRaysPulse {
  0%, 100% { opacity: 0.45; transform: scale(1); }
  50% { opacity: 0.75; transform: scale(1.03); }
}

@keyframes adminRaysRotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes adminTapPulse {
  0%, 100% { opacity: 0.85; transform: translateY(0); }
  50% { opacity: 1; transform: translateY(6px); }
}

@keyframes popIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}

@keyframes glowFlow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

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

  .auth-layout {
    grid-template-columns: 1fr;
  }

  .auth-aside {
    order: 2;
  }

  .auth-dialog {
    max-width: 100%;
  }
}

@media (max-width: 640px) {
  .landing-logo {
    width: 110px;
    height: 110px;
  }

  .landing-title {
    font-size: 2.2rem;
  }

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
