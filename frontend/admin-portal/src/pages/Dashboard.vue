<template>
  <div class="admin-shell">
    <header class="admin-dock">
      <div class="admin-dock-profile">
        <div class="admin-avatar">{{ adminInitials }}</div>
        <div>
          <p class="admin-name">{{ profile?.name || 'Admin User' }}</p>
          <p class="admin-email">{{ profile?.email || 'admin@barangay.local' }}</p>
        </div>
      </div>
      <div class="admin-dock-divider"></div>
      <nav class="admin-dock-nav">
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'resident-verification' }" href="#resident-verification">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 12l8-7 8 7v8a2 2 0 0 1-2 2h-4v-6H10v6H6a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Resident
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'services' }" href="#services">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 3v5m0 8v5m9-9h-5M8 12H3m14.95-6.95-3.54 3.54m-4.82 4.82-3.54 3.54m0-8.36-3.54-3.54m12.3 12.3-3.54-3.54" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Services
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'queue-control' }" href="#queue-control">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M5 7h14M5 12h10M5 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              <path d="M17 14v6l3-3-3-3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Queue
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'transactions' }" href="#transactions">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 7h16M4 12h12M4 17h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              <path d="M18 14h2v6h-6v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Transactions
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'kiosk-devices' }" href="#kiosk-devices">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <rect x="4" y="3" width="16" height="18" rx="3" stroke="currentColor" stroke-width="1.6" />
              <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Kiosks
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'audit-logs' }" href="#audit-logs">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 3h9l3 3v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6" />
              <path d="M9 10h6M9 14h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Logs
        </a>
        <a v-if="isSuperAdmin" class="admin-nav-item" :class="{ 'is-active': activeSection === 'admin-users' }" href="#admin-users">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="1.6" />
              <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Admins
        </a>
      </nav>
      <div class="admin-dock-divider"></div>
      <button class="admin-logout" type="button" @click="logout">
        Sign out
      </button>
    </header>

    <div class="admin-content">
      <div class="max-w-6xl mx-auto py-12 px-6">
        <div class="admin-intro">
          <div class="admin-intro-left">
            <div class="admin-emblem">
              <span class="admin-emblem-ring"></span>
              <span class="admin-emblem-dot"></span>
              <span class="admin-emblem-dot is-gold"></span>
            </div>
            <h1 class="admin-title">{{ activeSectionTitle }}</h1>
          </div>
          <div class="admin-intro-art" aria-hidden="true">
            <span class="admin-intro-bar is-primary"></span>
            <span class="admin-intro-bar is-gold"></span>
            <span class="admin-intro-bar is-neutral"></span>
          </div>
        </div>

        <div id="resident-verification" class="admin-card mt-10" v-show="activeSection === 'resident-verification'">
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

          <section id="services" class="admin-card mt-10" v-show="activeSection === 'services'">
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

          <section id="queue-control" class="admin-card mt-10" v-show="activeSection === 'queue-control'">
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
        <section id="transactions" class="admin-card mt-10" v-show="activeSection === 'transactions'">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-semibold text-[#0B2C6F]">Transactions</h2>
              <p class="text-sm text-[#6B7280]">Completed and cancelled tickets.</p>
            </div>
            <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-sm" @click="loadTransactions">
              Refresh
            </button>
          </div>
          <div class="mt-4 flex flex-wrap gap-3">
            <input v-model="transactionServiceId" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm w-32" placeholder="Service ID" />
            <select v-model="transactionStatus" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm">
              <option value="done">Done</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="text-left text-[#6B7280]">
                <tr class="border-b border-[#E5E7EB]">
                  <th class="py-2">Ticket</th>
                  <th class="py-2">Service</th>
                  <th class="py-2">Status</th>
                  <th class="py-2">Issued</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoadingTransactions">
                  <td colspan="4" class="py-4 text-center text-[#6B7280]">Loading transactions...</td>
                </tr>
                <tr v-else-if="transactions.length === 0">
                  <td colspan="4" class="py-4 text-center text-[#6B7280]">No transactions found.</td>
                </tr>
                <tr v-for="ticket in transactions" :key="ticket.id" class="border-b border-[#F3F4F6]">
                  <td class="py-2">{{ ticket.ticket_no }}</td>
                  <td class="py-2">{{ ticket.service_id }}</td>
                  <td class="py-2">{{ ticket.status }}</td>
                  <td class="py-2">{{ formatDate(ticket.issued_at) }}</td>
                </tr>
              </tbody>
            </table>
            <p v-if="transactionError" class="mt-3 text-sm text-[#C0392B]">{{ transactionError }}</p>
          </div>
        </section>

          <section id="kiosk-devices" class="admin-card mt-10" v-show="activeSection === 'kiosk-devices'">
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

          <section id="audit-logs" class="admin-card mt-10" v-show="activeSection === 'audit-logs'">
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
        <div id="admin-users" class="admin-card mt-10" v-if="isSuperAdmin && activeSection === 'admin-users'">
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
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
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
const adminInitials = computed(() => {
  if (!profile?.name) return 'AD'
  return profile.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0].toUpperCase())
    .join('')
})
const activeSection = ref('resident-verification')
const sectionTitles = {
  'resident-verification': 'Resident Verification',
  services: 'Services',
  'queue-control': 'Queue Control',
  transactions: 'Transactions',
  'kiosk-devices': 'Kiosk Devices',
  'audit-logs': 'Audit Logs',
  'admin-users': 'Admin Users',
}
const activeSectionTitle = computed(() => {
  if (activeSection.value === 'admin-users' && !isSuperAdmin.value) {
    return sectionTitles['resident-verification']
  }
  return sectionTitles[activeSection.value] || sectionTitles['resident-verification']
})
let dockObserver = null
let hashListener = null
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
const transactions = ref([])
const isLoadingTransactions = ref(false)
const transactionError = ref('')
const transactionServiceId = ref('')
const transactionStatus = ref('done')
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

const loadTransactions = async () => {
  transactionError.value = ''
  isLoadingTransactions.value = true
  try {
    const data = await getQueue({
      service_id: transactionServiceId.value,
      status: transactionStatus.value,
    })
    transactions.value = data.tickets || []
  } catch (err) {
    transactionError.value = err.message
  } finally {
    isLoadingTransactions.value = false
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
  const sectionIds = ['resident-verification', 'services', 'queue-control', 'transactions', 'kiosk-devices', 'audit-logs', 'admin-users']
  const sections = sectionIds
    .map((id) => document.getElementById(id))
    .filter(Boolean)
  hashListener = () => {
    if (window.location.hash) {
      activeSection.value = window.location.hash.replace('#', '')
    }
  }
  hashListener()
  window.addEventListener('hashchange', hashListener)
  dockObserver = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)
      if (visible[0]) {
        activeSection.value = visible[0].target.id
      }
    },
    { threshold: [0.2, 0.4, 0.6] }
  )
  sections.forEach((section) => dockObserver.observe(section))

  loadResidents()
  loadServices()
  loadKiosks()
  loadQueue()
  loadTransactions()
  loadAuditLogs()
  if (isSuperAdmin.value) {
    loadAdmins()
  }
})

onBeforeUnmount(() => {
  if (dockObserver) {
    dockObserver.disconnect()
  }
  if (hashListener) {
    window.removeEventListener('hashchange', hashListener)
  }
})
</script>

<style scoped>
.admin-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  padding: 2.5rem 2rem 3.5rem;
  background: #f3f4f6;
}

.admin-dock {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1rem 1.5rem;
  border-radius: 28px;
  background: #0b2c6f;
  border: 1px solid rgba(255, 255, 255, 0.12);
  position: sticky;
  top: 1.5rem;
  z-index: 10;
}

.admin-dock-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.admin-avatar {
  height: 44px;
  width: 44px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  font-weight: 700;
  letter-spacing: 0.08em;
  background: #f2c300;
  color: #0b2c6f;
}

.admin-name {
  font-size: 1.05rem;
  font-weight: 600;
  color: #f8f7ff;
}

.admin-email {
  margin-top: 0.15rem;
  font-size: 0.85rem;
  color: rgba(226, 232, 240, 0.8);
}

.admin-dock-divider {
  height: 38px;
  width: 1px;
  background: rgba(255, 255, 255, 0.18);
}

.admin-dock-nav {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex: 1;
  flex-wrap: wrap;
}

.admin-nav-item {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.65rem 1rem;
  border-radius: 999px;
  font-size: 0.98rem;
  color: rgba(255, 255, 255, 0.86);
  text-decoration: none;
  transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
}

.admin-nav-item:hover {
  transform: translateX(2px);
  background: rgba(242, 195, 0, 0.2);
  color: #ffffff;
}

.admin-nav-item.is-active {
  background: #f2c300;
  color: #0b2c6f;
}

.admin-nav-icon {
  height: 28px;
  width: 28px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.16);
  color: inherit;
}

.admin-nav-icon svg {
  width: 16px;
  height: 16px;
}

.admin-logout {
  border: none;
  border-radius: 999px;
  padding: 0.65rem 1.1rem;
  font-size: 0.92rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  background: #f2c300;
  color: #0b2c6f;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.2s ease;
}

.admin-logout:hover {
  transform: translateY(-1px);
  background: #f7cf1a;
}

.admin-content {
  flex: 1;
  min-width: 0;
  font-size: 1.02rem;
}

.admin-intro {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  padding: 1.75rem 2rem;
  border-radius: 24px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  position: relative;
  overflow: hidden;
}

.admin-intro::before {
  content: '';
  position: absolute;
  right: -70px;
  top: -90px;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.18);
  pointer-events: none;
}

.admin-intro-left {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  position: relative;
  z-index: 1;
}

.admin-emblem {
  position: relative;
  height: 64px;
  width: 64px;
  border-radius: 20px;
  background: #0b2c6f;
  display: grid;
  place-items: center;
}

.admin-emblem-ring {
  position: absolute;
  height: 40px;
  width: 40px;
  border-radius: 50%;
  border: 2px solid #f2c300;
}

.admin-emblem-dot {
  height: 10px;
  width: 10px;
  border-radius: 50%;
  background: #ffffff;
}

.admin-emblem-dot.is-gold {
  position: absolute;
  height: 6px;
  width: 6px;
  background: #f2c300;
  top: 12px;
  right: 12px;
}

.admin-title {
  font-size: 2.35rem;
  font-weight: 700;
  color: #0b2c6f;
  letter-spacing: 0.02em;
}

.admin-intro-art {
  display: flex;
  gap: 0.6rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

.admin-intro-bar {
  width: 56px;
  height: 10px;
  border-radius: 999px;
  background: #e5e7eb;
}

.admin-intro-bar.is-primary {
  width: 80px;
  background: #0b2c6f;
}

.admin-intro-bar.is-gold {
  background: #f2c300;
}

.admin-intro-bar.is-neutral {
  width: 40px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
}

.admin-card {
  background: #ffffff;
  border-radius: 26px;
  padding: 1.8rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
}

@media (max-width: 960px) {
  .admin-shell {
    padding: 2rem 1.25rem 3rem;
  }

  .admin-dock {
    flex-direction: column;
    align-items: flex-start;
  }

  .admin-dock-nav {
    width: 100%;
  }

  .admin-intro {
    align-items: flex-start;
  }
}

@media (max-width: 640px) {
  .admin-dock {
    padding: 1rem;
  }

  .admin-nav-item {
    padding: 0.45rem 0.7rem;
    font-size: 0.82rem;
  }
}
</style>
