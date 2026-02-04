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
          <div class="pop-shell" :class="{ 'pop-shell--static': mode === 'register' }">
            <div class="pop-card" :class="{ 'pop-card--register': mode === 'register', 'pop-card--static': mode === 'register' }">
              <button class="auth-close" type="button" aria-label="Close" @click="closeAuth">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M6 6l12 12M18 6l-12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
              </button>
              <div class="auth-layout" :class="{ 'auth-layout--single': mode === 'register' }">
                <aside v-if="mode === 'login'" class="auth-aside">
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

                    <form v-else key="register-form" class="auth-form auth-form--register" @submit.prevent="onRegister">
                      <div class="auth-form-grid">
                        <div class="auth-column">
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
                        </div>

                        <div class="auth-column">
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
                        </div>
                      </div>

                      <div class="auth-footer">
                        <button class="auth-primary" :disabled="isRegistering || isPasswordMismatch">
                          {{ isRegistering ? 'Creating...' : 'Create Account' }}
                        </button>
                        <div class="auth-meta">
                          <span>Already have an account?</span>
                          <button class="pop-link" type="button" @click="mode = 'login'">
                            Sign in
                          </button>
                        </div>
                      </div>
                    </form>
                  </transition>
                  <div v-if="notice.open" class="auth-modal-overlay" role="alertdialog" aria-modal="true">
                    <div class="auth-modal-card">
                      <div class="auth-modal-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                          <path d="M12 8v5" stroke-linecap="round" />
                          <circle cx="12" cy="16.5" r="1" fill="currentColor" />
                          <path d="M10.2 3.8 3.7 16a2 2 0 0 0 1.8 3h13a2 2 0 0 0 1.8-3l-6.5-12a2 2 0 0 0-3.6 0Z" />
                        </svg>
                      </div>
                      <div>
                        <h3 class="auth-modal-title">{{ notice.title }}</h3>
                        <p class="auth-modal-text">{{ notice.message }}</p>
                      </div>
                      <div class="auth-modal-actions">
                        <button class="auth-modal-primary" type="button" @click="closeNotice">
                          {{ notice.primaryLabel }}
                        </button>
                        <button
                          v-if="notice.secondaryLabel"
                          class="auth-modal-ghost"
                          type="button"
                          @click="runNoticeSecondary"
                        >
                          {{ notice.secondaryLabel }}
                        </button>
                      </div>
                    </div>
                  </div>
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
const notice = ref({
  open: false,
  title: '',
  message: '',
  primaryLabel: 'Okay',
  secondaryLabel: '',
  onPrimary: null,
  onSecondary: null,
})
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
  notice.value.open = false
  showAuth.value = true
}

const closeAuth = () => {
  showAuth.value = false
  notice.value.open = false
}

const resetRegisterForm = () => {
  firstName.value = ''
  middleName.value = ''
  lastName.value = ''
  regEmail.value = ''
  regPassword.value = ''
  confirmPassword.value = ''
  username.value = ''
  dateOfBirth.value = ''
  gender.value = ''
  civilStatus.value = ''
  mobileNumber.value = ''
  address.value = ''
  validIdFile.value = null
}

const openNotice = ({ title, message, primaryLabel = 'Okay', secondaryLabel = '', onPrimary = null, onSecondary = null }) => {
  notice.value = { open: true, title, message, primaryLabel, secondaryLabel, onPrimary, onSecondary }
}

const closeNotice = () => {
  const handler = notice.value.onPrimary
  notice.value.open = false
  notice.value.onPrimary = null
  if (typeof handler === 'function') handler()
}

const runNoticeSecondary = () => {
  const handler = notice.value.onSecondary
  notice.value.open = false
  notice.value.onSecondary = null
  if (typeof handler === 'function') handler()
}

const onSubmit = async () => {
  notice.value.open = false
  isLoading.value = true
  try {
    const data = await request('/api/auth/resident/login', {
      method: 'POST',
      body: JSON.stringify({ email: email.value, password: password.value }),
    })
    if (data.resident?.status !== 'approved') {
      const status = data.resident?.status || 'pending'
      const note = data.resident?.status_message
      const baseMessage =
        status === 'rejected'
          ? 'Your account was not approved. Please review the message below or update your details.'
          : 'Your registration is under verification by the barangay admin.'
      const message = note ? `${baseMessage}\n\nMessage from admin: ${note}` : baseMessage
      openNotice({
        title: status === 'rejected' ? 'Account Rejected' : 'Account Pending',
        message,
        primaryLabel: 'Okay',
        secondaryLabel: 'Update details',
        onSecondary: () => {
          mode.value = 'register'
        },
      })
      return
    }
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
    const payload = err?.data || {}
    if (payload.status && payload.status !== 'approved') {
      const status = payload.status
      const baseMessage =
        status === 'rejected'
          ? 'Your account was not approved. Please review the message below or update your details.'
          : 'Your registration is under verification by the barangay admin.'
      const message = payload.message ? `${baseMessage}\n\nMessage from admin: ${payload.message}` : baseMessage
      openNotice({
        title: status === 'rejected' ? 'Account Rejected' : 'Account Pending',
        message,
        primaryLabel: 'Okay',
        secondaryLabel: 'Update details',
        onSecondary: () => {
          mode.value = 'register'
        },
      })
    } else {
      openNotice({ title: 'Login Failed', message: err.message || 'Unable to sign in.' })
    }
  } finally {
    isLoading.value = false
  }
}

const onRegister = async () => {
  notice.value.open = false
  isRegistering.value = true
  if (!validIdFile.value) {
    openNotice({ title: 'Valid ID required', message: 'Please upload a clear photo or PDF of your valid ID.' })
    isRegistering.value = false
    return
  }
  if (isPasswordMismatch.value) {
    openNotice({ title: 'Password mismatch', message: 'Passwords do not match.' })
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
    openNotice({
      title: 'Registration Submitted',
      message:
        'Your account is now under verification by the barangay admin. Please wait for approval before signing in.',
      onPrimary: () => {
        resetRegisterForm()
        closeAuth()
      },
    })
  } catch (err) {
    openNotice({ title: 'Registration Failed', message: err.message || 'Unable to register.' })
  } finally {
    isRegistering.value = false
  }
}

const onValidIdChange = (event) => {
  const file = event.target.files?.[0] || null
  validIdFile.value = file
}

</script>
