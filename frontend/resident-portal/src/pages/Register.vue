<template>
  <div class="min-h-screen">
    <div class="w-full px-10 py-16 grid lg:grid-cols-[1fr,1fr] gap-12 items-center">
      <div class="order-2 lg:order-1 bg-white rounded-3xl border border-[#E5E7EB] p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-12 w-12 rounded-2xl bg-white border border-[#E5E7EB] flex items-center justify-center">
            <img src="/logo.png" alt="Barangay San Miguel" class="h-8 w-8 object-contain" />
          </div>
          <div>
            <h2 class="text-2xl font-semibold">Create an account</h2>
            <p class="text-sm text-[#6B7280]">Get approved to access kiosk services.</p>
          </div>
        </div>
        <form class="auth-form" @submit.prevent="onSubmit">
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
                  v-model="email"
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
                  v-model="password"
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
              <p class="auth-help">{{ validIdName }}</p>
            </div>
          </section>

          <button class="auth-primary" :disabled="isSubmitting || isPasswordMismatch">
            {{ isSubmitting ? 'Creating...' : 'Create Account' }}
          </button>
        </form>
        <div class="mt-5 flex items-center justify-between text-sm">
          <span class="text-[#6B7280]">Already registered?</span>
          <router-link class="text-[#0B2C6F] font-semibold" to="/login">Login</router-link>
        </div>
      </div>
      <div class="order-1 lg:order-2 space-y-6">
        <div>
          <p class="text-xs uppercase tracking-[0.35em] text-[#6B7280]">Get Verified</p>
          <h1 class="text-5xl font-semibold leading-tight text-[#0B2C6F]">
            Create your account
            <span class="block text-[#F2C300]">in minutes</span>
          </h1>
        </div>
        <p class="text-[#6B7280] text-lg">
          Register once, get verified, and use your QR code at the kiosk for faster service.
        </p>
        <div class="rounded-2xl border border-[#E5E7EB] bg-[#F3F4F6] p-4 text-sm text-[#6B7280]">
          Bring a valid ID for quick verification.
        </div>
      </div>
    </div>

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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const firstName = ref('')
const middleName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const username = ref('')
const dateOfBirth = ref('')
const gender = ref('')
const civilStatus = ref('')
const mobileNumber = ref('')
const address = ref('')
const notice = ref({
  open: false,
  title: '',
  message: '',
  primaryLabel: 'Okay',
  onPrimary: null,
})
const validIdFile = ref(null)
const isSubmitting = ref(false)

const genderOptions = ['Male', 'Female', 'Non-binary', 'Prefer not to say']
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Widowed']
const validIdName = computed(() => validIdFile.value?.name || 'No file selected yet.')
const isPasswordMismatch = computed(() => {
  if (!password.value || !confirmPassword.value) return false
  return password.value !== confirmPassword.value
})

const onSubmit = async () => {
  notice.value.open = false
  isSubmitting.value = true
  if (!validIdFile.value) {
    openNotice({ title: 'Valid ID required', message: 'Please upload a clear photo or PDF of your valid ID.' })
    isSubmitting.value = false
    return
  }
  if (isPasswordMismatch.value) {
    openNotice({ title: 'Password mismatch', message: 'Passwords do not match.' })
    isSubmitting.value = false
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
    formData.append('email', email.value)
    formData.append('password', password.value)
    formData.append('valid_id', validIdFile.value)
    const data = await request('/api/auth/resident/register', {
      method: 'POST',
      body: formData,
    })
    localStorage.setItem('resident_token', data.token)
    openNotice({
      title: 'Registration Submitted',
      message:
        'Your account is now under verification by the barangay admin. Please wait for approval before signing in.',
      onPrimary: () => {
        router.push('/login')
      },
    })
  } catch (err) {
    openNotice({ title: 'Registration Failed', message: err.message || 'Unable to register.' })
  } finally {
    isSubmitting.value = false
  }
}

const onValidIdChange = (event) => {
  const file = event.target.files?.[0] || null
  validIdFile.value = file
}

const openNotice = ({ title, message, primaryLabel = 'Okay', onPrimary = null }) => {
  notice.value = { open: true, title, message, primaryLabel, onPrimary }
}

const closeNotice = () => {
  const handler = notice.value.onPrimary
  notice.value.open = false
  notice.value.onPrimary = null
  if (typeof handler === 'function') handler()
}
</script>
