<template>
  <div class="min-h-screen kiosk-scan">
    <div class="relative z-10 min-h-screen flex items-center justify-center px-6 py-10">
      <div class="w-full max-w-6xl grid gap-10 lg:grid-cols-[1.1fr_0.9fr] items-center">
        <div>
          <div class="kiosk-fade">
            <div
              class="inline-flex items-center gap-3 rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-[#0B2C6F] ring-1 ring-[#0B2C6F]/10"
            >
              <span class="scan-dot"></span>
              Kiosk Scan - Step 1 of 3
            </div>
            <h1 class="mt-6 text-4xl sm:text-5xl font-semibold text-[#0B2C6F] font-hero">Scan Resident QR</h1>
            <p class="mt-4 text-lg text-slate-700/80 max-w-xl">
              Hold the QR code steady in front of the scanner. We'll verify your registration in seconds.
            </p>
          </div>
          <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div
              class="rounded-2xl border border-white/60 bg-white/70 px-4 py-4 shadow-[0_12px_30px_-20px_rgba(15,23,42,0.4)] backdrop-blur kiosk-fade kiosk-fade-delay-1"
            >
              <div class="h-10 w-10 rounded-xl bg-[#0B2C6F]/10 text-[#0B2C6F] flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" />
                  <rect x="14" y="3" width="7" height="7" />
                  <rect x="3" y="14" width="7" height="7" />
                  <path d="M14 14h7v7h-7z" />
                </svg>
              </div>
              <div class="mt-3 text-sm font-semibold text-slate-900">Align QR</div>
              <p class="mt-1 text-xs text-slate-600">Keep it centered in view.</p>
            </div>
            <div
              class="rounded-2xl border border-white/60 bg-white/70 px-4 py-4 shadow-[0_12px_30px_-20px_rgba(15,23,42,0.4)] backdrop-blur kiosk-fade kiosk-fade-delay-2"
            >
              <div class="h-10 w-10 rounded-xl bg-[#0B2C6F]/10 text-[#0B2C6F] flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="7" />
                  <path d="M12 3v4" />
                  <path d="M12 17v4" />
                  <path d="M3 12h4" />
                  <path d="M17 12h4" />
                </svg>
              </div>
              <div class="mt-3 text-sm font-semibold text-slate-900">Hold Steady</div>
              <p class="mt-1 text-xs text-slate-600">Let the scanner lock in.</p>
            </div>
            <div
              class="rounded-2xl border border-white/60 bg-white/70 px-4 py-4 shadow-[0_12px_30px_-20px_rgba(15,23,42,0.4)] backdrop-blur kiosk-fade kiosk-fade-delay-3"
            >
              <div class="h-10 w-10 rounded-xl bg-[#0B2C6F]/10 text-[#0B2C6F] flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 6L9 17l-5-5" />
                </svg>
              </div>
              <div class="mt-3 text-sm font-semibold text-slate-900">Validate</div>
              <p class="mt-1 text-xs text-slate-600">Confirm to continue.</p>
            </div>
          </div>
          <div class="mt-8 flex items-center gap-3 text-sm text-slate-600 kiosk-fade kiosk-fade-delay-3">
            <div
              class="h-10 w-10 rounded-2xl bg-white/80 text-[#0B2C6F] flex items-center justify-center shadow-[0_10px_26px_-20px_rgba(15,23,42,0.4)]"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3l7 4v5c0 4.5-3.1 8.6-7 9-3.9-.4-7-4.5-7-9V7l7-4z" />
                <path d="M9 12l2 2 4-4" />
              </svg>
            </div>
            <span>Secure scan only. No photos are stored.</span>
          </div>
        </div>
        <div class="kiosk-card rounded-3xl px-8 py-8 sm:px-10 sm:py-10 kiosk-fade kiosk-fade-delay-2">
          <div class="flex items-start justify-between gap-6">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Scanner Ready</p>
              <p class="mt-2 text-2xl font-semibold text-slate-900">Place QR inside the frame</p>
            </div>
            <div class="rounded-full bg-[#0B2C6F]/10 px-3 py-2 text-xs font-semibold text-[#0B2C6F]">Auto focus</div>
          </div>
          <div class="mt-6 scan-frame">
            <div class="scan-beam"></div>
            <div class="relative z-10 flex flex-col items-center text-center gap-3 py-6">
              <div
                class="h-14 w-14 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center shadow-[0_12px_24px_-16px_rgba(11,44,111,0.8)]"
              >
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 7V3h4" />
                  <path d="M17 3h4v4" />
                  <path d="M21 17v4h-4" />
                  <path d="M7 21H3v-4" />
                  <path d="M7 12h10" />
                </svg>
              </div>
              <div class="text-lg font-semibold text-[#0B2C6F]">Ready to scan</div>
              <p class="text-sm text-slate-600">The input below is active and waiting.</p>
            </div>
          </div>
          <div class="mt-6">
            <label class="text-sm font-semibold text-slate-600">Manual entry</label>
            <input
              ref="inputRef"
              v-model="qrCode"
              class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-[#0B2C6F]/30 focus:border-transparent"
              placeholder="Enter or scan QR code"
              @keyup.enter="onSubmit"
            />
          </div>
          <button class="mt-6 w-full text-2xl py-4 rounded-2xl font-semibold kiosk-button" @click="onSubmit">
            Validate QR Code
          </button>
          <p class="mt-3 text-sm text-slate-600">Press Enter or tap Validate to continue.</p>
          <div
            v-if="error"
            class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm"
            role="alert"
          >
            {{ error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const qrCode = ref('')
const error = ref('')
const inputRef = ref(null)

const onSubmit = async () => {
  error.value = ''
  if (!qrCode.value) {
    error.value = 'Please scan a QR code'
    return
  }

  try {
    const data = await request('/api/kiosk/validate-qr', {
      method: 'POST',
      body: JSON.stringify({ qr_code: qrCode.value }),
    })
    localStorage.setItem('kiosk_resident', JSON.stringify(data.resident))
    localStorage.setItem('kiosk_allowed_services', JSON.stringify(data.allowed_services || []))
    localStorage.setItem('kiosk_approved', String(data.approved))

    if (!data.approved) {
      error.value = 'Resident is not approved yet.'
      return
    }

    router.push('/services')
  } catch (err) {
    error.value = err.message
  }
}

onMounted(() => {
  localStorage.removeItem('kiosk_ticket')
  localStorage.removeItem('kiosk_service')
  localStorage.removeItem('kiosk_approved')
  setTimeout(() => inputRef.value?.focus(), 50)
})
</script>
