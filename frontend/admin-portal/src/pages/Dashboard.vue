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

    <div class="mt-10 grid lg:grid-cols-2 gap-6">
      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-[#0B2C6F]">Services</h2>
            <p class="text-sm text-[#6B7280]">Create and manage service types.</p>
          </div>
          <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadServices">
            Refresh
          </button>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
          <input v-model="newService.name" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Service name" />
          <input v-model="newService.code" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Code" />
          <button class="bg-[#F2C300] text-black rounded-xl text-sm font-semibold" @click="createService">
            Add service
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">Name</th>
                <th class="py-2">Code</th>
                <th class="py-2">Active</th>
                <th class="py-2 text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingServices">
                <td colspan="4" class="py-4 text-center text-[#6B7280]">Loading services...</td>
              </tr>
              <tr v-else-if="services.length === 0">
                <td colspan="4" class="py-4 text-center text-[#6B7280]">No services found.</td>
              </tr>
              <tr v-for="service in services" :key="service.id" class="border-b border-[#F3F4F6]">
                <td class="py-2">{{ service.name }}</td>
                <td class="py-2">{{ service.code }}</td>
                <td class="py-2">
                  <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="service.active ? 'bg-[#2E7D32] text-white' : 'bg-[#F3F4F6] text-[#6B7280]'">
                    {{ service.active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="py-2 text-right">
                  <button class="text-xs border border-[#0B2C6F] text-[#0B2C6F] px-3 py-1 rounded-full" @click="toggleService(service)">
                    Toggle
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-if="serviceError" class="mt-3 text-sm text-[#C0392B]">{{ serviceError }}</p>
        </div>
      </section>

      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-[#0B2C6F]">Queue Control</h2>
            <p class="text-sm text-[#6B7280]">Call, serve, or cancel tickets.</p>
          </div>
          <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadQueue">
            Refresh
          </button>
        </div>
        <div class="mt-4 flex flex-wrap gap-3">
          <input v-model="queueServiceId" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm w-32" placeholder="Service ID" />
          <select v-model="queueStatus" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm">
            <option value="">All</option>
            <option value="waiting">Waiting</option>
            <option value="serving">Serving</option>
            <option value="done">Done</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <button class="bg-[#F2C300] text-black rounded-xl px-4 py-2 text-sm font-semibold" @click="callNext">
            Call next
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">Ticket</th>
                <th class="py-2">Service</th>
                <th class="py-2">Status</th>
                <th class="py-2 text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingQueue">
                <td colspan="4" class="py-4 text-center text-[#6B7280]">Loading tickets...</td>
              </tr>
              <tr v-else-if="queueTickets.length === 0">
                <td colspan="4" class="py-4 text-center text-[#6B7280]">No tickets found.</td>
              </tr>
              <tr v-for="ticket in queueTickets" :key="ticket.id" class="border-b border-[#F3F4F6]">
                <td class="py-2">{{ ticket.ticket_no }}</td>
                <td class="py-2">{{ ticket.service_id }}</td>
                <td class="py-2">{{ ticket.status }}</td>
                <td class="py-2 text-right">
                  <button class="text-xs border border-[#2E7D32] text-[#2E7D32] px-3 py-1 rounded-full" @click="serveTicket(ticket)">
                    Serve
                  </button>
                  <button class="ml-2 text-xs border border-[#C0392B] text-[#C0392B] px-3 py-1 rounded-full" @click="cancelTicket(ticket)">
                    Cancel
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-if="queueError" class="mt-3 text-sm text-[#C0392B]">{{ queueError }}</p>
        </div>
      </section>
    </div>

    <div class="mt-10 grid lg:grid-cols-2 gap-6">
      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-[#0B2C6F]">Kiosk Devices</h2>
            <p class="text-sm text-[#6B7280]">Register kiosk devices and tokens.</p>
          </div>
          <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadKiosks">
            Refresh
          </button>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
          <input v-model="newKiosk.device_id" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Device ID" />
          <input v-model="newKiosk.name" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Name" />
          <button class="bg-[#F2C300] text-black rounded-xl text-sm font-semibold" @click="createKiosk">
            Add kiosk
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">Device ID</th>
                <th class="py-2">Name</th>
                <th class="py-2">Token</th>
                <th class="py-2">Last seen</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingKiosks">
                <td colspan="4" class="py-4 text-center text-[#6B7280]">Loading kiosks...</td>
              </tr>
              <tr v-else-if="kiosks.length === 0">
                <td colspan="4" class="py-4 text-center text-[#6B7280]">No kiosks found.</td>
              </tr>
              <tr v-for="kiosk in kiosks" :key="kiosk.id" class="border-b border-[#F3F4F6]">
                <td class="py-2">{{ kiosk.device_id }}</td>
                <td class="py-2">{{ kiosk.name }}</td>
                <td class="py-2 text-xs">{{ kiosk.token }}</td>
                <td class="py-2">{{ kiosk.last_seen_at || '-' }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="kioskError" class="mt-3 text-sm text-[#C0392B]">{{ kioskError }}</p>
        </div>
      </section>

      <section class="bg-white border border-[#E5E7EB] rounded-2xl p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-[#0B2C6F]">Audit Logs</h2>
            <p class="text-sm text-[#6B7280]">Recent system actions.</p>
          </div>
          <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadAuditLogs">
            Refresh
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">When</th>
                <th class="py-2">Action</th>
                <th class="py-2">Actor</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingLogs">
                <td colspan="3" class="py-4 text-center text-[#6B7280]">Loading logs...</td>
              </tr>
              <tr v-else-if="auditLogs.length === 0">
                <td colspan="3" class="py-4 text-center text-[#6B7280]">No logs found.</td>
              </tr>
              <tr v-for="log in auditLogs" :key="log.id" class="border-b border-[#F3F4F6]">
                <td class="py-2">{{ formatDate(log.created_at) }}</td>
                <td class="py-2">{{ log.action }}</td>
                <td class="py-2">{{ log.actor_type }} #{{ log.actor_id }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="auditError" class="mt-3 text-sm text-[#C0392B]">{{ auditError }}</p>
        </div>
      </section>
    </div>

    <div class="mt-10 bg-white border border-[#E5E7EB] rounded-2xl p-6" v-if="isSuperAdmin">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-semibold text-[#0B2C6F]">Admin Users</h2>
          <p class="text-sm text-[#6B7280]">Create and manage staff admins.</p>
        </div>
        <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadAdmins">
          Refresh
        </button>
      </div>
      <div class="mt-4 grid gap-3 sm:grid-cols-4">
        <input v-model="newAdmin.name" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Name" />
        <input v-model="newAdmin.email" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Email" />
        <input v-model="newAdmin.password" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm" placeholder="Password" />
        <select v-model="newAdmin.role" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm">
          <option value="staff_admin">staff_admin</option>
          <option value="super_admin">super_admin</option>
        </select>
        <input v-model="newAdmin.service_ids" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm sm:col-span-2" placeholder="Service IDs (comma)" />
        <button class="bg-[#F2C300] text-black rounded-xl text-sm font-semibold sm:col-span-2" @click="createAdmin">
          Add admin
        </button>
      </div>
      <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="text-left text-[#6B7280]">
            <tr class="border-b border-[#E5E7EB]">
              <th class="py-2">Name</th>
              <th class="py-2">Email</th>
              <th class="py-2">Role</th>
              <th class="py-2">Services</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoadingAdmins">
              <td colspan="4" class="py-4 text-center text-[#6B7280]">Loading admins...</td>
            </tr>
            <tr v-else-if="admins.length === 0">
              <td colspan="4" class="py-4 text-center text-[#6B7280]">No admins found.</td>
            </tr>
            <tr v-for="admin in admins" :key="admin.id" class="border-b border-[#F3F4F6]">
              <td class="py-2">{{ admin.name }}</td>
              <td class="py-2">{{ admin.email }}</td>
              <td class="py-2">{{ admin.role }}</td>
              <td class="py-2 text-xs">{{ admin.service_ids.join(', ') }}</td>
            </tr>
          </tbody>
        </table>
        <p v-if="adminError" class="mt-3 text-sm text-[#C0392B]">{{ adminError }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  getResidents,
  updateResidentStatus,
  getServices,
  createService as apiCreateService,
  updateService as apiUpdateService,
  getAdmins,
  createAdmin as apiCreateAdmin,
  getKiosks,
  createKiosk as apiCreateKiosk,
  getQueue,
  queueNext,
  queueServe,
  queueCancel,
  getAuditLogs,
} from '../adminApi'

const router = useRouter()
const profile = JSON.parse(localStorage.getItem('admin_profile') || 'null')
const isSuperAdmin = computed(() => profile?.role === 'super_admin')
const residents = ref([])
const isLoadingResidents = ref(false)
const residentError = ref('')
const statusFilter = ref('')
const search = ref('')
const services = ref([])
const isLoadingServices = ref(false)
const serviceError = ref('')
const newService = ref({ name: '', code: '' })
const kiosks = ref([])
const isLoadingKiosks = ref(false)
const kioskError = ref('')
const newKiosk = ref({ device_id: '', name: '' })
const queueTickets = ref([])
const isLoadingQueue = ref(false)
const queueError = ref('')
const queueServiceId = ref('')
const queueStatus = ref('')
const auditLogs = ref([])
const isLoadingLogs = ref(false)
const auditError = ref('')
const admins = ref([])
const isLoadingAdmins = ref(false)
const adminError = ref('')
const newAdmin = ref({
  name: '',
  email: '',
  password: '',
  role: 'staff_admin',
  service_ids: '',
})

const logout = () => {
  localStorage.removeItem('admin_token')
  localStorage.removeItem('admin_profile')
  router.push('/login')
}

const loadResidents = async () => {
  residentError.value = ''
  isLoadingResidents.value = true
  try {
    const data = await getResidents({ status: statusFilter.value, search: search.value })
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
    await updateResidentStatus(id, status)
    await loadResidents()
  } catch (err) {
    residentError.value = err.message
  }
}

const loadServices = async () => {
  serviceError.value = ''
  isLoadingServices.value = true
  try {
    const data = await getServices()
    services.value = data.services || []
  } catch (err) {
    serviceError.value = err.message
  } finally {
    isLoadingServices.value = false
  }
}

const createService = async () => {
  serviceError.value = ''
  try {
    const payload = { name: newService.value.name, code: newService.value.code, active: 1 }
    await apiCreateService(payload)
    newService.value = { name: '', code: '' }
    await loadServices()
  } catch (err) {
    serviceError.value = err.message
  }
}

const toggleService = async (service) => {
  serviceError.value = ''
  try {
    await apiUpdateService(service.id, { active: service.active ? 0 : 1 })
    await loadServices()
  } catch (err) {
    serviceError.value = err.message
  }
}

const loadKiosks = async () => {
  kioskError.value = ''
  isLoadingKiosks.value = true
  try {
    const data = await getKiosks()
    kiosks.value = data.kiosks || []
  } catch (err) {
    kioskError.value = err.message
  } finally {
    isLoadingKiosks.value = false
  }
}

const createKiosk = async () => {
  kioskError.value = ''
  try {
    await apiCreateKiosk({ device_id: newKiosk.value.device_id, name: newKiosk.value.name })
    newKiosk.value = { device_id: '', name: '' }
    await loadKiosks()
  } catch (err) {
    kioskError.value = err.message
  }
}

const loadQueue = async () => {
  queueError.value = ''
  isLoadingQueue.value = true
  try {
    const data = await getQueue({ service_id: queueServiceId.value, status: queueStatus.value })
    queueTickets.value = data.tickets || []
  } catch (err) {
    queueError.value = err.message
  } finally {
    isLoadingQueue.value = false
  }
}

const callNext = async () => {
  queueError.value = ''
  try {
    if (!queueServiceId.value) {
      queueError.value = 'Service ID is required'
      return
    }
    await queueNext(parseInt(queueServiceId.value, 10))
    await loadQueue()
  } catch (err) {
    queueError.value = err.message
  }
}

const serveTicket = async (ticket) => {
  queueError.value = ''
  try {
    await queueServe(ticket.id)
    await loadQueue()
  } catch (err) {
    queueError.value = err.message
  }
}

const cancelTicket = async (ticket) => {
  queueError.value = ''
  try {
    await queueCancel(ticket.id)
    await loadQueue()
  } catch (err) {
    queueError.value = err.message
  }
}

const loadAuditLogs = async () => {
  auditError.value = ''
  isLoadingLogs.value = true
  try {
    const data = await getAuditLogs(100)
    auditLogs.value = data.logs || []
  } catch (err) {
    auditError.value = err.message
  } finally {
    isLoadingLogs.value = false
  }
}

const loadAdmins = async () => {
  adminError.value = ''
  isLoadingAdmins.value = true
  try {
    const data = await getAdmins()
    admins.value = data.admins || []
  } catch (err) {
    adminError.value = err.message
  } finally {
    isLoadingAdmins.value = false
  }
}

const createAdmin = async () => {
  adminError.value = ''
  try {
    const serviceIds = newAdmin.value.service_ids
      ? newAdmin.value.service_ids.split(',').map((id) => parseInt(id.trim(), 10)).filter(Boolean)
      : []
    await apiCreateAdmin({
      name: newAdmin.value.name,
      email: newAdmin.value.email,
      password: newAdmin.value.password,
      role: newAdmin.value.role,
      service_ids: serviceIds,
    })
    newAdmin.value = { name: '', email: '', password: '', role: 'staff_admin', service_ids: '' }
    await loadAdmins()
  } catch (err) {
    adminError.value = err.message
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
  loadServices()
  loadKiosks()
  loadQueue()
  loadAuditLogs()
  if (isSuperAdmin.value) {
    loadAdmins()
  }
})
</script>
