<template>
  <div class="min-h-screen relative bg-[#F7F8FB]">
    <div
      class="landing-screen landing-ensure-visible"
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
                    <button class="text-[#0B2C6F] font-semibold pop-link" type="button" @click="mode = 'login'">
                      Sign in
                    </button>
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
