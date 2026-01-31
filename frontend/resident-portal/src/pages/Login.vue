<template>
  <div class="landing-shell">
    <section
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
    </section>

    <transition name="auth-fade">
      <div v-if="showAuth" class="auth-overlay">
        <div class="auth-dialog">
          <div class="pop-shell">
            <div class="pop-card">
              <button class="auth-close" type="button" aria-label="Close" @click="closeAuth">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M6 6l12 12M18 6l-12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
              </button>
              <div class="auth-layout">
                <aside class="auth-aside">
                  <p class="auth-kicker">Resident Portal</p>
                  <h2 class="auth-heading">Barangay San Miguel</h2>
                  <p class="auth-lead">
                    Register once, get verified, and download your Resident ID with QR for faster kiosk access.
                  </p>
                  <div class="auth-steps">
                    <div class="auth-step">
                      <span class="auth-step-count">01</span>
                      <div>
                        <p class="auth-step-title">Submit details</p>
                        <p class="auth-step-text">Complete the personal info form and upload a valid ID.</p>
                      </div>
                    </div>
                    <div class="auth-step">
                      <span class="auth-step-count">02</span>
                      <div>
                        <p class="auth-step-title">Admin verification</p>
                        <p class="auth-step-text">Barangay staff will review and approve your account.</p>
                      </div>
                    </div>
                    <div class="auth-step">
                      <span class="auth-step-count">03</span>
                      <div>
                        <p class="auth-step-title">Download your ID</p>
                        <p class="auth-step-text">Save the Resident ID with QR to your phone.</p>
                      </div>
                    </div>
                  </div>
                  <div class="auth-support">
                    Need assistance? <a class="auth-support-link" :href="helpLink">Contact support</a>
                  </div>
                </aside>

                <div class="auth-panel">
                  <div class="pop-top">
                    <div class="pop-brand">
                      <p class="pop-eyebrow">Barangay San Miguel</p>
                      <h2 class="pop-title">{{ mode === 'login' ? 'Sign in' : 'Create account' }}</h2>
                      <p class="pop-subtitle">
                        {{ mode === 'login' ? 'Use your registered email to continue.' : 'Create your resident access profile.' }}
                      </p>
                    </div>
                  </div>

                  <div class="auth-toggle" role="tablist" aria-label="Authentication mode">
                    <button
                      class="auth-toggle-btn"
                      :class="{ active: mode === 'login' }"
                      type="button"
                      role="tab"
                      :aria-selected="mode === 'login'"
                      @click="mode = 'login'"
                    >
                      Sign in
                    </button>
                    <button
                      class="auth-toggle-btn"
                      :class="{ active: mode === 'register' }"
                      type="button"
                      role="tab"
                      :aria-selected="mode === 'register'"
                      @click="mode = 'register'"
                    >
                      Create account
                    </button>
                  </div>

                  <transition name="form-swap" mode="out-in">
                    <form v-if="mode === 'login'" key="login-form" class="auth-form" @submit.prevent="onSubmit">
                      <div class="auth-field">
                        <label>Email</label>
                        <input
                          v-model="email"
                          type="email"
                          placeholder="you@example.com"
                          autocomplete="email"
                          class="auth-input"
                          required
                        />
                      </div>
                      <div class="auth-field">
                        <label>Password</label>
                        <input
                          v-model="password"
                          type="password"
                          placeholder="Enter your password"
                          autocomplete="current-password"
                          class="auth-input"
                          required
                        />
                      </div>
                      <button class="auth-primary" :disabled="isLoading">
                        {{ isLoading ? 'Signing in...' : 'Login' }}
                      </button>
                      <div class="auth-meta">
                        <span>Need an account?</span>
                        <button class="pop-link" type="button" @click="mode = 'register'">
                          Create account
                        </button>
                      </div>
                    </form>

                    <form v-else key="register-form" class="auth-form" @submit.prevent="onRegister">
                      <section class="auth-section">
                        <div class="auth-section-head">
                          <div>
                            <h3>Account setup</h3>
                            <p>Choose a username and secure password.</p>
                          </div>
                        </div>
                        <div class="auth-grid">
                          <div class="auth-field">
                            <label>Username</label>
                            <input
                              v-model="username"
                              type="text"
                              placeholder="bsm-resident"
                              autocomplete="username"
                              class="auth-input"
                              required
                            />
                          </div>
                          <div class="auth-field">
                            <label>Email</label>
                            <input
                              v-model="regEmail"
                              type="email"
                              placeholder="you@example.com"
                              autocomplete="email"
                              class="auth-input"
                              required
                            />
                          </div>
                        </div>
                        <div class="auth-grid">
                          <div class="auth-field">
                            <label>Password</label>
                            <input
                              v-model="regPassword"
                              type="password"
                              placeholder="Create a password"
                              autocomplete="new-password"
                              class="auth-input"
                              required
                            />
                          </div>
                          <div class="auth-field">
                            <label>Confirm password</label>
                            <input
                              v-model="confirmPassword"
                              type="password"
                              placeholder="Re-enter password"
                              autocomplete="new-password"
                              class="auth-input"
                              required
                            />
                          </div>
                        </div>
                      </section>

                      <section class="auth-section">
                        <div class="auth-section-head">
                          <div>
                            <h3>Personal information</h3>
                            <p>Ensure your details match your government ID.</p>
                          </div>
                        </div>
                        <div class="auth-grid">
                          <div class="auth-field">
                            <label>First name</label>
                            <input
                              v-model="firstName"
                              type="text"
                              placeholder="Juan"
                              autocomplete="given-name"
                              class="auth-input"
                              required
                            />
                          </div>
                          <div class="auth-field">
                            <label>Middle name</label>
                            <input
                              v-model="middleName"
                              type="text"
                              placeholder="Santos"
                              autocomplete="additional-name"
                              class="auth-input"
                            />
                          </div>
                          <div class="auth-field">
                            <label>Last name</label>
                            <input
                              v-model="lastName"
                              type="text"
                              placeholder="Dela Cruz"
                              autocomplete="family-name"
                              class="auth-input"
                              required
                            />
                          </div>
                        </div>
                        <div class="auth-grid">
                          <div class="auth-field">
                            <label>Date of birth</label>
                            <input
                              v-model="dateOfBirth"
                              type="date"
                              autocomplete="bday"
                              class="auth-input"
                              required
                            />
                          </div>
                          <div class="auth-field">
                            <label>Gender</label>
                            <select v-model="gender" class="auth-select" required>
                              <option value="" disabled>Select gender</option>
                              <option v-for="option in genderOptions" :key="option" :value="option">{{ option }}</option>
                            </select>
                          </div>
                          <div class="auth-field">
                            <label>Civil status</label>
                            <select v-model="civilStatus" class="auth-select" required>
                              <option value="" disabled>Select status</option>
                              <option v-for="option in civilStatusOptions" :key="option" :value="option">{{ option }}</option>
                            </select>
                          </div>
                        </div>
                      </section>

                      <section class="auth-section">
                        <div class="auth-section-head">
                          <div>
                            <h3>Contact & address</h3>
                            <p>We will use this for verification updates.</p>
                          </div>
                        </div>
                        <div class="auth-grid">
                          <div class="auth-field">
                            <label>Mobile number</label>
                            <input
                              v-model="mobileNumber"
                              type="tel"
                              placeholder="09xx xxx xxxx"
                              autocomplete="tel"
                              class="auth-input"
                              required
                            />
                          </div>
                        </div>
                        <div class="auth-field">
                          <label>Barangay address</label>
                          <textarea
                            v-model="address"
                            rows="3"
                            placeholder="House no., street, barangay, city"
                            autocomplete="street-address"
                            class="auth-textarea"
                            required
                          ></textarea>
                        </div>
                      </section>

                      <section class="auth-section">
                        <div class="auth-section-head">
                          <div>
                            <h3>ID verification</h3>
                            <p>Upload a clear photo or PDF of your government-issued ID.</p>
                          </div>
                        </div>
                        <div class="auth-field">
                          <label>Valid ID upload</label>
                          <input
                            type="file"
                            accept="image/*,.pdf"
                            class="auth-file"
                            @change="onValidIdChange"
                            required
                          />
                          <p class="auth-help">
                            {{ validIdName }}
                          </p>
                        </div>
                      </section>

                      <button class="auth-primary" :disabled="isRegistering || isPasswordMismatch">
                        {{ isRegistering ? 'Creating...' : 'Create Account' }}
                      </button>
                      <div class="auth-meta">
                        <span>Already have an account?</span>
                        <button class="pop-link" type="button" @click="mode = 'login'">
                          Sign in
                        </button>
                      </div>
                    </form>
                  </transition>
                  <p v-if="error" class="auth-error">{{ error }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
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
const middleName = ref('')
const lastName = ref('')
const regEmail = ref('')
const regPassword = ref('')
const confirmPassword = ref('')
const username = ref('')
const dateOfBirth = ref('')
const gender = ref('')
const civilStatus = ref('')
const mobileNumber = ref('')
const address = ref('')
const isRegistering = ref(false)
const validIdFile = ref(null)
const helpLink = 'mailto:helpdesk@barangay.local'

const genderOptions = ['Male', 'Female', 'Non-binary', 'Prefer not to say']
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Widowed']
const validIdName = computed(() => validIdFile.value?.name || 'No file selected yet.')
const isPasswordMismatch = computed(() => {
  if (!regPassword.value || !confirmPassword.value) return false
  return regPassword.value !== confirmPassword.value
})

const openAuth = () => {
  mode.value = 'login'
  error.value = ''
  showAuth.value = true
}

const closeAuth = () => {
  showAuth.value = false
  error.value = ''
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
    let cachedProfile = {}
    const cached = localStorage.getItem('resident_profile')
    if (cached) {
      try {
        cachedProfile = JSON.parse(cached)
      } catch (err) {
        cachedProfile = {}
      }
    }
    localStorage.setItem('resident_profile', JSON.stringify({ ...cachedProfile, ...data.resident }))
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
  if (isPasswordMismatch.value) {
    error.value = 'Passwords do not match.'
    isRegistering.value = false
    return
  }
  try {
    const formData = new FormData()
    formData.append('username', username.value)
    formData.append('first_name', firstName.value)
    formData.append('middle_name', middleName.value)
    formData.append('last_name', lastName.value)
    formData.append('date_of_birth', dateOfBirth.value)
    formData.append('gender', gender.value)
    formData.append('mobile_number', mobileNumber.value)
    formData.append('civil_status', civilStatus.value)
    formData.append('address', address.value)
    formData.append('email', regEmail.value)
    formData.append('password', regPassword.value)
    formData.append('valid_id', validIdFile.value)
    const data = await request('/api/auth/resident/register', {
      method: 'POST',
      body: formData,
    })
    localStorage.setItem('resident_token', data.token)
    const enrichedResident = {
      ...data.resident,
      username: username.value,
      middle_name: middleName.value,
      date_of_birth: dateOfBirth.value,
      gender: gender.value,
      mobile_number: mobileNumber.value,
      civil_status: civilStatus.value,
      address: address.value,
    }
    localStorage.setItem('resident_profile', JSON.stringify(enrichedResident))
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
