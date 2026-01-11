<template>
  <div class="min-h-screen px-6 py-10">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 7h16" />
            <path d="M4 12h16" />
            <path d="M4 17h10" />
          </svg>
        </div>
        <div>
          <h1 class="text-3xl font-bold text-[#0B2C6F]">Select Service</h1>
          <p class="text-[#6B7280]">Tap a service to continue.</p>
        </div>
      </div>
      <div class="mt-8 grid gap-4">
        <button
          v-for="service in services"
          :key="service.id"
          class="w-full text-left bg-white border border-[#E5E7EB] px-6 py-5 rounded-2xl text-xl"
          @click="selectService(service)"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-semibold">{{ service.name }}</div>
              <div class="text-sm text-[#6B7280]">Code: {{ service.code }}</div>
            </div>
            <div class="h-10 w-10 rounded-full bg-[#0B2C6F] text-white flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6" />
              </svg>
            </div>
          </div>
        </button>
      </div>
      <p v-if="error" class="mt-6 text-red-600 text-lg">{{ error }}</p>
    </div>
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
