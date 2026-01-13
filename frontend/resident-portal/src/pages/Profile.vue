<template>
  <div class="w-full py-12 px-10">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-xs uppercase tracking-[0.35em] text-[#6B7280]">Resident</p>
        <h1 class="text-3xl font-semibold text-[#0B2C6F]">Profile</h1>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <router-link class="text-sm font-semibold text-slate-900 bg-white/80 px-4 py-2 rounded-full border" to="/dashboard">
          Back to dashboard
        </router-link>
        <button
          v-if="!isEditing"
          class="text-sm font-semibold text-white bg-[#0B2C6F] px-4 py-2 rounded-full"
          type="button"
          @click="enableEdit"
        >
          Edit profile
        </button>
        <button
          v-if="isEditing"
          class="text-sm font-semibold text-black bg-[#F2C300] px-4 py-2 rounded-full"
          type="button"
          @click="saveProfile"
        >
          Save changes
        </button>
        <button
          v-if="isEditing"
          class="text-sm font-semibold text-[#0B2C6F] bg-white/80 px-4 py-2 rounded-full border"
          type="button"
          @click="cancelEdit"
        >
          Cancel
        </button>
      </div>
    </div>

    <div class="mt-10 grid lg:grid-cols-[0.9fr,1.1fr] gap-6">
      <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6 space-y-6">
        <div class="flex items-center gap-4">
          <div class="h-12 w-12 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 20c1.8-4 6-6 8-6s6.2 2 8 6" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-[#6B7280]">Resident card</p>
            <p class="text-xl font-semibold">{{ fullName || 'Resident Name' }}</p>
          </div>
        </div>
        <div class="rounded-2xl bg-[#F3F4F6] border border-[#E5E7EB] p-4 text-sm text-[#6B7280] flex items-center justify-between">
          <span>Status</span>
          <span class="font-semibold text-[#F2C300]">{{ statusLabel }}</span>
        </div>
        <div class="space-y-4 text-sm text-[#0B2C6F]">
          <div>
            <label class="text-xs uppercase tracking-[0.2em] text-[#6B7280] font-semibold">First name</label>
            <input
              v-model="form.first_name"
              :readonly="!isEditing"
              class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl"
              :class="inputClass"
              type="text"
            />
          </div>
          <div>
            <label class="text-xs uppercase tracking-[0.2em] text-[#6B7280] font-semibold">Last name</label>
            <input
              v-model="form.last_name"
              :readonly="!isEditing"
              class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl"
              :class="inputClass"
              type="text"
            />
          </div>
          <div>
            <label class="text-xs uppercase tracking-[0.2em] text-[#6B7280] font-semibold">Email</label>
            <input
              v-model="form.email"
              :readonly="!isEditing"
              class="mt-2 w-full border border-[#E5E7EB] px-4 py-3 rounded-2xl"
              :class="inputClass"
              type="email"
            />
          </div>
        </div>
        <p v-if="saveMessage" class="text-sm text-[#2E7D32] font-semibold">{{ saveMessage }}</p>
        <p v-if="errorMessage" class="text-sm text-[#C0392B] font-semibold">{{ errorMessage }}</p>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6">
          <h2 class="text-lg font-semibold">Verification</h2>
          <p class="mt-2 text-sm text-[#6B7280]">Upload a valid ID to complete verification.</p>
          <div class="mt-6 border-2 border-dashed rounded-2xl p-6 text-center text-sm text-[#6B7280]">
            Drop your ID file here or click to upload.
          </div>
        </div>
        <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6">
          <h2 class="text-lg font-semibold">Barangay services</h2>
          <p class="mt-2 text-sm text-[#6B7280]">Choose services after approval to generate your queue ticket.</p>
          <div class="mt-4 flex flex-wrap gap-3">
            <span class="px-3 py-1 rounded-full text-xs bg-[#F3F4F6] text-[#0B2C6F] border border-[#E5E7EB]">Clearance</span>
            <span class="px-3 py-1 rounded-full text-xs bg-[#F3F4F6] text-[#0B2C6F] border border-[#E5E7EB]">Business Permit</span>
            <span class="px-3 py-1 rounded-full text-xs bg-[#F3F4F6] text-[#0B2C6F] border border-[#E5E7EB]">Residency</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { request } from '../api'

const resident = ref(null)
const isEditing = ref(false)
const saveMessage = ref('')
const errorMessage = ref('')
const form = ref({
  first_name: '',
  last_name: '',
  email: '',
})

const inputClass = computed(() =>
  !isEditing.value ? 'bg-[#F3F4F6] text-[#6B7280] cursor-not-allowed' : 'bg-white'
)

const fullName = computed(() => {
  if (!resident.value) return ''
  return `${resident.value.first_name || ''} ${resident.value.last_name || ''}`.trim()
})

const statusLabel = computed(() => {
  const value = resident.value?.status || 'pending'
  if (value === 'approved') return 'Approved'
  if (value === 'rejected') return 'Rejected'
  return 'Pending'
})

const syncForm = (data) => {
  form.value = {
    first_name: data?.first_name || '',
    last_name: data?.last_name || '',
    email: data?.email || '',
  }
}

const hydrateFromCache = () => {
  const cached = localStorage.getItem('resident_profile')
  if (!cached) return
  try {
    const data = JSON.parse(cached)
    if (data) {
      resident.value = data
      syncForm(data)
    }
  } catch (err) {
    return
  }
}

const refreshProfile = async () => {
  const token = localStorage.getItem('resident_token')
  if (!token) return
  try {
    const data = await request('/api/resident/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
    resident.value = data.resident
    syncForm(data.resident)
    localStorage.setItem('resident_profile', JSON.stringify(data.resident))
  } catch (err) {
    errorMessage.value = err?.message || 'Unable to refresh profile.'
  }
}

const enableEdit = () => {
  saveMessage.value = ''
  errorMessage.value = ''
  isEditing.value = true
}

const cancelEdit = () => {
  isEditing.value = false
  if (resident.value) {
    syncForm(resident.value)
  }
}

const saveProfile = () => {
  if (!resident.value) return
  const updated = {
    ...resident.value,
    first_name: form.value.first_name,
    last_name: form.value.last_name,
    email: form.value.email,
  }
  resident.value = updated
  localStorage.setItem('resident_profile', JSON.stringify(updated))
  isEditing.value = false
  saveMessage.value = 'Profile updated locally.'
}

onMounted(() => {
  hydrateFromCache()
  refreshProfile()
})
</script>
