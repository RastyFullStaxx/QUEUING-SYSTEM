<template>
  <div class="min-h-screen flex flex-col items-center justify-center text-center px-6">
    <div class="w-full max-w-2xl border border-[#E5E7EB] rounded-3xl px-10 py-12 bg-white">
      <div class="mx-auto h-16 w-16 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h18" />
          <path d="M12 3v18" />
        </svg>
      </div>
      <h1 class="text-4xl font-bold mt-6 text-[#0B2C6F]">Welcome to the Kiosk</h1>
      <p class="mt-4 text-lg text-[#6B7280]">Barangay San Miguel Queuing System</p>
      <router-link
        class="mt-10 inline-flex items-center justify-center gap-3 bg-[#F2C300] text-black text-2xl px-10 py-4 rounded-2xl font-semibold"
        to="/scan"
      >
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" />
          <rect x="14" y="3" width="7" height="7" />
          <rect x="3" y="14" width="7" height="7" />
          <path d="M14 14h7v7h-7z" />
        </svg>
        Start Scan
      </router-link>
    </div>
  </div>
</template>
