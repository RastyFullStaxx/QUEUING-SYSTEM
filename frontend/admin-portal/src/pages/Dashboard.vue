<template>
  <div class="max-w-6xl mx-auto py-12 px-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-sm uppercase tracking-[0.3em] text-[#6B7280]">Admin</p>
        <h1 class="text-4xl font-semibold text-[#0B2C6F]">Dashboard</h1>
      </div>
      <button class="text-sm font-semibold text-[#0B2C6F] bg-white px-4 py-2 rounded-full border border-[#E5E7EB]" @click="logout">
        Logout
      </button>
    </div>

    <div class="mt-10 grid lg:grid-cols-3 gap-6">
      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12h18" />
              <path d="M3 6h18" />
              <path d="M3 18h10" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold">Queue Management</h2>
        </div>
        <p class="text-sm text-[#6B7280] mt-3">Placeholder for calling and serving tickets.</p>
      </section>

      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v20" />
              <path d="M6 6h12" />
              <path d="M6 18h12" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold">Transactions</h2>
        </div>
        <p class="text-sm text-[#6B7280] mt-3">Placeholder for completed service logs.</p>
      </section>

      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 8v4l3 3" />
              <circle cx="12" cy="12" r="9" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold">Audit Trail</h2>
        </div>
        <p class="text-sm text-[#6B7280] mt-3">Basic audit trail placeholder.</p>
      </section>
    </div>

    <div class="mt-10 bg-white border border-[#E5E7EB] rounded-2xl p-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-[#0B2C6F]">Resident Verification</h2>
          <p class="text-sm text-[#6B7280]">Approve or reject resident registrations.</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <select v-model="statusFilter" class="border border-[#E5E7EB] rounded-full px-4 py-2 text-sm">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
          <input
            v-model="search"
            type="text"
            placeholder="Search name or email"
            class="border border-[#E5E7EB] rounded-full px-4 py-2 text-sm w-64"
          />
          <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadResidents">
            Refresh
          </button>
        </div>
      </div>
      <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm border-collapse">
          <thead class="text-left text-[#6B7280]">
            <tr class="border-b border-[#E5E7EB]">
              <th class="py-2">Name</th>
              <th class="py-2">Email</th>
              <th class="py-2">Status</th>
              <th class="py-2">Registered</th>
              <th class="py-2 text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoadingResidents">
              <td colspan="5" class="py-6 text-center text-[#6B7280]">Loading residents...</td>
            </tr>
            <tr v-else-if="residents.length === 0">
              <td colspan="5" class="py-6 text-center text-[#6B7280]">No residents found.</td>
            </tr>
            <tr v-for="resident in residents" :key="resident.id" class="border-b border-[#F3F4F6]">
              <td class="py-3">{{ resident.first_name }} {{ resident.last_name }}</td>
              <td class="py-3">{{ resident.email }}</td>
              <td class="py-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="statusClass(resident.status)">
                  {{ resident.status }}
                </span>
              </td>
              <td class="py-3">{{ formatDate(resident.created_at) }}</td>
              <td class="py-3 text-right">
                <div class="inline-flex gap-2">
                  <button
                    class="px-3 py-1 rounded-full text-xs border border-[#2E7D32] text-[#2E7D32]"
                    @click="updateStatus(resident.id, 'approved')"
                  >
                    Approve
                  </button>
                  <button
                    class="px-3 py-1 rounded-full text-xs border border-[#C0392B] text-[#C0392B]"
                    @click="updateStatus(resident.id, 'rejected')"
                  >
                    Reject
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-if="residentError" class="mt-4 text-sm text-[#C0392B]">{{ residentError }}</p>
      </div>
    </div>

    <div class="mt-6 grid lg:grid-cols-2 gap-6" v-if="isSuperAdmin">
      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="8.5" cy="7" r="4" />
              <path d="M20 8v6" />
              <path d="M23 11h-6" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold">Account Management</h2>
        </div>
        <p class="text-sm text-[#6B7280] mt-3">Super admin-only area for staff accounts.</p>
      </section>

      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 3v18h18" />
              <path d="M7 15l4-4 4 4 5-7" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold">Full Audit Trail</h2>
        </div>
        <p class="text-sm text-[#6B7280] mt-3">Expanded audit logs placeholder for super admins.</p>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const profile = JSON.parse(localStorage.getItem('admin_profile') || 'null')
const isSuperAdmin = computed(() => profile?.role === 'super_admin')
const residents = ref([])
const isLoadingResidents = ref(false)
const residentError = ref('')
const statusFilter = ref('')
const search = ref('')

const logout = () => {
  localStorage.removeItem('admin_token')
  localStorage.removeItem('admin_profile')
  router.push('/login')
}

const loadResidents = async () => {
  residentError.value = ''
  isLoadingResidents.value = true
  try {
    const token = localStorage.getItem('admin_token')
    const params = new URLSearchParams()
    if (statusFilter.value) params.set('status', statusFilter.value)
    if (search.value) params.set('search', search.value)
    const query = params.toString() ? `?${params.toString()}` : ''
    const data = await request(`/api/admin/residents${query}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    residents.value = data.residents || []
  } catch (err) {
    residentError.value = err.message
  } finally {
    isLoadingResidents.value = false
  }
}

const updateStatus = async (id, status) => {
  residentError.value = ''
  try {
    const token = localStorage.getItem('admin_token')
    await request(`/api/admin/residents/${id}/status`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: JSON.stringify({ status }),
    })
    await loadResidents()
  } catch (err) {
    residentError.value = err.message
  }
}

const statusClass = (status) => {
  if (status === 'approved') return 'bg-[#2E7D32] text-white'
  if (status === 'rejected') return 'bg-[#C0392B] text-white'
  return 'bg-[#F2C300] text-black'
}

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  return date.toLocaleDateString()
}

onMounted(() => {
  loadResidents()
})
</script>
