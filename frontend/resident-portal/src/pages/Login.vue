<template>
  <div class="max-w-md mx-auto py-16 px-6">
    <h1 class="text-3xl font-semibold mb-6">Resident Login</h1>
    <form class="space-y-4" @submit.prevent="onSubmit">
      <input v-model="email" type="email" placeholder="Email" class="w-full border px-3 py-2 rounded" required />
      <input v-model="password" type="password" placeholder="Password" class="w-full border px-3 py-2 rounded" required />
      <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
    </form>
    <p class="mt-4 text-sm">
      No account? <router-link class="text-blue-600" to="/register">Register</router-link>
    </p>
    <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
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
