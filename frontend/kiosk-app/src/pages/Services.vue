<template>
  <div class="min-h-screen kiosk-service-stage" @click="handleStageClick">
    <div v-if="showStepFlash" class="kiosk-step-flash" aria-hidden="true">
      <span class="kiosk-step-flash-text">{{ labels.stepFlash }}</span>
    </div>
    <div v-if="showStageReveal" class="kiosk-stage-reveal" aria-hidden="true"></div>
    <div
      class="relative z-10 min-h-screen flex items-center justify-center px-6 py-10 kiosk-service-body"
      :class="{ 'kiosk-dim': showConfirm || showReminder || showStepFlash }"
    >
      <div v-if="!showStepFlash" class="w-full max-w-none mx-auto grid gap-10">
        <div class="kiosk-service-hero kiosk-fade">
          <div class="kiosk-step-header">
            <div class="kiosk-pill">
              <span class="scan-dot"></span>
              {{ labels.stepBadge }}
            </div>
          </div>
          <div class="kiosk-service-hero-slot">
            <transition name="kiosk-panel" mode="out-in">
              <div v-if="selectedServices.length" class="kiosk-service-hero-cta" :style="ctaStyle">
                <p class="kiosk-service-hero-kicker">{{ labels.selectedLabel }}</p>
                <h1 class="kiosk-service-hero-title">{{ selectedTitle }}</h1>
                <p class="kiosk-service-hero-note">{{ labels.selectedNote }}</p>
                <div class="kiosk-service-hero-list">
                  <span
                    v-for="service in selectedServices"
                    :key="service.id"
                    class="kiosk-service-hero-chip"
                  >
                    {{ service.name }}
                  </span>
                </div>
              </div>
              <div v-else class="kiosk-service-hero-default">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold text-[#0B2C6F] font-hero">
                  {{ labels.title }}
                </h1>
                <p class="kiosk-service-subtitle">
                  <span>{{ labels.subtitle }}</span>
                </p>
                <p class="kiosk-service-greeting">{{ greeting }}</p>
                <div class="kiosk-service-hint">
                  <span>{{ labels.hint }}</span>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <div class="kiosk-service-ribbon" aria-hidden="true">
          <span class="kiosk-service-ribbon-line"></span>
          <span class="kiosk-service-ribbon-dots">
            <span class="kiosk-service-ribbon-dot"></span>
            <span class="kiosk-service-ribbon-dot"></span>
            <span class="kiosk-service-ribbon-dot"></span>
          </span>
        </div>

        <div v-if="services.length" class="kiosk-service-stage-row kiosk-fade kiosk-fade-delay-1">
          <button
            class="kiosk-arrow-button kiosk-arrow-button--back kiosk-action"
            type="button"
            @click="handleBack"
          >
            <span>{{ labels.back }}</span>
          </button>
          <div class="kiosk-service-grid">
            <button
              v-for="(service, index) in services"
              :key="service.id"
              type="button"
              class="kiosk-service-card"
              :class="{ 'is-expanded': isExpanded(service.id), 'is-selected': isSelected(service.id) }"
              :style="cardStyle(service, index)"
              :data-service-id="service.id"
              :ref="(el) => setCardRef(el, service.id)"
              @mouseenter="setHovered(service.id)"
              @mouseleave="handleCardLeave"
              @focus="setHovered(service.id)"
              @blur="handleCardLeave"
              @mousemove="handleCardMove"
              @click="selectService(service, $event)"
              :aria-pressed="isSelected(service.id)"
            >
            <span class="kiosk-service-accent" aria-hidden="true"></span>
            <span class="kiosk-service-highlight" aria-hidden="true"></span>
            <span class="kiosk-card-sheen" aria-hidden="true"></span>
            <div class="kiosk-service-top">
                <div class="kiosk-service-icon">
                  <span class="kiosk-service-pulse" aria-hidden="true"></span>
                  <svg v-if="getServiceMeta(service).icon === 'shield'" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l7 4v6c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6l7-4z" />
                  </svg>
                  <svg v-else-if="getServiceMeta(service).icon === 'briefcase'" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 6V4h8v2" />
                    <rect x="3" y="6" width="18" height="14" rx="2" />
                    <path d="M3 12h18" />
                  </svg>
                  <svg v-else-if="getServiceMeta(service).icon === 'home'" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 11l9-7 9 7" />
                    <path d="M5 10v10h14V10" />
                  </svg>
                  <svg v-else-if="getServiceMeta(service).icon === 'heart'" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.8 7.8a4.5 4.5 0 0 0-6.4 0L12 10.2l-2.4-2.4a4.5 4.5 0 1 0-6.4 6.4l2.4 2.4L12 22l6.4-5.4 2.4-2.4a4.5 4.5 0 0 0 0-6.4z" />
                  </svg>
                  <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                    <path d="M14 3v5h5" />
                  </svg>
                </div>
                <div class="kiosk-service-copy">
                  <div class="kiosk-service-title-row">
                    <transition name="kiosk-chip">
                      <div v-if="isSelected(service.id)" class="kiosk-service-selected">
                        <span>{{ labels.selected }}</span>
                      </div>
                    </transition>
                    <p class="kiosk-service-name">{{ service.name }}</p>
                  </div>
                  <p class="kiosk-service-snippet">{{ text(getServiceMeta(service).summary) }}</p>
                </div>
              </div>

              <div class="kiosk-service-tagline">
                <span>{{ text(getServiceMeta(service).tagline) }}</span>
              </div>
              <div class="kiosk-service-card-hint" :class="{ 'is-hidden': isExpanded(service.id) }">
                <span>{{ labels.cardHint }}</span>
                <svg class="kiosk-service-hint-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M7 10l5 5 5-5" />
                </svg>
              </div>

              <div class="kiosk-service-details">
                <div class="kiosk-service-stats">
                  <div v-for="stat in getServiceMeta(service).stats" :key="stat.label.en" class="kiosk-service-stat">
                    <div class="kiosk-service-stat-label">
                      <span>{{ text(stat.label) }}</span>
                    </div>
                    <div class="kiosk-service-stat-value">{{ stat.value }}</div>
                  </div>
                </div>
                <div class="kiosk-service-requirements">
                  <div class="kiosk-service-requirements-title">
                    <span>{{ labels.requirements }}</span>
                  </div>
                  <div class="kiosk-service-requirements-list">
                    <div
                      v-for="item in getServiceMeta(service).requirements"
                      :key="item.en"
                      class="kiosk-service-requirements-item"
                    >
                      <span class="kiosk-service-check">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M5 12l4 4 10-10" />
                        </svg>
                      </span>
                      <div>
                        <p class="kiosk-service-req-text">{{ text(item) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <span class="kiosk-service-ripple" aria-hidden="true"></span>
              <span class="kiosk-service-blob" aria-hidden="true"></span>

            </button>
          </div>
          <button
            class="kiosk-arrow-button kiosk-arrow-button--proceed kiosk-action"
            :class="{ 'is-disabled': !selectedServices.length }"
            type="button"
            :aria-disabled="!selectedServices.length"
            @click="handleProceed"
          >
            <span>{{ labels.proceed }}</span>
          </button>
        </div>

        <div v-else class="kiosk-service-empty kiosk-fade kiosk-fade-delay-1">
          <h2 class="text-2xl font-semibold text-slate-800">{{ labels.emptyTitle }}</h2>
          <p class="text-slate-500 mt-2">{{ labels.emptyBody }}</p>
        </div>

        <p v-if="error" class="kiosk-service-error">{{ error }}</p>
      </div>
    </div>

    <transition name="kiosk-modal">
      <div v-if="showConfirm" class="kiosk-modal" @click.self="closeConfirm">
        <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card service-confirm-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="kiosk-modal-header">
            <p class="kiosk-modal-kicker">{{ labels.confirmKicker }}</p>
            <h2 class="kiosk-modal-title">{{ labels.confirmTitle }}</h2>
            <p class="kiosk-modal-subtitle">{{ labels.confirmSubtitle }}</p>
          </div>
          <div class="kiosk-modal-body">
            <div class="service-confirm-panel">
              <div class="service-confirm-check" aria-hidden="true">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10" />
                </svg>
              </div>
              <div class="service-confirm-chip">
                <span>{{ labels.confirmChip }}</span>
              </div>
              <div class="service-confirm-name">{{ selectedSummary }}</div>
              <div class="service-confirm-note">
                <span>{{ labels.confirmNote }}</span>
              </div>
            </div>
          </div>
          <div class="kiosk-modal-actions">
            <div class="grid gap-3 sm:grid-cols-2">
              <button class="kiosk-secondary-button text-lg py-3 rounded-2xl" type="button" @click="closeConfirm">
                <span>{{ labels.change }}</span>
              </button>
              <button
                class="kiosk-button text-lg py-3 rounded-2xl kiosk-action"
                type="button"
                :disabled="isCreatingTicket"
                @click="confirmProceed"
              >
                <span>{{ labels.confirm }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <transition name="kiosk-modal">
      <div v-if="showReminder" class="kiosk-modal" @click.self="closeReminder">
        <div class="kiosk-modal-card kiosk-modal-glow kiosk-portal-card service-reminder-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="kiosk-modal-header">
            <p class="kiosk-modal-kicker">{{ labels.reminderKicker }}</p>
            <h2 class="kiosk-modal-title">{{ labels.reminderTitle }}</h2>
            <p class="kiosk-modal-subtitle">{{ labels.reminderBody }}</p>
          </div>
          <div class="kiosk-modal-actions">
            <button class="w-full kiosk-button text-lg py-3 rounded-2xl kiosk-action" type="button" @click="closeReminder">
              {{ labels.reminderButton }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../api'

const router = useRouter()
const services = ref([])
const error = ref('')
const isCreatingTicket = ref(false)
const selectedServiceIds = ref([])
const expandedServiceId = ref(null)
const hoveredServiceId = ref(null)
const showConfirm = ref(false)
const showReminder = ref(false)
const cardSpans = ref({})
const cardElements = new Map()
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')
const showStepFlash = ref(true)
const stepFlashTimer = ref(null)
const showStageReveal = ref(false)
const stageRevealTimer = ref(null)

const copy = {
  en: {
    stepBadge: 'Kiosk Scan - Step 2 of 3',
    stepFlash: 'STEP 2',
    title: 'Select Services',
    subtitle: 'Tap a panel to expand and select one or more services.',
    hint: 'Tap to select multiple services.',
    selected: 'Selected',
    selectedLabel: 'Selected services',
    selectedNote: 'Review your selections before proceeding.',
    selectedCountLabel: 'services selected',
    cardHint: 'Tap for details',
    back: 'Go back',
    proceed: 'Proceed',
    requirements: 'Requirements',
    emptyTitle: 'No services available',
    emptyBody: 'Please ask staff for assistance.',
    confirmKicker: 'Confirmation',
    confirmTitle: 'Proceed with selected services?',
    confirmSubtitle: 'Review your selections before continuing.',
    confirmChip: 'You selected',
    confirmNote: 'Please confirm to continue to Step 3.',
    change: 'Change selection',
    confirm: 'Confirm & Continue',
    reminderKicker: 'Reminder',
    reminderTitle: 'Select at least one service',
    reminderBody: 'Please choose one or more services before proceeding.',
    reminderButton: 'Got it',
  },
  tl: {
    stepBadge: 'Kiosk Scan - Hakbang 2 sa 3',
    stepFlash: 'HAKBANG 2',
    title: 'Piliin ang mga Serbisyo',
    subtitle: 'I-tap ang panel para buksan at pumili ng isa o higit pang serbisyo.',
    hint: 'I-tap para pumili ng marami.',
    selected: 'Napili',
    selectedLabel: 'Napiling mga serbisyo',
    selectedNote: 'Suriin ang mga napili bago magpatuloy.',
    selectedCountLabel: 'serbisyong napili',
    cardHint: 'I-tap para sa detalye',
    back: 'Bumalik',
    proceed: 'Magpatuloy',
    requirements: 'Kailangan',
    emptyTitle: 'Walang serbisyong available',
    emptyBody: 'Mangyaring humingi ng tulong sa staff.',
    confirmKicker: 'Kumpirmasyon',
    confirmTitle: 'Magpatuloy sa mga napiling serbisyo?',
    confirmSubtitle: 'Suriin muna ang mga napili bago magpatuloy.',
    confirmChip: 'Napili mo',
    confirmNote: 'Kumpirmahin para magpatuloy sa Hakbang 3.',
    change: 'Palitan ang pili',
    confirm: 'Kumpirmahin at Magpatuloy',
    reminderKicker: 'Paalala',
    reminderTitle: 'Pumili ng kahit isang serbisyo',
    reminderBody: 'Mangyaring pumili ng isa o higit pang serbisyo bago magpatuloy.',
    reminderButton: 'Sige',
  },
}

const storedLanguage = localStorage.getItem('kiosk_language')
const language = ref(storedLanguage === 'tl' ? 'tl' : 'en')
const labels = computed(() => copy[language.value] || copy.en)
const isTagalog = computed(() => language.value === 'tl')
const text = (pair) => (isTagalog.value ? pair.tl : pair.en)
const greetingText = {
  en: {
    morning: 'Good morning! Choose a service and we will guide you through.',
    afternoon: 'Good afternoon! Pick a service to get started.',
    evening: 'Good evening! Choose a service and we will assist you.',
  },
  tl: {
    morning: 'Magandang umaga! Pumili ng serbisyo at tutulungan ka namin.',
    afternoon: 'Magandang hapon! Pumili ng serbisyo para magsimula.',
    evening: 'Magandang gabi! Pumili ng serbisyo at gagabayan ka namin.',
  },
}
const greeting = computed(() => {
  const hour = new Date().getHours()
  const slot = hour < 12 ? 'morning' : hour < 18 ? 'afternoon' : 'evening'
  const group = greetingText[language.value] || greetingText.en
  return group[slot]
})

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

const serviceMeta = {
  PROOF_RESIDENCY: {
    icon: 'home',
    accent: '#1C7C54',
    accentSoft: 'rgba(28, 124, 84, 0.16)',
    accentGlow: 'rgba(28, 124, 84, 0.32)',
    tagline: {
      en: 'Verify your residency for school, work, or ID applications.',
      tl: 'Patunay ng paninirahan para sa paaralan, trabaho, o ID.',
    },
    summary: {
      en: 'We verify your address and issue the certificate promptly.',
      tl: 'Bine-verify ang address at mabilis na naglalabas ng sertipikasyon.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '10-15 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window A' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Proof of address', tl: 'Patunay ng tirahan' },
    ],
  },
  INDIGENCY: {
    icon: 'shield',
    accent: '#0B2C6F',
    accentSoft: 'rgba(11, 44, 111, 0.16)',
    accentGlow: 'rgba(11, 44, 111, 0.32)',
    tagline: {
      en: 'Request a certificate for assistance and government programs.',
      tl: 'Humingi ng sertipiko para sa tulong at programa ng gobyerno.',
    },
    summary: {
      en: 'We confirm eligibility and release the certificate the same day.',
      tl: 'Tinitiyak ang eligibility at nilalabas sa parehong araw.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '15-20 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window B' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Barangay form', tl: 'Barangay form' },
    ],
  },
  INCOME_LOAN: {
    icon: 'briefcase',
    accent: '#185ADB',
    accentSoft: 'rgba(24, 90, 219, 0.16)',
    accentGlow: 'rgba(24, 90, 219, 0.32)',
    tagline: {
      en: 'Income certification for loans and financial applications.',
      tl: 'Income certification para sa loan at financial applications.',
    },
    summary: {
      en: 'Submit details, verify documents, and get processing updates.',
      tl: 'Ibigay ang detalye, i-verify ang dokumento, at mabilis ang proseso.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '20-30 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window C' },
      { label: { en: 'Form', tl: 'Porma' }, value: 'LI-01' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Income details', tl: 'Detalye ng kita' },
    ],
  },
  SOLO_PARENT: {
    icon: 'heart',
    accent: '#D84343',
    accentSoft: 'rgba(216, 67, 67, 0.16)',
    accentGlow: 'rgba(216, 67, 67, 0.32)',
    tagline: {
      en: 'Solo parent certification for benefits and official records.',
      tl: 'Sertipikasyon para sa benepisyo at opisyal na tala.',
    },
    summary: {
      en: 'We review eligibility and issue the certificate after validation.',
      tl: 'Sinusuri ang eligibility at inilalabas pagkatapos ng validation.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '20-25 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window D' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Solo parent documents', tl: 'Dokumento ng solo parent' },
    ],
  },
  SPECIAL_PERMIT: {
    icon: 'file',
    accent: '#7C3AED',
    accentSoft: 'rgba(124, 58, 237, 0.16)',
    accentGlow: 'rgba(124, 58, 237, 0.32)',
    tagline: {
      en: 'Secure special permits with guided requirements.',
      tl: 'Kumuha ng special permit na may malinaw na requirements.',
    },
    summary: {
      en: 'Submit the request, verify documents, and await approval.',
      tl: 'Ihain ang request, i-verify ang dokumento, at hintayin ang approval.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '15-25 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window E' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Request letter', tl: 'Liham ng request' },
    ],
  },
  BUILDING_PERMIT: {
    icon: 'briefcase',
    accent: '#0F766E',
    accentSoft: 'rgba(15, 118, 110, 0.16)',
    accentGlow: 'rgba(15, 118, 110, 0.32)',
    tagline: {
      en: 'Apply for building permits with a guided checklist.',
      tl: 'Mag-apply ng building permit na may malinaw na checklist.',
    },
    summary: {
      en: 'Submit plans, verify documents, and track approvals.',
      tl: 'Ibigay ang plano, i-verify ang dokumento, at subaybayan ang approval.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '30-45 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window F' },
      { label: { en: 'Form', tl: 'Porma' }, value: 'BP-02' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Building plans', tl: 'Plano ng building' },
    ],
  },
}

const defaultMeta = {
  icon: 'file',
  accent: '#0B2C6F',
  accentSoft: 'rgba(11, 44, 111, 0.16)',
  accentGlow: 'rgba(11, 44, 111, 0.32)',
  tagline: {
    en: 'Barangay support for common requests and certifications.',
    tl: 'Tulong barangay para sa karaniwang serbisyo at sertipikasyon.',
  },
  summary: {
    en: 'We provide guided steps and clear requirements for each service.',
    tl: 'May malinaw na requirements at gabay sa bawat serbisyo.',
  },
  stats: [
    { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '10-20 min' },
    { label: { en: 'Counter', tl: 'Counter' }, value: 'Queue-based' },
    { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
  ],
  requirements: [
    { en: 'Valid ID', tl: 'Balidong ID' },
    { en: 'Service form', tl: 'Form ng serbisyo' },
  ],
}

const selectedServices = computed(() =>
  services.value.filter((service) => selectedServiceIds.value.includes(service.id))
)

const primaryService = computed(() => selectedServices.value[0] || null)

const normalizeServiceName = (name = '') =>
  name
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, ' ')
    .trim()

const serviceNameMap = {
  'proof of residency': 'PROOF_RESIDENCY',
  'barangay indigency': 'INDIGENCY',
  'loan of income': 'INCOME_LOAN',
  'certificate of solo parent': 'SOLO_PARENT',
  'special permit': 'SPECIAL_PERMIT',
  'building permit': 'BUILDING_PERMIT',
}

const getServiceMeta = (service) => {
  const code = (service?.code || '').toUpperCase()
  const nameKey = serviceNameMap[normalizeServiceName(service?.name)]
  return serviceMeta[code] || serviceMeta[nameKey] || defaultMeta
}

const capturePositions = () => {
  const positions = new Map()
  cardElements.forEach((element, id) => {
    positions.set(id, element.getBoundingClientRect())
  })
  return positions
}

const animateReflow = async (positions) => {
  await nextTick()
  requestAnimationFrame(() => {
    cardElements.forEach((element, id) => {
      const first = positions.get(id)
      if (!first) return
      const last = element.getBoundingClientRect()
      const dx = first.left - last.left
      const dy = first.top - last.top
      if (Math.abs(dx) < 1 && Math.abs(dy) < 1) return
      element.style.setProperty('--flow-x', `${dx}px`)
      element.style.setProperty('--flow-y', `${dy}px`)
      element.classList.add('is-reflowing')
      element.getBoundingClientRect()
      requestAnimationFrame(() => {
        element.style.setProperty('--flow-x', '0px')
        element.style.setProperty('--flow-y', '0px')
        window.setTimeout(() => {
          element.classList.remove('is-reflowing')
        }, 920)
      })
    })
  })
}

const selectedMeta = computed(() =>
  primaryService.value ? getServiceMeta(primaryService.value) : null
)

const selectedTitle = computed(() => {
  if (selectedServices.value.length === 1) {
    return selectedServices.value[0].name
  }
  return `${selectedServices.value.length} ${labels.value.selectedCountLabel}`
})

const selectedSummary = computed(() =>
  selectedServices.value.map((service) => service.name).join(', ')
)

const ctaStyle = computed(() => {
  if (!selectedMeta.value) return null
  return {
    '--service-accent': selectedMeta.value.accent,
    '--service-accent-glow': selectedMeta.value.accentGlow,
  }
})

const cardStyle = (service, index = 0) => {
  const meta = getServiceMeta(service)
  const span = cardSpans.value[service.id] || 1
  const delay = Math.min(index * 90, 540)
  return {
    '--service-accent': meta.accent,
    '--service-accent-soft': meta.accentSoft,
    '--service-accent-glow': meta.accentGlow,
    '--row-span': span,
    '--card-delay': `${delay}ms`,
  }
}

const isSelected = (serviceId) => selectedServiceIds.value.includes(serviceId)

const isExpanded = (serviceId) => {
  if (expandedServiceId.value) {
    return expandedServiceId.value === serviceId
  }
  return hoveredServiceId.value === serviceId
}

const setHovered = (serviceId) => {
  if (expandedServiceId.value) return
  const positions = capturePositions()
  hoveredServiceId.value = serviceId
  animateReflow(positions)
}

const clearHovered = () => {
  if (expandedServiceId.value) return
  const positions = capturePositions()
  hoveredServiceId.value = null
  animateReflow(positions)
}

const clearSelection = () => {
  if (!selectedServiceIds.value.length) return
  selectedServiceIds.value = []
  expandedServiceId.value = null
  hoveredServiceId.value = null
  showConfirm.value = false
  showReminder.value = false
}

const handleStageClick = (event) => {
  const target = event.target
  if (target.closest('.kiosk-service-card')) return
  if (target.closest('.kiosk-service-cta')) return
  if (target.closest('.kiosk-proceed-floating')) return
  if (target.closest('.kiosk-arrow-button')) return
  if (target.closest('.kiosk-modal')) return
  clearSelection()
}

const handleCardLeave = (event) => {
  clearHovered()
  resetCardTilt(event)
}

const handleCardMove = (event) => {
  const card = event.currentTarget
  if (!card) return
  const rect = card.getBoundingClientRect()
  const x = event.clientX - rect.left
  const y = event.clientY - rect.top
  const centerX = rect.width / 2
  const centerY = rect.height / 2
  const tiltX = ((y - centerY) / centerY) * -5
  const tiltY = ((x - centerX) / centerX) * 5
  card.style.setProperty('--tilt-x', `${tiltX.toFixed(2)}deg`)
  card.style.setProperty('--tilt-y', `${tiltY.toFixed(2)}deg`)
  card.style.setProperty('--glow-x', `${((x / rect.width) * 100).toFixed(2)}%`)
  card.style.setProperty('--glow-y', `${((y / rect.height) * 100).toFixed(2)}%`)
}

const resetCardTilt = (event) => {
  const card = event?.currentTarget
  if (!card) return
  card.style.setProperty('--tilt-x', '0deg')
  card.style.setProperty('--tilt-y', '0deg')
}

const updateCardSpan = (element, entry) => {
  if (!element) return
  const grid = element.parentElement
  if (!grid) return
  const styles = getComputedStyle(grid)
  const rowGap = parseFloat(styles.rowGap) || 0
  const rowHeight = parseFloat(styles.getPropertyValue('--kiosk-row-height')) || 6
  const measured =
    entry?.borderBoxSize?.[0]?.blockSize ??
    entry?.contentRect?.height ??
    element.getBoundingClientRect().height
  const span = Math.max(1, Math.ceil((measured + rowGap) / (rowHeight + rowGap)))
  cardSpans.value[element.dataset.serviceId] = span
}

const resizeObserver =
  typeof ResizeObserver !== 'undefined'
    ? new ResizeObserver((entries) => {
        entries.forEach((entry) => {
          updateCardSpan(entry.target, entry)
        })
      })
    : null

const setCardRef = (element, serviceId) => {
  const existing = cardElements.get(serviceId)
  if (existing && resizeObserver) {
    resizeObserver.unobserve(existing)
    cardElements.delete(serviceId)
  }

  if (!element) return
  element.dataset.serviceId = String(serviceId)
  cardElements.set(serviceId, element)
  if (resizeObserver) {
    resizeObserver.observe(element)
  }
  requestAnimationFrame(() => {
    updateCardSpan(element)
  })
}

const loadServices = async () => {
  try {
    const cached = JSON.parse(localStorage.getItem('kiosk_allowed_services') || 'null')
    if (Array.isArray(cached) && cached.length) {
      services.value = cached
      return
    }
    const data = await request('/api/services')
    services.value = data.services || []
    if (!services.value.length) {
      services.value = [
        { id: 1, code: 'PROOF_RESIDENCY', name: 'Proof of residency' },
        { id: 2, code: 'INDIGENCY', name: 'Barangay Indigency' },
        { id: 3, code: 'INCOME_LOAN', name: 'Loan of income' },
        { id: 4, code: 'SOLO_PARENT', name: 'Certificate of Solo parent' },
        { id: 5, code: 'SPECIAL_PERMIT', name: 'Special permit' },
        { id: 6, code: 'BUILDING_PERMIT', name: 'Building permit' },
      ]
    }
  } catch (err) {
    error.value = err.message
    services.value = [
      { id: 1, code: 'PROOF_RESIDENCY', name: 'Proof of residency' },
      { id: 2, code: 'INDIGENCY', name: 'Barangay Indigency' },
      { id: 3, code: 'INCOME_LOAN', name: 'Loan of income' },
      { id: 4, code: 'SOLO_PARENT', name: 'Certificate of Solo parent' },
      { id: 5, code: 'SPECIAL_PERMIT', name: 'Special permit' },
      { id: 6, code: 'BUILDING_PERMIT', name: 'Building permit' },
    ]
  }
}

const selectService = (service, event) => {
  triggerRipple(event)
  const positions = capturePositions()
  const next = new Set(selectedServiceIds.value)
  if (next.has(service.id)) {
    next.delete(service.id)
  } else {
    next.add(service.id)
  }
  selectedServiceIds.value = Array.from(next)
  expandedServiceId.value = service.id
  if (!selectedServiceIds.value.length) {
    expandedServiceId.value = null
    showConfirm.value = false
  }
  showReminder.value = false
  animateReflow(positions)
}

const triggerRipple = (event) => {
  const card = event?.currentTarget
  if (!card || !event) return
  const rect = card.getBoundingClientRect()
  const x = event.clientX - rect.left
  const y = event.clientY - rect.top
  card.style.setProperty('--ripple-x', `${x}px`)
  card.style.setProperty('--ripple-y', `${y}px`)
  card.classList.remove('is-rippling')
  void card.offsetWidth
  card.classList.add('is-rippling')
  window.setTimeout(() => {
    card.classList.remove('is-rippling')
  }, 650)
}

const refreshCardSpans = (duration = 900) => {
  const start = performance.now()
  const tick = () => {
    cardElements.forEach((element) => updateCardSpan(element))
    if (performance.now() - start < duration) {
      requestAnimationFrame(tick)
    }
  }
  requestAnimationFrame(tick)
}

watch(
  () => selectedServiceIds.value,
  async () => {
    await nextTick()
    refreshCardSpans()
  }
)


const handleProceed = () => {
  if (!selectedServices.value.length) {
    showReminder.value = true
    return
  }
  openConfirm()
}

const handleBack = () => {
  router.back()
}

const openConfirm = () => {
  if (!selectedServices.value.length) return
  showConfirm.value = true
}

const closeConfirm = () => {
  showConfirm.value = false
}

const closeReminder = () => {
  showReminder.value = false
}

const confirmProceed = async () => {
  if (!selectedServices.value.length) return
  if (!resident) {
    error.value = 'Missing resident information.'
    return
  }
  isCreatingTicket.value = true
  error.value = ''
  try {
    const tickets = []
    for (const service of selectedServices.value) {
      const idempotencyKey =
        crypto.randomUUID?.() || `${Date.now()}-${resident.id}-${service.id}`
      const data = await request('/api/kiosk/tickets', {
        method: 'POST',
        body: JSON.stringify({
          resident_id: resident.id,
          service_id: service.id,
          kiosk_device_id: 1,
          idempotency_key: idempotencyKey,
        }),
      })
      tickets.push(data.ticket)
    }
    const requirementsByService = selectedServices.value.map((service) =>
      getServiceMeta(service).requirements.map(text)
    )
    localStorage.setItem('kiosk_tickets', JSON.stringify(tickets))
    localStorage.setItem('kiosk_services', JSON.stringify(selectedServices.value))
    localStorage.setItem('kiosk_service_requirements', JSON.stringify(requirementsByService))
    if (tickets[0]) {
      localStorage.setItem('kiosk_ticket', JSON.stringify(tickets[0]))
    }
    if (selectedServices.value[0]) {
      localStorage.setItem('kiosk_service', JSON.stringify(selectedServices.value[0]))
    }
    showConfirm.value = false
    router.push('/ticket')
  } catch (err) {
    error.value = err.message
  } finally {
    isCreatingTicket.value = false
  }
}

onMounted(() => {
  triggerStepFlash()
  loadServices()
})

onBeforeUnmount(() => {
  resizeObserver?.disconnect()
  if (stepFlashTimer.value) {
    clearTimeout(stepFlashTimer.value)
  }
  if (stageRevealTimer.value) {
    clearTimeout(stageRevealTimer.value)
  }
})
</script>
