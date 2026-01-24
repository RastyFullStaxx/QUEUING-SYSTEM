<template>
  <div class="min-h-screen relative bg-[#F7F8FB]">
    <div
      class="landing-screen"
      role="button"
      tabindex="0"
      @click="openAuth"
      @keydown.enter.prevent="openAuth"
      @keydown.space.prevent="openAuth"
    >
      <div class="landing-sunray" aria-hidden="true"></div>
      <div class="landing-content">
        <img class="landing-logo" src="/logo.png" alt="Barangay San Miguel" />
        <p class="landing-kicker">Queue Registration Kiosk</p>
        <h1 class="landing-title">Barangay San Miguel</h1>
        <div class="landing-tap">
          <span class="landing-line"></span>
          <span class="landing-text">Tap anywhere to begin</span>
          <span class="landing-line"></span>
        </div>
      </div>
      <div class="landing-skyline" aria-hidden="true"></div>
    </div>

    <transition name="auth-fade">
      <div v-if="showAuth" class="auth-overlay">
        <div class="auth-dialog w-full max-w-2xl">
          <div class="pop-shell">
            <div class="pop-card">
              <button class="auth-close" type="button" aria-label="Close" @click="closeAuth">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M6 6l12 12M18 6l-12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
              </button>
              <div class="pop-top">
                <div class="pop-brand">
                  <p class="pop-eyebrow">Barangay San Miguel</p>
                  <h2 class="pop-title">{{ mode === 'login' ? 'Sign in' : 'Create account' }}</h2>
                  <p class="pop-subtitle">
                    {{ mode === 'login' ? 'Use your registered email.' : 'Register for resident access.' }}
                  </p>
                </div>
              </div>
              <transition name="form-swap" mode="out-in">
                <form v-if="mode === 'login'" key="login-form" class="space-y-5" @submit.prevent="onSubmit">
                  <div>
                    <label class="text-base font-semibold text-[#0B2C6F]">Email</label>
                    <input
                      v-model="email"
                      type="email"
                      placeholder="you@example.com"
                      autocomplete="email"
                      class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                      required
                    />
                  </div>
                  <div>
                    <label class="text-base font-semibold text-[#0B2C6F]">Password</label>
                    <input
                      v-model="password"
                      type="password"
                      placeholder="Enter your password"
                      autocomplete="current-password"
                      class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                      required
                    />
                  </div>
                <button
                  class="w-full pop-action bg-[#F2C300] text-black py-4 rounded-2xl text-xl font-semibold disabled:opacity-70 disabled:cursor-not-allowed"
                  :disabled="isLoading"
                >
                  {{ isLoading ? 'Signing in...' : 'Login' }}
                </button>
                <div class="flex items-center justify-between text-base">
                  <span class="text-[#6B7280]">Need an account?</span>
                  <button class="text-[#0B2C6F] font-semibold pop-link" type="button" @click="mode = 'register'">
                    Create account
                  </button>
                </div>
                </form>
            <form v-else key="register-form" class="space-y-4" @submit.prevent="onRegister">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="text-base font-semibold text-[#0B2C6F]">First name</label>
                      <input
                        v-model="firstName"
                        type="text"
                        placeholder="Juan"
                        autocomplete="given-name"
                        class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                        required
                      />
                    </div>
                    <div>
                      <label class="text-base font-semibold text-[#0B2C6F]">Last name</label>
                      <input
                        v-model="lastName"
                        type="text"
                        placeholder="Dela Cruz"
                        autocomplete="family-name"
                        class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                        required
                      />
                    </div>
                  </div>
                  <div>
                    <label class="text-base font-semibold text-[#0B2C6F]">Email</label>
                    <input
                      v-model="regEmail"
                      type="email"
                      placeholder="you@example.com"
                      autocomplete="email"
                      class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                      required
                    />
                  </div>
              <div>
                <label class="text-base font-semibold text-[#0B2C6F]">Password</label>
                <input
                  v-model="regPassword"
                  type="password"
                  placeholder="Create a password"
                  autocomplete="new-password"
                  class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                  required
                />
              </div>
              <div>
                <label class="text-base font-semibold text-[#0B2C6F]">Valid ID upload</label>
                <input
                  type="file"
                  accept="image/*,.pdf"
                  class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg file:mr-4 file:rounded-full file:border-0 file:bg-[#0B2C6F] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
                  @change="onValidIdChange"
                  required
                />
                <p class="mt-2 text-sm text-[#6B7280]">
                  Upload a government-issued ID. Admins will verify this before activating your account.
                </p>
              </div>
            <button
              class="w-full pop-action bg-[#F2C300] text-black py-4 rounded-2xl text-xl font-semibold disabled:opacity-70 disabled:cursor-not-allowed"
              :disabled="isRegistering"
            >
                  {{ isRegistering ? 'Creating...' : 'Create Account' }}
                </button>
                <div class="text-base text-[#6B7280]">
                  Already have an account?
                  <button class="text-[#0B2C6F] font-semibold pop-link" type="button" @click="mode = 'login'">Sign in</button>
                </div>
              </form>
              </transition>
              <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
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
const email = ref('')
const password = ref('')
const error = ref('')
const isLoading = ref(false)
const mode = ref('login')
const showAuth = ref(false)
const firstName = ref('')
const lastName = ref('')
const regEmail = ref('')
const regPassword = ref('')
const isRegistering = ref(false)
const validIdFile = ref(null)

const openAuth = () => {
  showAuth.value = true
}

const closeAuth = () => {
  showAuth.value = false
}

const onSubmit = async () => {
  error.value = ''
  isLoading.value = true
  try {
    const data = await request('/api/auth/resident/login', {
      method: 'POST',
      body: JSON.stringify({ email: email.value, password: password.value }),
    })
    localStorage.setItem('resident_token', data.token)
    localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message
  } finally {
    isLoading.value = false
  }
}

const onRegister = async () => {
  error.value = ''
  isRegistering.value = true
  if (!validIdFile.value) {
    error.value = 'Valid ID upload is required.'
    isRegistering.value = false
    return
  }
  try {
    const formData = new FormData()
    formData.append('first_name', firstName.value)
    formData.append('last_name', lastName.value)
    formData.append('email', regEmail.value)
    formData.append('password', regPassword.value)
    formData.append('valid_id', validIdFile.value)
    const data = await request('/api/auth/resident/register', {
      method: 'POST',
      body: formData,
    })
    localStorage.setItem('resident_token', data.token)
    localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message
  } finally {
    isRegistering.value = false
  }
}

const onValidIdChange = (event) => {
  const file = event.target.files?.[0] || null
  validIdFile.value = file
}

</script>

<style scoped>

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
    rgba(242, 195, 0, 0.8),
    rgba(255, 255, 255, 0.9),
    rgba(145, 175, 230, 0.9),
    rgba(255, 255, 255, 0.9)
  );
  filter: blur(16px);
  opacity: 0.85;
  z-index: 0;
  animation: glowFlow 10s linear infinite;
}

.pop-shell::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 36px;
  background: conic-gradient(
    from 0deg,
    rgba(255, 255, 255, 0.95),
    rgba(242, 195, 0, 0.85),
    rgba(255, 255, 255, 0.95),
    rgba(145, 175, 230, 0.9),
    rgba(255, 255, 255, 0.95)
  );
  padding: 3px;
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  z-index: 0;
  animation: glowFlow 12s linear infinite;
}

.pop-card {
  position: relative;
  z-index: 1;
  border-radius: 34px;
  padding: 2.25rem 3.75rem 2.5rem 2.75rem;
  background:
    radial-gradient(circle at top center, rgba(255, 255, 255, 0.98), rgba(246, 249, 255, 0.86)),
    linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(242, 245, 252, 0.95));
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.pop-top {
  position: relative;
  margin-bottom: 2.5rem;
}

.pop-brand {
  text-align: left;
  display: grid;
  justify-items: start;
  gap: 0.4rem;
}

.pop-eyebrow {
  text-transform: uppercase;
  letter-spacing: 0.35em;
  font-size: 0.68rem;
  color: #6b7280;
  font-weight: 600;
}

.pop-title {
  font-size: 2.4rem;
  line-height: 1.1;
  font-weight: 700;
  color: #0b2c6f;
}

.pop-subtitle {
  font-size: 1rem;
  color: #6b7280;
}

.pop-action {
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  cursor: pointer;
}

.pop-action:hover:not(:disabled) {
  background: #f6cf2a;
  box-shadow: 0 18px 36px rgba(242, 195, 0, 0.35);
  transform: translateY(-1px);
}

.pop-action:active:not(:disabled) {
  transform: translateY(0);
}

.pop-link {
  transition: color 0.2s ease;
  cursor: pointer;
}

.pop-link:hover {
  color: #092b6c;
}

.landing-screen {
  position: fixed;
  inset: 0;
  background:
    radial-gradient(circle at 15% 10%, rgba(255, 255, 255, 0.95), rgba(215, 231, 255, 0.65) 40%),
    radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.8), rgba(255, 216, 123, 0.45) 45%),
    linear-gradient(160deg, #edf2ff 10%, #f7f4ee 65%, #f1f5ff 100%);
  display: grid;
  place-items: center;
  z-index: 20;
  overflow: hidden;
  cursor: pointer;
}

.landing-sunray {
  position: fixed;
  inset: -60%;
  background-image:
    radial-gradient(circle at center, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0) 70%),
    repeating-conic-gradient(
      from 0deg,
      rgba(11, 44, 111, 0.18) 0deg,
      rgba(11, 44, 111, 0.18) 10deg,
      rgba(255, 255, 255, 0) 10deg,
      rgba(255, 255, 255, 0) 20deg
    );
  -webkit-mask-image: radial-gradient(
    circle at center,
    rgba(0, 0, 0, 0) 0 1%,
    rgba(0, 0, 0, 0.2) 4%,
    rgba(0, 0, 0, 0.7) 22%,
    rgba(0, 0, 0, 1) 100%
  );
  mask-image: radial-gradient(
    circle at center,
    rgba(0, 0, 0, 0) 0 1%,
    rgba(0, 0, 0, 0.2) 4%,
    rgba(0, 0, 0, 0.7) 22%,
    rgba(0, 0, 0, 1) 100%
  );
  opacity: 0.75;
  filter: blur(0.8px);
  pointer-events: none;
  z-index: 0;
  transform-origin: 50% 50%;
  animation: raysPulse 8s ease-in-out infinite, raysRotate 180s linear infinite;
}

.landing-content {
  position: relative;
  z-index: 2;
  text-align: center;
  display: grid;
  gap: 1rem;
  justify-items: center;
}

.landing-logo {
  width: 170px;
  height: 170px;
  object-fit: contain;
  filter:
    drop-shadow(0 18px 36px rgba(11, 44, 111, 0.25))
    drop-shadow(0 0 18px rgba(255, 214, 102, 0.45));
  animation: logoBreathe 5s ease-in-out infinite, logoPulse 2.6s ease-in-out infinite;
}

.landing-kicker {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.4em;
  color: #6b7280;
  font-weight: 700;
  margin-top: 0.35rem;
}

.landing-title {
  font-size: 3.1rem;
  color: #0b2c6f;
  font-weight: 800;
  text-shadow: 0 18px 36px rgba(11, 44, 111, 0.18);
}

.landing-tap {
  margin-top: 1.4rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  color: #6b7280;
  font-size: 0.85rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  animation: pulseText 2.4s ease-in-out infinite;
}

.landing-line {
  height: 1px;
  width: 120px;
  background: rgba(11, 44, 111, 0.2);
}

.landing-text {
  font-weight: 700;
}

.landing-skyline {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 24vh;
  background: url("/buildings.png") repeat-x bottom center;
  background-size: contain;
  z-index: 1;
  opacity: 0.95;
}

.auth-overlay {
  position: fixed;
  inset: 0;
  z-index: 40;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3.5rem 1.5rem;
  background: rgba(7, 19, 40, 0.68);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  overflow-y: auto;
}

.auth-dialog {
  width: 100%;
  max-width: 42rem;
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
}

.auth-close svg {
  width: 1.25rem;
  height: 1.25rem;
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

@keyframes popIn {
  0% {
    transform: scale(0.96);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes glowFlow {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
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

@keyframes pulseText {
  0%,
  100% {
    transform: translateY(0);
    opacity: 0.85;
  }
  50% {
    transform: translateY(6px);
    opacity: 1;
  }
}

@keyframes logoBreathe {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

@keyframes logoPulse {
  0%,
  100% {
    filter:
      drop-shadow(0 18px 36px rgba(11, 44, 111, 0.25))
      drop-shadow(0 0 14px rgba(255, 214, 102, 0.35));
  }
  50% {
    filter:
      drop-shadow(0 22px 44px rgba(11, 44, 111, 0.3))
      drop-shadow(0 0 28px rgba(255, 214, 102, 0.6));
  }
}

.form-swap-enter-active,
.form-swap-leave-active {
  transition: opacity 0.35s ease, transform 0.35s ease;
}

.form-swap-enter-from,
.form-swap-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

</style>
