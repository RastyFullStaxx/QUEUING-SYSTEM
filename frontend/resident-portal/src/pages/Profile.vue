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
            <div class="profile-avatar" aria-hidden="true">
              <img class="profile-photo" :src="profilePhotoUrl" alt="Resident photo" />
            </div>
            <div>
              <p class="profile-card-kicker">Resident card</p>
              <h2 class="profile-name">{{ fullName || 'Resident Name' }}</h2>
            </div>
            <span class="status-pill" :class="`status-${resident?.status || 'pending'}`">
              {{ statusLabel }}
            </span>
          </div>

          <div class="profile-summary">
            <div class="summary-item">
              <span class="summary-label">Resident ID</span>
              <span class="summary-value">{{ residentIdDisplay }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Username</span>
              <span class="summary-value">{{ form.username || 'Not provided' }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Date of birth</span>
              <span class="summary-value">{{ form.date_of_birth || 'Not provided' }}</span>
            </div>
          </div>

          <div class="profile-form card-reveal">
            <section class="profile-section">
              <h3 class="profile-section-title">Profile photo</h3>
              <p class="profile-section-subtitle">This will appear on your Resident ID.</p>
              <div class="profile-photo-row">
                <img class="profile-photo-preview" :src="profilePhotoUrl" alt="Profile preview" />
                <div class="profile-photo-actions">
                  <input
                    class="profile-photo-input"
                    type="file"
                    accept="image/*"
                    :disabled="!isEditing"
                    @change="onProfilePhotoChange"
                  />
                  <p class="profile-photo-note">PNG or JPG up to 5MB.</p>
                </div>
              </div>
            </section>
            <section class="profile-section">
              <h3 class="profile-section-title">Account details</h3>
              <p class="profile-section-subtitle">Keep your login credentials up to date.</p>
              <div class="profile-grid-fields">
                <div class="field">
                  <label>Username</label>
                  <input v-model="form.username" :readonly="!isEditing" class="profile-input" :class="inputClass" type="text" />
                </div>
                <div class="field">
                  <label>Email</label>
                  <input v-model="form.email" :readonly="!isEditing" class="profile-input" :class="inputClass" type="email" />
                </div>
              </div>
            </section>

            <section class="profile-section">
              <h3 class="profile-section-title">Personal information</h3>
              <p class="profile-section-subtitle">Match the details on your government ID.</p>
              <div class="profile-grid-fields">
                <div class="field">
                  <label>First name</label>
                  <input v-model="form.first_name" :readonly="!isEditing" class="profile-input" :class="inputClass" type="text" />
                </div>
                <div class="field">
                  <label>Middle name</label>
                  <input v-model="form.middle_name" :readonly="!isEditing" class="profile-input" :class="inputClass" type="text" />
                </div>
                <div class="field">
                  <label>Last name</label>
                  <input v-model="form.last_name" :readonly="!isEditing" class="profile-input" :class="inputClass" type="text" />
                </div>
              </div>
              <div class="profile-grid-fields">
                <div class="field">
                  <label>Date of birth</label>
                  <input v-model="form.date_of_birth" :readonly="!isEditing" class="profile-input" :class="inputClass" type="date" />
                </div>
                <div class="field">
                  <label>Gender</label>
                  <select v-model="form.gender" class="profile-input" :class="inputClass" :disabled="!isEditing">
                    <option value="" disabled>Select gender</option>
                    <option v-for="option in genderOptions" :key="option" :value="option">{{ option }}</option>
                  </select>
                </div>
                <div class="field">
                  <label>Civil status</label>
                  <select v-model="form.civil_status" class="profile-input" :class="inputClass" :disabled="!isEditing">
                    <option value="" disabled>Select status</option>
                    <option v-for="option in civilStatusOptions" :key="option" :value="option">{{ option }}</option>
                  </select>
                </div>
              </div>
            </section>

            <section class="profile-section">
              <h3 class="profile-section-title">Contact & address</h3>
              <p class="profile-section-subtitle">We use this for verification updates.</p>
              <div class="profile-grid-fields">
                <div class="field">
                  <label>Mobile number</label>
                  <input v-model="form.mobile_number" :readonly="!isEditing" class="profile-input" :class="inputClass" type="tel" />
                </div>
              </div>
              <div class="field">
                <label>Barangay address</label>
                <textarea v-model="form.address" :readonly="!isEditing" class="profile-input" :class="inputClass" rows="3"></textarea>
              </div>
            </section>
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
            <div v-if="form.valid_id_preview" class="upload-preview">
              <img
                v-if="form.valid_id_type?.startsWith('image/')"
                :src="form.valid_id_preview"
                alt="Valid ID preview"
              />
              <div v-else class="upload-file-chip">PDF uploaded</div>
            </div>
            <div v-else class="upload-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 16V6" stroke-linecap="round" />
                <path d="M8 10l4-4 4 4" stroke-linecap="round" stroke-linejoin="round" />
                <rect x="4" y="16" width="16" height="4" rx="2" />
              </svg>
            </div>
            <div>
              <p class="upload-title">{{ form.valid_id_preview ? 'Replace ID file' : 'Upload a new ID' }}</p>
              <p class="upload-subtitle">{{ validIdName }}</p>
            </div>
            <input
              class="upload-input"
              type="file"
              accept="image/*,.pdf"
              :disabled="!isEditing"
              @change="onValidIdChange"
            />
          </div>
          <p class="upload-note">If your details change, upload a new ID for re-verification.</p>
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
const validIdFile = ref(null)
const profilePhotoFile = ref(null)
const form = ref({
  profile_photo_url: '',
  valid_id_preview: '',
  valid_id_type: '',
  valid_id_name: '',
  username: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  date_of_birth: '',
  gender: '',
  civil_status: '',
  mobile_number: '',
  address: '',
  email: '',
})

const inputClass = computed(() => (!isEditing.value ? 'is-readonly' : ''))
const profilePhotoUrl = computed(() => form.value.profile_photo_url || resident.value?.profile_photo_url || '/favicon.png')
const validIdName = computed(() => {
  return validIdFile.value?.name || form.value.valid_id_name || resident.value?.valid_id_name || 'No file selected yet.'
})
const genderOptions = ['Male', 'Female', 'Non-binary', 'Prefer not to say']
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Widowed']

const fullName = computed(() => {
  if (!resident.value) return ''
  return `${resident.value.first_name || ''} ${resident.value.middle_name || ''} ${resident.value.last_name || ''}`.trim()
})

const statusLabel = computed(() => {
  const value = resident.value?.status || 'pending'
  if (value === 'approved') return 'Approved'
  if (value === 'rejected') return 'Rejected'
  return 'Pending'
})

const residentIdDisplay = computed(() => {
  const id = resident.value?.id
  if (!id && id !== 0) return 'Pending approval'
  return `BSM-RES-${String(id).padStart(6, '0')}`
})

const syncForm = (data) => {
  form.value = {
    profile_photo_url: data?.profile_photo_url || '',
    valid_id_preview: data?.valid_id_preview || '',
    valid_id_type: data?.valid_id_type || '',
    valid_id_name: data?.valid_id_name || '',
    username: data?.username || '',
    first_name: data?.first_name || '',
    middle_name: data?.middle_name || '',
    last_name: data?.last_name || '',
    date_of_birth: data?.date_of_birth || '',
    gender: data?.gender || '',
    civil_status: data?.civil_status || '',
    mobile_number: data?.mobile_number || '',
    address: data?.address || '',
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
    resident.value = { ...resident.value, ...data.resident }
    syncForm(resident.value)
    localStorage.setItem('resident_profile', JSON.stringify(resident.value))
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
  validIdFile.value = null
  profilePhotoFile.value = null
  if (resident.value) {
    syncForm(resident.value)
  }
}

const readFileAsDataUrl = (file) => new Promise((resolve, reject) => {
  const reader = new FileReader()
  reader.onload = () => resolve(reader.result)
  reader.onerror = reject
  reader.readAsDataURL(file)
})

const saveProfile = () => {
  if (!resident.value) return
  const updated = {
    ...resident.value,
    profile_photo_url: form.value.profile_photo_url,
    valid_id_preview: form.value.valid_id_preview,
    valid_id_type: form.value.valid_id_type,
    valid_id_name: form.value.valid_id_name,
    username: form.value.username,
    first_name: form.value.first_name,
    middle_name: form.value.middle_name,
    last_name: form.value.last_name,
    date_of_birth: form.value.date_of_birth,
    gender: form.value.gender,
    civil_status: form.value.civil_status,
    mobile_number: form.value.mobile_number,
    address: form.value.address,
    email: form.value.email,
  }
  if (validIdFile.value) {
    updated.valid_id_name = validIdFile.value.name
  }
  if (profilePhotoFile.value && form.value.profile_photo_url) {
    updated.profile_photo_url = form.value.profile_photo_url
  }
  resident.value = updated
  localStorage.setItem('resident_profile', JSON.stringify(updated))
  isEditing.value = false
  validIdFile.value = null
  profilePhotoFile.value = null
  saveMessage.value = 'Profile updated locally.'
}

const onValidIdChange = (event) => {
  const file = event.target.files?.[0] || null
  validIdFile.value = file
  if (!file) return
  readFileAsDataUrl(file)
    .then((result) => {
      form.value.valid_id_preview = result
      form.value.valid_id_type = file.type || ''
      form.value.valid_id_name = file.name
    })
    .catch(() => {
      errorMessage.value = 'Unable to read the uploaded ID.'
    })
}

const onProfilePhotoChange = (event) => {
  const file = event.target.files?.[0] || null
  profilePhotoFile.value = file
  if (!file) return
  readFileAsDataUrl(file)
    .then((result) => {
      form.value.profile_photo_url = result
    })
    .catch(() => {
      errorMessage.value = 'Unable to read the profile photo.'
    })
}

onMounted(() => {
  hydrateFromCache()
  refreshProfile()
})
</script>
