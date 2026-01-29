<template>
  <div class="profile-shell">
    <div class="profile-sunray" aria-hidden="true"></div>
    <div class="profile-buildings" aria-hidden="true"></div>
    <div class="profile-content">
      <header class="profile-header">
        <div class="profile-title">
          <p class="profile-kicker">Resident profile</p>
          <h1 class="profile-heading">Edit profile</h1>
          <p class="profile-subtitle">Keep your personal details accurate for faster barangay services.</p>
        </div>
        <div class="profile-actions">
          <router-link class="ghost-button" to="/dashboard">
            Back to dashboard
          </router-link>
          <button v-if="!isEditing" class="primary-button" type="button" @click="enableEdit">
            Edit profile
          </button>
          <button v-if="isEditing" class="accent-button" type="button" @click="saveProfile">
            Save changes
          </button>
          <button v-if="isEditing" class="ghost-button" type="button" @click="cancelEdit">
            Cancel
          </button>
        </div>
      </header>

      <div class="profile-grid">
        <section class="profile-card" :class="`tone-${resident?.status || 'pending'}`">
          <span class="card-orbs" aria-hidden="true"></span>
          <span class="card-corner" aria-hidden="true"></span>
          <div class="profile-card-header">
            <div class="profile-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 20c1.8-4 6-6 8-6s6.2 2 8 6" />
              </svg>
            </div>
            <div>
              <p class="profile-card-kicker">Resident card</p>
              <h2 class="profile-name">{{ fullName || 'Resident Name' }}</h2>
            </div>
            <span class="status-pill" :class="`status-${resident?.status || 'pending'}`">
              {{ statusLabel }}
            </span>
          </div>

          <div class="profile-form card-reveal">
            <div class="field">
              <label>First name</label>
              <input v-model="form.first_name" :readonly="!isEditing" class="profile-input" :class="inputClass" type="text" />
            </div>
            <div class="field">
              <label>Last name</label>
              <input v-model="form.last_name" :readonly="!isEditing" class="profile-input" :class="inputClass" type="text" />
            </div>
            <div class="field">
              <label>Email</label>
              <input v-model="form.email" :readonly="!isEditing" class="profile-input" :class="inputClass" type="email" />
            </div>
          </div>

          <p v-if="saveMessage" class="status-message success">{{ saveMessage }}</p>
          <p v-if="errorMessage" class="status-message error">{{ errorMessage }}</p>
        </section>

        <section class="profile-card profile-verify">
          <span class="card-orbs" aria-hidden="true"></span>
          <span class="card-corner" aria-hidden="true"></span>
          <h2 class="profile-section-title">Verification</h2>
          <p class="profile-section-subtitle">Upload a valid ID to complete verification.</p>
          <div class="upload-box card-reveal">
            <div class="upload-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 16V6" stroke-linecap="round" />
                <path d="M8 10l4-4 4 4" stroke-linecap="round" stroke-linejoin="round" />
                <rect x="4" y="16" width="16" height="4" rx="2" />
              </svg>
            </div>
            <div>
              <p class="upload-title">Drop your ID file here</p>
              <p class="upload-subtitle">PNG, JPG, or PDF up to 10MB.</p>
            </div>
          </div>
        </section>
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

const inputClass = computed(() => (!isEditing.value ? 'is-readonly' : ''))

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
