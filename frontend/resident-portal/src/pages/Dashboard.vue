<template>
  <div class="max-w-6xl mx-auto py-12 px-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-xs uppercase tracking-[0.35em] text-[#6B7280]">Resident</p>
        <h1 class="text-4xl font-semibold text-[#0B2C6F]">Dashboard</h1>
      </div>
      <router-link class="text-sm font-semibold text-slate-900 bg-white/80 px-4 py-2 rounded-full border" to="/profile">
        View profile
      </router-link>
    </div>
    <div class="mt-10 grid lg:grid-cols-[1.4fr,0.6fr] gap-6">
      <section class="bg-white rounded-3xl border border-[#E5E7EB] p-8">
        <div class="flex items-center gap-4">
          <div class="h-12 w-12 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 20c1.8-4 6-6 8-6s6.2 2 8 6" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-[#6B7280]">Welcome back</p>
            <p class="text-2xl font-semibold">{{ residentName }}</p>
          </div>
        </div>
        <div class="mt-6 grid sm:grid-cols-2 gap-4">
          <div class="rounded-2xl border border-[#E5E7EB] bg-[#F3F4F6] p-4">
            <p class="text-xs uppercase tracking-[0.25em] text-[#6B7280]">Status</p>
            <div class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold" :class="statusClass">
              {{ status }}
            </div>
            <p class="mt-2 text-sm text-[#6B7280]">We update this once your ID is checked.</p>
          </div>
          <div class="rounded-2xl border border-[#E5E7EB] bg-[#F3F4F6] p-4">
            <p class="text-xs uppercase tracking-[0.25em] text-[#6B7280]">Kiosk lane</p>
            <p class="mt-2 text-lg font-semibold">Scan-ready</p>
            <p class="text-sm text-[#6B7280]">Keep your QR code handy for faster service.</p>
          </div>
        </div>
        <div v-if="status === 'approved'" class="mt-8">
          <div class="flex items-center justify-between">
            <p class="text-sm text-[#6B7280]">Your QR code</p>
            <span class="text-xs uppercase tracking-[0.2em] text-[#2E7D32]">Approved</span>
          </div>
          <div class="mt-3 p-8 border-2 border-dashed rounded-3xl text-center text-3xl font-mono bg-[#F3F4F6] tracking-widest">
            RESIDENT #{{ resident?.id || '---' }}
          </div>
        </div>
        <div v-else class="mt-8 text-sm text-[#6B7280]">
          Your account is awaiting approval. Once approved, your QR code will appear here.
        </div>
      </section>
      <aside class="space-y-4">
        <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6">
          <h2 class="text-lg font-semibold">Kiosk quick steps</h2>
          <div class="mt-4 space-y-3">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full bg-[#F2C300] text-black flex items-center justify-center font-semibold">1</div>
              <div>
                <p class="font-semibold">Show QR</p>
                <p class="text-sm text-[#6B7280]">Note your resident number.</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full bg-[#F2C300] text-black flex items-center justify-center font-semibold">2</div>
              <div>
                <p class="font-semibold">Select service</p>
                <p class="text-sm text-[#6B7280]">Choose the document type.</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full bg-[#F2C300] text-black flex items-center justify-center font-semibold">3</div>
              <div>
                <p class="font-semibold">Receive ticket</p>
                <p class="text-sm text-[#6B7280]">Wait for your number to be called.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="rounded-3xl bg-[#0B2C6F] text-white p-6">
          <p class="text-xs uppercase tracking-[0.3em] text-white/70">Reminder</p>
          <p class="mt-3 text-lg font-semibold">Bring a valid ID</p>
          <p class="mt-2 text-sm text-white/70">Approval depends on your barangay residency documents.</p>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { request } from '../api'

const resident = ref(null)
const status = ref('pending')

const residentName = computed(() => {
  if (!resident.value) return 'Resident'
  return `${resident.value.first_name} ${resident.value.last_name}`.trim()
})

const statusClass = computed(() => {
  if (status.value === 'approved') return 'bg-[#2E7D32] text-white'
  if (status.value === 'rejected') return 'bg-[#C0392B] text-white'
  return 'bg-[#F2C300] text-black'
})

onMounted(async () => {
  const cached = localStorage.getItem('resident_profile')
  if (cached) {
    resident.value = JSON.parse(cached)
    status.value = resident.value.status
  }

  const token = localStorage.getItem('resident_token')
  if (token) {
    try {
      const data = await request('/api/resident/me', {
        headers: { Authorization: `Bearer ${token}` },
      })
      resident.value = data.resident
      status.value = data.resident.status
      localStorage.setItem('resident_profile', JSON.stringify(data.resident))
    } catch (err) {
      // Ignore refresh errors for now.
    }
  }
})
</script>
