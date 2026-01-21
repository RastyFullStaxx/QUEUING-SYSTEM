<template>
  <div class="min-h-screen kiosk-scan kiosk-stage">
    <div v-if="showStepFlash" class="kiosk-step-flash" aria-hidden="true">
      <span class="kiosk-step-flash-text">{{ labels.stepFlash }}</span>
    </div>
    <div v-if="showStageReveal" class="kiosk-stage-reveal" aria-hidden="true"></div>
    <div
      class="relative z-10 min-h-screen flex items-center justify-center px-6 py-10"
      :class="{ 'kiosk-dim': showLanguagePrompt || showInstructions || showManualEntry || showBackConfirm || showStepFlash }"
    >
      <transition name="kiosk-page">
        <div v-if="isReady && !showStepFlash" class="kiosk-scan-shell">
          <div class="kiosk-hero kiosk-hero-centered kiosk-fade">
            <div class="kiosk-step-header">
              <div class="kiosk-pill">
                <span class="scan-dot"></span>
                {{ labels.stepBadge }}
              </div>
            </div>
            <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-semibold text-[#0B2C6F] font-hero">
              {{ labels.heroTitle }}
            </h1>
            <p class="mt-4 text-lg text-slate-700/80 max-w-xl">{{ labels.heroSubtitle }}</p>
            <p class="kiosk-hero-tip">{{ labels.heroTip }}</p>
          </div>
          <div class="kiosk-scan-stage-row kiosk-fade kiosk-fade-delay-2">
            <button class="kiosk-arrow-button kiosk-arrow-button--back kiosk-action" type="button" @click="openBackConfirm">
              <span>{{ labels.back }}</span>
            </button>
            <div class="kiosk-scan-panel kiosk-option-card kiosk-option-card--alt kiosk-scan-spotlight">
              <div class="kiosk-scan-panel-header">
                <div class="kiosk-option-label">{{ labels.scannerReady }}</div>
                <div class="kiosk-scan-live">
                  <span class="scan-live-dot" aria-hidden="true"></span>
                  <span>{{ labels.cameraActive }}</span>
                </div>
              </div>
              <input
                ref="inputRef"
                v-model="qrCode"
                class="sr-only"
                :placeholder="labels.manualPlaceholder"
                @keyup.enter="onSubmit"
              />
              <div class="scan-frame scan-frame-hero" :class="{ 'is-locked': hasQr }">
                <span class="scan-corner corner-tl" aria-hidden="true"></span>
                <span class="scan-corner corner-tr" aria-hidden="true"></span>
                <span class="scan-corner corner-bl" aria-hidden="true"></span>
                <span class="scan-corner corner-br" aria-hidden="true"></span>
                <div class="scan-grid" aria-hidden="true"></div>
                <div class="scan-beam" :class="{ 'is-locked': hasQr }"></div>
                <div class="scan-center">
                  <div class="scan-state-icon" :class="{ 'is-locked': hasQr }">
                    <svg v-if="hasQr" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M5 12l4 4 10-10" />
                    </svg>
                    <svg v-else class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M3 7V3h4" />
                      <path d="M17 3h4v4" />
                      <path d="M21 17v4h-4" />
                      <path d="M7 21H3v-4" />
                      <path d="M7 12h10" />
                    </svg>
                  </div>
                  <div class="scan-state-title">{{ scanTitle }}</div>
                  <p class="scan-state-text">{{ scanSubtitle }}</p>
                </div>
              </div>
              <div class="scan-status">
                <span class="scan-status-pill" :class="hasQr ? 'is-locked' : 'is-ready'">
                  {{ hasQr ? labels.statusLocked : labels.statusReady }}
                </span>
                <p class="scan-status-text" :class="{ 'is-searching': !hasQr }">
                  {{ scanStatusText }}
                </p>
              </div>
              <div class="kiosk-scan-tips">
                <span class="kiosk-scan-tip">{{ labels.tipHoldSteady }}</span>
                <span class="kiosk-scan-tip">{{ labels.tipAvoidGlare }}</span>
                <span class="kiosk-scan-tip">{{ labels.tipMoveCloser }}</span>
              </div>
              <div class="kiosk-manual-card">
                <div class="kiosk-manual-copy">
                  <p class="kiosk-manual-kicker">{{ labels.manualPrompt }}</p>
                  <p class="kiosk-manual-text">{{ labels.manualAction }}</p>
                </div>
                <button class="kiosk-manual-button" type="button" @click="openManualEntry">
                  {{ labels.manualButton }}
                </button>
              </div>
            </div>
            <div aria-hidden="true"></div>
          </div>
          <div
            v-if="error && !showManualEntry"
            class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm"
            role="alert"
          >
            {{ error }}
          </div>
        </div>
      </transition>
    </div>
    <transition name="kiosk-modal">
      <div v-if="showLanguagePrompt" class="kiosk-modal">
        <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="kiosk-modal-header">
            <p class="kiosk-modal-kicker">{{ labels.languageKicker }}</p>
            <h2 class="kiosk-modal-title">{{ labels.languageTitle }}</h2>
            <p class="kiosk-modal-subtitle">{{ labels.languageSubtitle }}</p>
          </div>
          <div class="kiosk-modal-body">
            <div class="kiosk-language-grid">
              <button class="kiosk-button text-lg py-3 rounded-2xl kiosk-action" @click="setLanguage('en')">English</button>
              <button class="kiosk-button text-lg py-3 rounded-2xl kiosk-action" @click="setLanguage('tl')">Tagalog</button>
            </div>
            <p class="kiosk-modal-note">{{ labels.languageNote }}</p>
          </div>
        </div>
      </div>
    </transition>
    <transition name="kiosk-modal">
      <div v-if="showInstructions" class="kiosk-modal">
        <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="kiosk-modal-header">
            <p class="kiosk-modal-kicker">{{ labels.instructionsKicker }}</p>
            <h2 class="kiosk-modal-title">{{ labels.instructionsTitle }}</h2>
            <p class="kiosk-modal-subtitle">{{ labels.instructionsSubtitle }}</p>
          </div>
          <div class="kiosk-modal-body">
            <div class="reminder-card">
              <p class="text-base text-slate-700">{{ labels.heroSubtitle }}</p>
              <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="reminder-item">
                  <div class="reminder-icon">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="7" height="7" />
                      <rect x="14" y="3" width="7" height="7" />
                      <rect x="3" y="14" width="7" height="7" />
                      <path d="M14 14h7v7h-7z" />
                    </svg>
                  </div>
                  <div>
                    <p class="reminder-title">{{ labels.step1Title }}</p>
                    <p class="reminder-body">{{ labels.step1Body }}</p>
                  </div>
                </div>
                <div class="reminder-item">
                  <div class="reminder-icon">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="7" />
                      <path d="M12 3v4" />
                      <path d="M12 17v4" />
                      <path d="M3 12h4" />
                      <path d="M17 12h4" />
                    </svg>
                  </div>
                  <div>
                    <p class="reminder-title">{{ labels.step2Title }}</p>
                    <p class="reminder-body">{{ labels.step2Body }}</p>
                  </div>
                </div>
                <div class="reminder-item">
                  <div class="reminder-icon">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 6L9 17l-5-5" />
                    </svg>
                  </div>
                  <div>
                    <p class="reminder-title">{{ labels.step3Title }}</p>
                    <p class="reminder-body">{{ labels.step3Body }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="kiosk-modal-actions">
            <button class="w-full kiosk-button text-lg py-3 rounded-2xl kiosk-action" @click="acknowledgeInstructions">
              {{ labels.understandButton }}
            </button>
          </div>
        </div>
      </div>
    </transition>
    <transition name="kiosk-modal">
      <div v-if="showManualEntry" class="kiosk-modal">
        <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="kiosk-modal-header">
            <p class="kiosk-modal-kicker">{{ labels.manualKicker }}</p>
            <h2 class="kiosk-modal-title">{{ labels.manualEntry }}</h2>
            <p class="kiosk-modal-subtitle">{{ labels.manualHelper }}</p>
          </div>
          <div class="kiosk-modal-body">
            <input
              ref="manualInputRef"
              v-model="qrCode"
              class="kiosk-input"
              :placeholder="labels.manualPlaceholder"
              @keyup.enter="onSubmit"
            />
            <p class="kiosk-modal-note">{{ labels.validateHint }}</p>
            <div
              v-if="error"
              class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm"
              role="alert"
            >
              {{ error }}
            </div>
          </div>
          <div class="kiosk-modal-actions">
            <button class="w-full text-lg py-3 rounded-2xl font-semibold kiosk-button kiosk-action" @click="onSubmit">
              {{ labels.validateButton }}
            </button>
            <button class="w-full text-lg py-3 rounded-2xl kiosk-secondary-button" @click="closeManualEntry">
              {{ labels.backToScan }}
            </button>
          </div>
        </div>
      </div>
    </transition>
    <transition name="kiosk-modal">
      <div v-if="showBackConfirm" class="kiosk-modal" @click.self="closeBackConfirm">
        <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="kiosk-modal-header">
            <p class="kiosk-modal-kicker">{{ labels.backKicker }}</p>
            <h2 class="kiosk-modal-title">{{ labels.backTitle }}</h2>
            <p class="kiosk-modal-subtitle">{{ labels.backSubtitle }}</p>
          </div>
          <div class="kiosk-modal-actions">
            <div class="grid gap-3 sm:grid-cols-2">
              <button class="kiosk-secondary-button text-lg py-3 rounded-2xl" type="button" @click="closeBackConfirm">
                {{ labels.backStay }}
              </button>
              <button class="kiosk-button text-lg py-3 rounded-2xl kiosk-action" type="button" @click="confirmBack">
                {{ labels.backConfirm }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const qrCode = ref('')
const error = ref('')
const inputRef = ref(null)
const manualInputRef = ref(null)
const language = ref('')
const instructionsAccepted = ref(false)
const showManualEntry = ref(false)
const languageDialogOpen = ref(false)
const showStepFlash = ref(false)
const showBackConfirm = ref(false)
const stepFlashTimer = ref(null)
const showStageReveal = ref(false)
const stageRevealTimer = ref(null)

const copy = {
  en: {
    stepBadge: 'Kiosk Scan - Step 1 of 3',
    heroTitle: 'Scan Resident QR',
    heroSubtitle: 'Hold the QR inside the frame. Verification takes seconds.',
    heroTip: 'Tip: increase screen brightness / avoid glare.',
    stepFlash: 'STEP 1',
    instructionsSubtitle: 'A quick guide before we start.',
    reminderKicker: 'Reminder',
    step1Title: 'Align QR',
    step1Body: 'Center it in the frame.',
    step2Title: 'Hold Steady',
    step2Body: 'Let the scanner lock in.',
    step3Title: 'Validate',
    step3Body: 'Confirm to continue.',
    scannerReady: 'Scanner Ready',
    scannerPrompt: 'Place QR inside the frame',
    autoFocus: 'Auto focus',
    readyTitle: 'Ready to scan',
    readySubtitle: 'The scanner is active and waiting.',
    detectedTitle: 'QR found',
    detectedSubtitle: 'Verifying...',
    manualEntry: 'Manual entry',
    manualPlaceholder: 'Enter or scan QR code',
    validateButton: 'Validate QR Code',
    validateHint: 'Press Enter or tap Validate to continue.',
    manualPrompt: 'Trouble scanning?',
    manualAction: 'Enter code instead.',
    manualButton: 'Enter code manually',
    back: 'Go back',
    backKicker: 'Back',
    backTitle: 'Go back to the main menu?',
    backSubtitle: 'This will end the current scan.',
    backStay: 'Continue scanning',
    backConfirm: 'Go back',
    manualKicker: 'Manual entry',
    manualHelper: 'Type the QR code to continue.',
    backToScan: 'Back to scanner',
    statusReady: 'Ready',
    statusLocked: 'Locked',
    statusLooking: 'Searching for QR',
    statusDetected: 'QR found — verifying',
    cameraActive: 'Camera active',
    tipHoldSteady: 'Hold steady',
    tipAvoidGlare: 'Avoid glare',
    tipMoveCloser: 'Move closer',
    instructionsKicker: 'Secure scan',
    instructionsTitle: 'Quick Reminders',
    instructionsLine1: 'Secure scan only.',
    instructionsLine2: 'No photos are stored.',
    instructionsLine3: 'Keep the QR steady.',
    understandButton: 'I understand',
    languageKicker: 'Language',
    languageTitle: 'Choose your language',
    languageSubtitle: 'Pumili ng wika',
    languageNote: 'You can change this anytime during the flow.',
  },
  tl: {
    stepBadge: 'Kiosk Scan - Hakbang 1 sa 3',
    heroTitle: 'I-scan ang Resident QR',
    heroSubtitle: 'Ilagay ang QR sa loob ng frame. Mabilis ang beripikasyon.',
    heroTip: 'Tip: dagdagan ang brightness / iwasan ang glare.',
    stepFlash: 'HAKBANG 1',
    instructionsSubtitle: 'Mabilis na gabay bago mag-scan.',
    reminderKicker: 'Paalala',
    step1Title: 'I-align ang QR',
    step1Body: 'Igitna sa frame.',
    step2Title: 'Panatilihing steady',
    step2Body: 'Hayaan ang scanner.',
    step3Title: 'I-validate',
    step3Body: 'Kumpirmahin para magpatuloy.',
    scannerReady: 'Handa ang scanner',
    scannerPrompt: 'Ilagay ang QR sa loob ng frame',
    autoFocus: 'Auto focus',
    readyTitle: 'Handa nang mag-scan',
    readySubtitle: 'Aktibo ang scanner at naghihintay.',
    detectedTitle: 'Nakita ang QR',
    detectedSubtitle: 'Bine-verify...',
    manualEntry: 'Manwal na pagpasok',
    manualPlaceholder: 'Ilagay o i-scan ang QR code',
    validateButton: 'I-validate ang QR Code',
    validateHint: 'Pindutin ang Enter o tapikin ang Validate para magpatuloy.',
    manualPrompt: 'Hindi ma-scan?',
    manualAction: 'Ilagay ang code.',
    manualButton: 'Maglagay ng code',
    back: 'Bumalik',
    backKicker: 'Bumalik',
    backTitle: 'Bumalik sa main menu?',
    backSubtitle: 'Matatapos ang kasalukuyang scan.',
    backStay: 'Magpatuloy sa scan',
    backConfirm: 'Bumalik',
    manualKicker: 'Manwal na pagpasok',
    manualHelper: 'I-type ang QR code para magpatuloy.',
    backToScan: 'Bumalik sa scanner',
    statusReady: 'Handa',
    statusLocked: 'Nakabasa',
    statusLooking: 'Naghahanap ng QR',
    statusDetected: 'Nakita ang QR — bine-verify',
    cameraActive: 'Aktibo ang camera',
    tipHoldSteady: 'Panatilihing steady',
    tipAvoidGlare: 'Iwasan ang glare',
    tipMoveCloser: 'Lumapit',
    instructionsKicker: 'Ligtas na scan',
    instructionsTitle: 'Mabilis na paalala',
    instructionsLine1: 'Ligtas na scan lamang.',
    instructionsLine2: 'Walang larawang itinatago.',
    instructionsLine3: 'Panatilihing steady ang QR.',
    understandButton: 'Naiintindihan ko',
    languageKicker: 'Wika',
    languageTitle: 'Pumili ng wika',
    languageSubtitle: 'Choose your language',
    languageNote: 'Maaari itong palitan anumang oras.',
  },
}

const labels = computed(() => copy[language.value] || copy.en)
const showLanguagePrompt = computed(() => !language.value || languageDialogOpen.value)
const showInstructions = computed(
  () =>
    Boolean(language.value) &&
    !instructionsAccepted.value &&
    !showLanguagePrompt.value
)
const isReady = computed(() => Boolean(language.value) && instructionsAccepted.value)
const hasQr = computed(() => Boolean(qrCode.value && qrCode.value.trim()))
const scanTitle = computed(() => (hasQr.value ? labels.value.detectedTitle : labels.value.readyTitle))
const scanSubtitle = computed(() => (hasQr.value ? labels.value.detectedSubtitle : labels.value.readySubtitle))
const scanStatusText = computed(() => (hasQr.value ? labels.value.statusDetected : labels.value.statusLooking))

const setLanguage = (value) => {
  language.value = value
  localStorage.setItem('kiosk_language', value)
  languageDialogOpen.value = false
}

const openLanguagePrompt = () => {
  languageDialogOpen.value = true
}

const triggerStepFlash = () => {
  if (stepFlashTimer.value) {
    clearTimeout(stepFlashTimer.value)
  }
  if (stageRevealTimer.value) {
    clearTimeout(stageRevealTimer.value)
  }
  showStepFlash.value = true
  showStageReveal.value = false
  stepFlashTimer.value = setTimeout(() => {
    showStepFlash.value = false
    showStageReveal.value = true
    stageRevealTimer.value = setTimeout(() => {
      showStageReveal.value = false
    }, 1400)
  }, 3600)
}

const acknowledgeInstructions = () => {
  instructionsAccepted.value = true
  triggerStepFlash()
}


const openManualEntry = () => {
  showManualEntry.value = true
  error.value = ''
  setTimeout(() => manualInputRef.value?.focus(), 50)
}

const closeManualEntry = () => {
  showManualEntry.value = false
  setTimeout(() => inputRef.value?.focus(), 50)
}

const openBackConfirm = () => {
  showBackConfirm.value = true
}

const closeBackConfirm = () => {
  showBackConfirm.value = false
}

const confirmBack = () => {
  showBackConfirm.value = false
  router.push('/welcome')
}

const onSubmit = async () => {
  error.value = ''
  if (!qrCode.value) {
    error.value = labels.value.manualPlaceholder
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
      error.value = language.value === 'tl' ? 'Hindi pa aprubado ang residente.' : 'Resident is not approved yet.'
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
  if (isReady.value) {
    setTimeout(() => inputRef.value?.focus(), 50)
  }
})

onBeforeUnmount(() => {
  if (stepFlashTimer.value) {
    clearTimeout(stepFlashTimer.value)
  }
  if (stageRevealTimer.value) {
    clearTimeout(stageRevealTimer.value)
  }
})

watch(isReady, (ready) => {
  if (ready) {
    setTimeout(() => inputRef.value?.focus(), 50)
  }
})
</script>
