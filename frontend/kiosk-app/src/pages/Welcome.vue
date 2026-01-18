<template>
  <div class="min-h-screen flex items-center justify-center px-6 py-16 kiosk-stage">
    <div class="kiosk-landing">
      <div class="kiosk-landing-card kiosk-fade">
        <p class="kiosk-landing-kicker">Barangay San Miguel</p>
        <h1 class="kiosk-landing-title font-hero">Welcome to the Community Kiosk</h1>
        <p class="kiosk-landing-subtitle">
          Scan your resident QR, pick a service, and receive a queue ticket in minutes.
        </p>
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
          <div class="kiosk-landing-reminder">
            <span class="scan-dot"></span>
            Have your QR ready
          </div>
        </div>
      </div>

      <div class="kiosk-landing-steps kiosk-fade kiosk-fade-delay-1">
        <div class="kiosk-landing-step">
          <div class="kiosk-landing-step-icon">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 7V3h4" />
              <path d="M17 3h4v4" />
              <path d="M21 17v4h-4" />
              <path d="M7 21H3v-4" />
              <path d="M7 12h10" />
            </svg>
          </div>
          <div>
            <p class="kiosk-landing-step-title">Step 1: Scan QR</p>
            <p class="kiosk-landing-step-text">Align your resident code inside the frame.</p>
          </div>
        </div>
        <div class="kiosk-landing-step">
          <div class="kiosk-landing-step-icon">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 11l9-7 9 7" />
              <path d="M5 10v10h14V10" />
            </svg>
          </div>
          <div>
            <p class="kiosk-landing-step-title">Step 2: Choose Service</p>
            <p class="kiosk-landing-step-text">Tap the service you need today.</p>
          </div>
        </div>
        <div class="kiosk-landing-step">
          <div class="kiosk-landing-step-icon">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 7h16" />
              <path d="M4 12h16" />
              <path d="M4 17h10" />
            </svg>
          </div>
          <div>
            <p class="kiosk-landing-step-title">Step 3: Get Ticket</p>
            <p class="kiosk-landing-step-text">Receive your queue number instantly.</p>
          </div>
        </div>
        <div class="kiosk-landing-note">
          <strong>Quick reminder</strong>
          <span>No photos are stored. Keep the QR steady for faster validation.</span>
        </div>
      </div>
    </div>
  </div>
</template>
