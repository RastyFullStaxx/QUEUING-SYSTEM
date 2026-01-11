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
          <img src="/logo.png" alt="Barangay San Miguel" class="mx-auto h-50 w-50 object-contain" />
          <p class="mt-6 text-sm uppercase tracking-[0.4em] text-[#6B7280]">Queue Registration Kiosk</p>
          <h1 class="mt-3 text-6xl font-semibold text-[#0B2C6F]">Barangay San Miguel</h1>
          <div class="mt-10 text-sm uppercase tracking-[0.35em] text-[#6B7280] intro-pulse intro-guide">
            Tap anywhere to begin
          </div>
        </div>
        <div class="buildings">
          <svg viewBox="0 0 1200 220" preserveAspectRatio="none">
            <rect x="0" y="80" width="80" height="140" fill="#0B2C6F" />
            <rect x="90" y="100" width="70" height="120" fill="#F2C300" />
            <rect x="170" y="60" width="110" height="160" fill="#0B2C6F" />
            <rect x="300" y="90" width="90" height="130" fill="#F2C300" />
            <rect x="410" y="40" width="130" height="180" fill="#0B2C6F" />
            <rect x="560" y="110" width="80" height="110" fill="#F2C300" />
            <rect x="660" y="70" width="100" height="150" fill="#0B2C6F" />
            <rect x="780" y="100" width="90" height="120" fill="#F2C300" />
            <rect x="890" y="50" width="140" height="170" fill="#0B2C6F" />
            <rect x="1050" y="90" width="90" height="130" fill="#F2C300" />
            <rect x="0" y="200" width="1200" height="20" fill="#0B2C6F" />
          </svg>
        </div>
      </div>
      <div
        v-else
        key="login"
        class="mx-auto max-w-5xl px-6 py-16 grid lg:grid-cols-[1fr,1fr] gap-12 items-center"
      >
      <div class="space-y-6">
        <div class="inline-flex items-center gap-3 rounded-full bg-[#F3F4F6] text-[#0B2C6F] px-4 py-2 text-base border border-[#E5E7EB]">
          <img src="/logo.png" alt="Barangay San Miguel" class="h-5 w-5 object-contain" />
          Barangay San Miguel
        </div>
        <div>
          <p class="text-sm uppercase tracking-[0.35em] text-[#6B7280]">Resident Portal</p>
          <h1 class="text-6xl font-semibold leading-tight text-[#0B2C6F]">
            Queue Registration
            <span class="block text-[#F2C300]">fast and simple</span>
          </h1>
        </div>
        <p class="text-[#6B7280] text-xl">
          Log in to check your approval status and keep your QR code ready for the kiosk.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-base text-[#6B7280]">
          <div class="rounded-xl border border-[#E5E7EB] bg-white p-4">
            <p class="font-semibold text-[#0B2C6F]">Kiosk-ready</p>
            <p class="mt-1">Your QR code appears after approval.</p>
          </div>
          <div class="rounded-xl border border-[#E5E7EB] bg-white p-4">
            <p class="font-semibold text-[#0B2C6F]">Clear status</p>
            <p class="mt-1">Approved, pending, or rejected at a glance.</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-3xl border border-[#E5E7EB] p-10">
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
          <button class="w-full bg-[#F2C300] text-black py-4 rounded-2xl text-xl font-semibold">Login</button>
        </form>
        <div class="mt-6 flex items-center justify-between text-base">
          <span class="text-[#6B7280]">Need an account?</span>
          <router-link class="text-[#0B2C6F] font-semibold" to="/register">Register</router-link>
        </div>
        <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
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
const showIntro = ref(true)

const onSubmit = async () => {
  error.value = ''
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
  z-index: 2;
}

.buildings svg {
  width: 100%;
  height: 100%;
  display: block;
}

.intro-content {
  z-index: 3;
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

</style>
