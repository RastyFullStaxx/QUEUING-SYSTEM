<template>
  <div class="min-h-screen">
    <div class="mx-auto max-w-6xl px-6 py-16 grid lg:grid-cols-[0.95fr,1.05fr] gap-12 items-center">
      <div class="order-2 lg:order-1 bg-white rounded-3xl border border-[#E5E7EB] p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-12 w-12 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 3v18" />
              <path d="M3 12h18" />
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-semibold">Create an account</h2>
            <p class="text-sm text-[#6B7280]">Get approved to access kiosk services.</p>
          </div>
        </div>
        <form class="space-y-4" @submit.prevent="onSubmit">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-semibold text-[#0B2C6F]">First name</label>
              <input
                v-model="firstName"
                type="text"
                placeholder="Juan"
                class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                required
              />
            </div>
            <div>
              <label class="text-sm font-semibold text-[#0B2C6F]">Last name</label>
              <input
                v-model="lastName"
                type="text"
                placeholder="Dela Cruz"
                class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
                required
              />
            </div>
          </div>
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
              placeholder="Create a password"
              class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
              required
            />
          </div>
          <button class="w-full bg-[#F2C300] text-black py-3 rounded-2xl text-lg font-semibold">Create Account</button>
        </form>
        <div class="mt-5 flex items-center justify-between text-sm">
          <span class="text-[#6B7280]">Already registered?</span>
          <router-link class="text-[#0B2C6F] font-semibold" to="/login">Login</router-link>
        </div>
        <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
      </div>
      <div class="order-1 lg:order-2 space-y-8">
        <div>
          <p class="text-xs uppercase tracking-[0.35em] text-[#6B7280]">Get Verified</p>
          <h1 class="text-5xl font-semibold leading-tight text-[#0B2C6F]">
            Register once, access
            <span class="block text-[#F2C300]">all barangay services</span>
          </h1>
        </div>
        <p class="text-[#6B7280] text-lg">
          Upload a valid ID after registration. Once approved, a QR code appears for kiosk check-in.
        </p>
        <div class="grid gap-4">
          <div class="flex items-start gap-3 rounded-2xl bg-white border border-[#E5E7EB] p-4">
            <div class="h-10 w-10 rounded-lg bg-[#F3F4F6] text-[#2E7D32] flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12l2-2 4 4 8-8 4 4" />
              </svg>
            </div>
            <div>
              <p class="font-semibold">Fast verification</p>
              <p class="text-sm text-[#6B7280]">Track your approval status in real time.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 rounded-2xl bg-white border border-[#E5E7EB] p-4">
            <div class="h-10 w-10 rounded-lg bg-[#F3F4F6] text-[#0B2C6F] flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v4" />
                <path d="M12 18v4" />
                <path d="M4.9 4.9l2.8 2.8" />
                <path d="M16.3 16.3l2.8 2.8" />
                <path d="M2 12h4" />
                <path d="M18 12h4" />
                <path d="M4.9 19.1l2.8-2.8" />
                <path d="M16.3 7.7l2.8-2.8" />
              </svg>
            </div>
            <div>
              <p class="font-semibold">Priority updates</p>
              <p class="text-sm text-[#6B7280]">Get notified when your QR code is ready.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 rounded-2xl bg-white border border-[#E5E7EB] p-4">
            <div class="h-10 w-10 rounded-lg bg-[#F3F4F6] text-[#0B2C6F] flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 8v4l3 3" />
                <circle cx="12" cy="12" r="9" />
              </svg>
            </div>
            <div>
              <p class="font-semibold">Kiosk ready</p>
              <p class="text-sm text-[#6B7280]">Use the QR code for faster queue entry.</p>
            </div>
          </div>
        </div>
        <div class="rounded-2xl border border-[#E5E7EB] bg-[#F3F4F6] p-4 text-sm text-[#6B7280]">
          Need help? Visit the barangay help desk for assisted registration.
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
const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const error = ref('')

const onSubmit = async () => {
  error.value = ''
  try {
    const data = await request('/api/auth/resident/register', {
      method: 'POST',
      body: JSON.stringify({
        first_name: firstName.value,
        last_name: lastName.value,
        email: email.value,
        password: password.value,
      }),
    })
    localStorage.setItem('resident_token', data.token)
    localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message
  }
}
</script>
