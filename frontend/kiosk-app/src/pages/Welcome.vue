<template>
  <div class="min-h-screen flex items-center justify-center px-6 py-16 kiosk-stage">
    <div class="kiosk-landing">
      <div class="kiosk-landing-shell kiosk-fade">
        <div class="kiosk-landing-card">
        <p class="kiosk-landing-kicker">Barangay San Miguel</p>
        <h1 class="kiosk-landing-title font-hero">
          Welcome to the
          <span>Community Kiosk</span>
        </h1>
        <div class="kiosk-landing-actions">
          <router-link
            class="kiosk-button text-lg py-4 px-10 rounded-2xl font-semibold inline-flex items-center gap-3 kiosk-action"
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
      </div>
    </div>
  </div>
</template>
