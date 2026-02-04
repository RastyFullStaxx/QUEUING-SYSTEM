<template>
  <div class="min-h-screen px-6 py-10 flex items-center justify-center">
    <div class="max-w-2xl w-full bg-white border border-[#E5E7EB] rounded-3xl p-10">
      <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
          <h1 class="text-3xl font-bold text-[#0B2C6F]">Confirm Details</h1>
          <p class="text-[#6B7280]">Please review before printing a ticket.</p>
        </div>
      </div>
      <div class="mt-8 bg-[#F3F4F6] border border-[#E5E7EB] rounded-2xl p-6 text-lg space-y-3">
        <p><span class="font-semibold">Resident:</span> {{ residentName }}</p>
        <p><span class="font-semibold">Service:</span> {{ serviceLabel }}</p>
      </div>
      <button
        class="mt-8 w-full bg-[#F2C300] text-black text-2xl py-4 rounded-2xl font-semibold"
        @click="createTicket"
      >
        Create Ticket
      </button>
      <p v-if="error" class="mt-4 text-red-600 text-lg">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { kioskPayload, request } from '../api'

const router = useRouter()
const error = ref('')
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')
const service = JSON.parse(localStorage.getItem('kiosk_service') || 'null')
const storedServices = JSON.parse(localStorage.getItem('kiosk_services') || 'null')
const sessionId = localStorage.getItem('kiosk_session_id') || ''
const selectedServices = Array.isArray(storedServices) && storedServices.length
  ? storedServices
  : service
    ? [service]
    : []

const residentName = computed(() => {
  if (!resident) return 'Unknown'
  return `${resident.first_name} ${resident.last_name}`.trim()
})

const serviceLabel = computed(() => {
  if (!selectedServices.length) return '—'
  if (selectedServices.length === 1) return selectedServices[0]?.name || 'Service'
  return `${selectedServices.length} services selected`
})

const createTicket = async () => {
  error.value = ''
  if (!resident || !selectedServices.length) {
    error.value = 'Missing resident or service information.'
    return
  }

  try {
    const primaryService = service || selectedServices[0]
    const idempotencyKey = crypto.randomUUID?.() || `${Date.now()}-${resident.id}-${primaryService.id}`
    const data = await request('/api/kiosk/tickets', {
      method: 'POST',
      body: JSON.stringify({
        resident_id: resident.id,
        service_id: primaryService.id,
        service_ids: selectedServices.map((item) => item.id),
        ...kioskPayload(),
        idempotency_key: idempotencyKey,
        session_id: sessionId,
      }),
    })
    localStorage.setItem('kiosk_ticket', JSON.stringify(data.ticket))
    router.push('/ticket')
  } catch (err) {
    error.value = err.message
  }
}
</script>
