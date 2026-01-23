<template>
  <div class="min-h-screen relative bg-[#F7F8FB]">
    <div class="fixed inset-0 w-full px-6 sm:px-10 py-16 flex items-center justify-center">
      <div class="w-full max-w-2xl pop-shell">
        <div class="pop-card">
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
const firstName = ref('')
const lastName = ref('')
const regEmail = ref('')
const regPassword = ref('')
const isRegistering = ref(false)

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
  try {
    const data = await request('/api/auth/resident/register', {
      method: 'POST',
      body: JSON.stringify({
        first_name: firstName.value,
        last_name: lastName.value,
        email: regEmail.value,
        password: regPassword.value,
      }),
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
  padding: 2.25rem 2.75rem 2.5rem;
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
