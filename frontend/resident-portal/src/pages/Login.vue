<template>
  <div
    class="min-h-screen relative transition-colors duration-700"
    :class="showIntro ? 'bg-white' : 'bg-[#F3F4F6]'"
  >
    <transition name="fade-slide" mode="out-in">
      <div
        v-if="showIntro"
        key="intro"
        class="min-h-screen flex items-center justify-center px-6 relative overflow-hidden"
        role="button"
        tabindex="0"
        @click="showIntro = false"
        @keydown.enter.prevent="showIntro = false"
        @keydown.space.prevent="showIntro = false"
      >
        <div class="sun-rays"></div>
        <div class="intro-orb orb-1"></div>
        <div class="intro-orb orb-2"></div>
        <div class="intro-orb orb-3"></div>
        <div class="text-center max-w-xl relative intro-content intro-center">
          <img src="/logo.png" alt="Barangay San Miguel" class="mx-auto h-50 w-50 object-contain logo-breathe" />
          <p class="mt-6 text-sm uppercase tracking-[0.4em] text-[#6B7280]">Queue Registration Kiosk</p>
          <h1 class="mt-3 text-6xl font-semibold text-[#0B2C6F]">Barangay San Miguel</h1>
          <div class="mt-10 text-sm uppercase tracking-[0.35em] text-[#6B7280] intro-pulse intro-guide">
            Tap anywhere to begin
          </div>
        </div>
        <div class="buildings"></div>
      </div>
      <div
        v-else
        key="login"
        class="min-h-screen w-full px-10 py-16 flex items-center justify-center relative z-10"
      >
      <div class="bg-white rounded-3xl border border-[#E5E7EB] p-10 w-full max-w-2xl pop-card relative">
        <button
          class="absolute top-4 right-4 h-10 w-10 rounded-full border border-[#E5E7EB] text-[#0B2C6F] text-2xl leading-none"
          @click="showIntro = true"
          aria-label="Close"
        >
          ×
        </button>
        <div class="flex items-center gap-3 mb-6">
          <div class="h-12 w-12 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 20c1.8-4 6-6 8-6s6.2 2 8 6" />
            </svg>
          </div>
          <div>
            <h2 class="text-3xl font-semibold">Sign in</h2>
            <p class="text-base text-[#6B7280]">Use your registered email.</p>
          </div>
        </div>
        <form class="space-y-5" @submit.prevent="onSubmit">
          <div>
            <label class="text-base font-semibold text-[#0B2C6F]">Email</label>
            <input
              v-model="email"
              type="email"
              placeholder="you@example.com"
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
              class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
              required
            />
          </div>
          <button
            class="w-full bg-[#F2C300] text-black py-4 rounded-2xl text-xl font-semibold disabled:opacity-70 disabled:cursor-not-allowed"
            :disabled="isLoading"
          >
            {{ isLoading ? 'Signing in...' : 'Login' }}
          </button>
        </form>
        <div class="mt-6 flex items-center justify-between text-base">
          <span class="text-[#6B7280]">Need an account?</span>
          <button class="text-[#0B2C6F] font-semibold" type="button" @click="mode = 'register'">
            Create account
          </button>
        </div>
        <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
        <div v-if="mode === 'register'" class="mt-8 border-t border-[#E5E7EB] pt-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3v18" />
                <path d="M3 12h18" />
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-semibold">Create account</h3>
              <p class="text-base text-[#6B7280]">Register for resident access.</p>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-base font-semibold text-[#0B2C6F]">First name</label>
              <input
                v-model="firstName"
                type="text"
                placeholder="Juan"
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
                class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                required
              />
            </div>
          </div>
          <div class="mt-4">
            <label class="text-base font-semibold text-[#0B2C6F]">Email</label>
            <input
              v-model="regEmail"
              type="email"
              placeholder="you@example.com"
              class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
              required
            />
          </div>
          <div class="mt-4">
            <label class="text-base font-semibold text-[#0B2C6F]">Password</label>
            <input
              v-model="regPassword"
              type="password"
              placeholder="Create a password"
              class="mt-2 w-full border border-[#E5E7EB] px-5 py-4 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
              required
            />
          </div>
          <button
            class="mt-5 w-full bg-[#F2C300] text-black py-4 rounded-2xl text-xl font-semibold disabled:opacity-70 disabled:cursor-not-allowed"
            :disabled="isRegistering"
            @click="onRegister"
          >
            {{ isRegistering ? 'Creating...' : 'Create Account' }}
          </button>
          <div class="mt-4 text-base text-[#6B7280]">
            Already have an account?
            <button class="text-[#0B2C6F] font-semibold" type="button" @click="mode = 'login'">Sign in</button>
          </div>
        </div>
      </div>
      </div>
    </transition>
    <transition name="fade-overlay">
      <div v-if="!showIntro" class="modal-overlay"></div>
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
const firstName = ref('')
const lastName = ref('')
const regEmail = ref('')
const regPassword = ref('')
const isRegistering = ref(false)
const showIntro = ref(true)

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
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(12px);
}

.fade-overlay-enter-active,
.fade-overlay-leave-active {
  transition: opacity 0.3s ease;
}

.fade-overlay-enter-from,
.fade-overlay-leave-to {
  opacity: 0;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(11, 44, 111, 0.12);
  backdrop-filter: blur(1px);
  z-index: 5;
}

.pop-card {
  animation: popIn 0.4s ease-out;
}

.intro-orb {
  position: absolute;
  border-radius: 9999px;
  opacity: 0.25;
  border: 2px solid #0b2c6f;
  background: transparent;
  animation: float 8s ease-in-out infinite;
  z-index: 1;
}

.sun-rays {
  position: absolute;
  inset: -60%;
  background-image:
    radial-gradient(circle at center, #ffffff 0%, #ffffff 14%, rgba(255, 255, 255, 0) 20%),
    repeating-conic-gradient(
      from 0deg,
      rgba(11, 44, 111, 0.06) 0deg,
      rgba(11, 44, 111, 0.06) 10deg,
      rgba(255, 255, 255, 0) 10deg,
      rgba(255, 255, 255, 0) 20deg
    );
  animation: raysPulse 8s ease-in-out infinite, raysRotate 180s linear infinite;
  transform-origin: 50% 50%;
  z-index: 0;
}


.buildings {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 220px;
  z-index: 3;
  background-image: url("/buildings.png");
  background-repeat: repeat-x;
  background-size: auto 100%;
}

.buildings::after {
  content: "";
  position: absolute;
  inset: 0;
  background-color: rgba(255, 255, 255, 0.08);
  animation: shimmer 6s ease-in-out infinite;
  pointer-events: none;
}

.intro-content {
  z-index: 4;
}

.intro-center {
  transform: translateY(-6vh);
}

.intro-guide {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 1.5rem;
  white-space: nowrap;
}

.intro-guide::before,
.intro-guide::after {
  content: "";
  display: block;
  height: 2px;
  width: 10rem;
  background-color: #0b2c6f;
  opacity: 0.3;
}

.logo-breathe {
  animation: logoBreathe 8s ease-in-out infinite;
}


.orb-1 {
  width: 220px;
  height: 220px;
  top: 12%;
  left: 8%;
}

.orb-2 {
  width: 160px;
  height: 160px;
  border-color: #f2c300;
  top: 20%;
  right: 10%;
  animation-delay: 1s;
}

.orb-3 {
  width: 280px;
  height: 280px;
  bottom: 10%;
  right: 20%;
  animation-delay: 2s;
}

.intro-pulse {
  animation: pulse 2.2s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-12px);
  }
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.03);
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

@keyframes logoBreathe {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
}

@keyframes shimmer {
  0%,
  100% {
    opacity: 0.3;
  }
  50% {
    opacity: 0.8;
  }
}


</style>
