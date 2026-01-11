<template>
  <div class="max-w-5xl mx-auto py-12 px-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-semibold">Admin Dashboard</h1>
      <button class="text-sm text-blue-600" @click="logout">Logout</button>
    </div>

    <div class="mt-8 grid gap-6">
      <section class="bg-white border rounded p-6">
        <h2 class="text-xl font-semibold">Queue Management</h2>
        <p class="text-sm text-slate-600 mt-2">Placeholder for calling and serving tickets.</p>
      </section>

      <section class="bg-white border rounded p-6">
        <h2 class="text-xl font-semibold">Transactions</h2>
        <p class="text-sm text-slate-600 mt-2">Placeholder for completed service logs.</p>
      </section>

      <section class="bg-white border rounded p-6">
        <h2 class="text-xl font-semibold">Audit Trail</h2>
        <p class="text-sm text-slate-600 mt-2">Basic audit trail placeholder.</p>
      </section>

      <section v-if="isSuperAdmin" class="bg-white border rounded p-6">
        <h2 class="text-xl font-semibold">Account Management</h2>
        <p class="text-sm text-slate-600 mt-2">Super admin-only area for staff accounts.</p>
      </section>

      <section v-if="isSuperAdmin" class="bg-white border rounded p-6">
        <h2 class="text-xl font-semibold">Full Audit Trail</h2>
        <p class="text-sm text-slate-600 mt-2">Expanded audit logs placeholder for super admins.</p>
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
