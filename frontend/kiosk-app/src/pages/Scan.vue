<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-6">
    <h1 class="text-3xl font-bold mb-6">Scan Resident QR</h1>
    <input
      ref="inputRef"
      v-model="qrCode"
      class="w-full max-w-md border text-xl px-4 py-3 rounded"
      placeholder="Enter or scan QR code"
      @keyup.enter="onSubmit"
    />
    <button class="mt-6 bg-green-600 text-white text-xl px-6 py-3 rounded" @click="onSubmit">
      Validate
    </button>
    <p v-if="error" class="mt-4 text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const qrCode = ref('')
const error = ref('')
const inputRef = ref(null)

const onSubmit = async () => {
  error.value = ''
  if (!qrCode.value) {
    error.value = 'Please scan a QR code'
    return
  }

  try {
    const data = await request('/api/kiosk/validate-qr', {
      method: 'POST',
      body: JSON.stringify({ qr_code: qrCode.value }),
    })
    localStorage.setItem('kiosk_resident', JSON.stringify(data.resident))
    localStorage.setItem('kiosk_allowed_services', JSON.stringify(data.allowed_services || []))
    localStorage.setItem('kiosk_approved', String(data.approved))

    if (!data.approved) {
      error.value = 'Resident is not approved yet.'
      return
    }

    router.push('/services')
  } catch (err) {
    error.value = err.message
  }
}

onMounted(() => {
  localStorage.removeItem('kiosk_ticket')
  localStorage.removeItem('kiosk_service')
  localStorage.removeItem('kiosk_approved')
  setTimeout(() => inputRef.value?.focus(), 50)
})
</script>
