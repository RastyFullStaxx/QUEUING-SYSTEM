<template>
  <div class="min-h-screen">
    <div class="mx-auto max-w-5xl px-6 py-16 grid lg:grid-cols-[1fr,1fr] gap-12 items-center">
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
