<template>
  <div class="min-h-screen kiosk-app-shell">
    <div class="kiosk-clouds" aria-hidden="true"></div>
    <div class="kiosk-buildings-back" aria-hidden="true"></div>
    <div class="kiosk-brand">
      <span class="kiosk-brand-sunray" aria-hidden="true"></span>
      <img class="kiosk-brand-logo" src="/logo.png" alt="Barangay logo" />
    </div>
    <div class="relative z-10">
      <router-view />
    </div>
  </div>
</template>
