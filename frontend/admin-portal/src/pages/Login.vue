<template>
  <div class="min-h-screen flex items-center justify-center px-6 py-16">
    <div class="w-full max-w-4xl grid md:grid-cols-2 gap-8 items-center">
      <div class="space-y-6">
        <p class="text-base uppercase tracking-[0.35em] text-[#6B7280]">Admin Control</p>
        <h1 class="text-2xl font-semibold leading-tight text-[#0B2C6F]">
          Operations & Queue
          <span class="block text-[#F2C300]">Command Center</span>
        </h1>
        <p class="text-[#6B7280]">
          Monitor services, manage staff access, and review audit trails from a single panel.
        </p>
        <div class="flex items-center gap-3 text-base text-[#6B7280]">
          <span class="inline-flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-[#2E7D32]"></span>
            Live status
          </span>
          <span class="inline-flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-[#0B2C6F]"></span>
            RBAC enforced
          </span>
        </div>
      </div>
      <div class="bg-white rounded-2xl border border-[#E5E7EB] p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-12 w-12 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 3l9 4.5v11L12 21l-9-4.5v-11L12 3z" />
              <path d="M12 7v5l4 2" />
            </svg>
          </div>
          <div>
            <h2 class="text-xl font-semibold">Admin Login</h2>
            <p class="text-base text-[#6B7280]">Use your staff account.</p>
          </div>
        </div>
        <form class="space-y-4" @submit.prevent="onSubmit">
          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full border border-[#E5E7EB] px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
            required
          />
          <input
            v-model="password"
            type="password"
            placeholder="Password"
            class="w-full border border-[#E5E7EB] px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
            required
          />
          <button class="w-full bg-[#F2C300] text-black py-3 rounded-xl text-base font-semibold">Login</button>
        </form>
        <p v-if="error" class="mt-4 text-red-600 text-base">{{ error }}</p>
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
    const data = await request('/api/auth/admin/login', {
      method: 'POST',
      body: JSON.stringify({ email: email.value, password: password.value }),
    })
    localStorage.setItem('admin_token', data.token)
    localStorage.setItem('admin_profile', JSON.stringify(data.admin))
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message
  }
}
</script>
