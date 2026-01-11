<template>
  <div class="min-h-screen">
    <div class="mx-auto max-w-6xl px-6 py-16 grid lg:grid-cols-[1.1fr,0.9fr] gap-12 items-center">
      <div class="space-y-8">
        <div class="inline-flex items-center gap-3 rounded-full bg-[#F3F4F6] text-[#0B2C6F] px-4 py-2 text-sm border border-[#E5E7EB]">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l9 4.5v11L12 22l-9-4.5v-11L12 2z" />
            <path d="M12 22V12" />
            <path d="M21 6.5l-9 5-9-5" />
          </svg>
          Barangay San Miguel
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.35em] text-[#6B7280]">Resident Portal</p>
          <h1 class="text-5xl font-semibold leading-tight text-[#0B2C6F]">
            Queue Registration
            <span class="block text-[#F2C300]">ready for the kiosk</span>
          </h1>
        </div>
        <p class="text-[#6B7280] text-lg">
          Manage requests, monitor approval status, and keep a ready-to-scan QR code for faster service.
        </p>
        <div class="grid sm:grid-cols-3 gap-4">
          <div class="rounded-2xl bg-white border border-[#E5E7EB] p-4">
            <p class="text-xs uppercase tracking-[0.2em] text-[#6B7280]">Step 1</p>
            <p class="mt-2 font-semibold">Register</p>
            <p class="text-sm text-[#6B7280]">Create your resident profile.</p>
          </div>
          <div class="rounded-2xl bg-white border border-[#E5E7EB] p-4">
            <p class="text-xs uppercase tracking-[0.2em] text-[#6B7280]">Step 2</p>
            <p class="mt-2 font-semibold">Verify</p>
            <p class="text-sm text-[#6B7280]">Barangay staff validates your ID.</p>
          </div>
          <div class="rounded-2xl bg-white border border-[#E5E7EB] p-4">
            <p class="text-xs uppercase tracking-[0.2em] text-[#6B7280]">Step 3</p>
            <p class="mt-2 font-semibold">Scan</p>
            <p class="text-sm text-[#6B7280]">Use your QR at the kiosk.</p>
          </div>
        </div>
        <div class="flex flex-wrap items-center gap-4 text-sm text-[#6B7280]">
          <span class="inline-flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32]"></span>
            Verified services
          </span>
          <span class="inline-flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#0B2C6F]"></span>
            Real-time updates
          </span>
          <span class="inline-flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#F2C300]"></span>
            Kiosk-ready QR
          </span>
        </div>
      </div>
      <div class="relative">
        <div class="relative bg-white rounded-3xl border border-[#E5E7EB] p-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="h-12 w-12 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 20c1.8-4 6-6 8-6s6.2 2 8 6" />
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-semibold">Sign in</h2>
              <p class="text-sm text-[#6B7280]">Use your registered email.</p>
            </div>
          </div>
          <form class="space-y-4" @submit.prevent="onSubmit">
            <div>
              <label class="text-sm font-semibold text-[#0B2C6F]">Email</label>
              <input
                v-model="email"
                type="email"
                placeholder="you@example.com"
                class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                required
              />
            </div>
            <div>
              <label class="text-sm font-semibold text-[#0B2C6F]">Password</label>
              <input
                v-model="password"
                type="password"
                placeholder="Enter your password"
                class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                required
              />
            </div>
            <button class="w-full bg-[#F2C300] text-black py-3 rounded-2xl text-lg font-semibold">Login</button>
          </form>
          <div class="mt-5 flex items-center justify-between text-sm">
            <span class="text-[#6B7280]">Need an account?</span>
            <router-link class="text-[#0B2C6F] font-semibold" to="/register">Register</router-link>
          </div>
          <div class="mt-6 rounded-2xl border border-[#E5E7EB] bg-[#F3F4F6] px-4 py-3 text-xs text-[#6B7280]">
            For kiosk assistance, visit the help desk or call the barangay front desk.
          </div>
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
