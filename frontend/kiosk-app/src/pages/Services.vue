<template>
  <div class="min-h-screen px-6 py-10">
    <h1 class="text-3xl font-bold">Select Service</h1>
    <p class="mt-2 text-slate-600">Tap a service to continue.</p>
    <div class="mt-8 grid gap-4">
      <button
        v-for="service in services"
        :key="service.id"
        class="w-full text-left bg-white border px-5 py-4 rounded text-xl"
        @click="selectService(service)"
      >
        <div class="font-semibold">{{ service.name }}</div>
        <div class="text-sm text-slate-500">Code: {{ service.code }}</div>
      </button>
    </div>
    <p v-if="error" class="mt-6 text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const services = ref([])
const error = ref('')

const loadServices = async () => {
  try {
    const data = await request('/api/services')
    services.value = data.services || []
  } catch (err) {
    error.value = err.message
  }
}

const selectService = (service) => {
  localStorage.setItem('kiosk_service', JSON.stringify(service))
  router.push('/confirm')
}

onMounted(() => {
  loadServices()
})
</script>
