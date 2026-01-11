<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-6 text-center">
    <div class="bg-white border border-[#E5E7EB] rounded-3xl px-10 py-12 max-w-2xl w-full">
      <div class="mx-auto h-16 w-16 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16" />
          <path d="M4 12h16" />
          <path d="M4 17h10" />
        </svg>
      </div>
      <h1 class="text-4xl font-bold mt-6 text-[#0B2C6F]">Ticket Created</h1>
      <div class="mt-8 bg-[#0B2C6F] text-white rounded-2xl p-8 text-4xl font-mono tracking-widest">
        {{ ticket?.ticket_no || '---' }}
      </div>
      <p class="mt-6 text-lg text-[#6B7280]">Please wait for your number to be called.</p>
      <router-link class="mt-8 inline-flex items-center gap-2 text-[#0B2C6F] font-semibold" to="/welcome">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M15 18l-6-6 6-6" />
        </svg>
        Back to welcome
      </router-link>
    </div>
  </div>
</template>

<script setup>
const ticket = JSON.parse(localStorage.getItem('kiosk_ticket') || 'null')
</script>
