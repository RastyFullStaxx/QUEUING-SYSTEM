<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-6">
    <div class="max-w-2xl w-full bg-white border border-[#E5E7EB] rounded-3xl px-10 py-12">
      <div class="flex items-center gap-4">
        <div class="h-14 w-14 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 7V3h4" />
            <path d="M17 3h4v4" />
            <path d="M21 17v4h-4" />
            <path d="M7 21H3v-4" />
            <path d="M7 12h10" />
          </svg>
        </div>
        <div>
          <h1 class="text-3xl font-bold text-[#0B2C6F]">Scan Resident QR</h1>
          <p class="text-[#6B7280]">Place the QR code in front of the scanner.</p>
        </div>
      </div>
      <div class="mt-8">
        <input
          ref="inputRef"
          v-model="qrCode"
          class="w-full border-2 border-dashed text-2xl px-5 py-4 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]"
          placeholder="Enter or scan QR code"
          @keyup.enter="onSubmit"
        />
        <button class="mt-6 w-full bg-[#F2C300] text-black text-2xl py-4 rounded-2xl font-semibold" @click="onSubmit">
          Validate QR Code
        </button>
        <p v-if="error" class="mt-4 text-red-600 text-lg">{{ error }}</p>
      </div>
    </div>
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
