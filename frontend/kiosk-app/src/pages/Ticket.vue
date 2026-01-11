<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-6 text-center">
    <h1 class="text-4xl font-bold">Ticket Created</h1>
    <div class="mt-8 bg-white border rounded p-8 text-3xl font-mono">
      {{ ticket?.ticket_no || '---' }}
    </div>
    <p class="mt-6 text-lg">Please wait for your number to be called.</p>
    <router-link class="mt-10 text-blue-600" to="/welcome">Back to welcome</router-link>
  </div>
</template>

<script setup>
const ticket = JSON.parse(localStorage.getItem('kiosk_ticket') || 'null')
</script>
