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
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const profile = JSON.parse(localStorage.getItem('admin_profile') || 'null')
const isSuperAdmin = computed(() => profile?.role === 'super_admin')

const logout = () => {
  localStorage.removeItem('admin_token')
  localStorage.removeItem('admin_profile')
  router.push('/login')
}
</script>
