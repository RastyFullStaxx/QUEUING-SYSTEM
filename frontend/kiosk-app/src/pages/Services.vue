<template>
  <div class="min-h-screen kiosk-service-stage" @click="handleStageClick">
    <div
      class="relative z-10 min-h-screen flex items-center justify-center px-6 py-10 kiosk-service-body"
      :class="{ 'kiosk-dim': showConfirm || showReminder }"
    >
      <div class="w-full max-w-none mx-auto grid gap-10">
        <div class="kiosk-service-hero kiosk-fade">
          <div class="kiosk-step-header">
            <div class="kiosk-pill">
              <span class="scan-dot"></span>
              {{ labels.stepBadge }}
            </div>
          </div>
          <div class="kiosk-service-hero-slot">
            <transition name="kiosk-panel" mode="out-in">
              <div v-if="selectedService" class="kiosk-service-hero-cta" :style="ctaStyle">
                <p class="kiosk-service-hero-kicker">{{ labels.selectedLabel }}</p>
                <h1 class="kiosk-service-hero-title">{{ selectedService.name }}</h1>
                <p class="kiosk-service-hero-note">{{ labels.selectedNote }}</p>
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
            :class="{ 'is-disabled': !selectedService }"
            type="button"
            :aria-disabled="!selectedService"
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
        <div class="kiosk-modal-card kiosk-modal-glow service-confirm-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <div class="service-confirm-header">
            <p class="service-confirm-kicker">{{ labels.confirmKicker }}</p>
            <h2 class="service-confirm-title">{{ labels.confirmTitle }}</h2>
            <p class="service-confirm-subtitle">{{ labels.confirmSubtitle }}</p>
          </div>
          <div class="service-confirm-panel">
            <div class="service-confirm-check" aria-hidden="true">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12l4 4 10-10" />
              </svg>
            </div>
            <div class="service-confirm-chip">
              <span>{{ labels.confirmChip }}</span>
            </div>
            <div class="service-confirm-name">{{ selectedService?.name }}</div>
            <div class="service-confirm-note">
              <span>{{ labels.confirmNote }}</span>
            </div>
          </div>
          <div class="service-confirm-actions">
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
    </transition>
    <transition name="kiosk-modal">
      <div v-if="showReminder" class="kiosk-modal" @click.self="closeReminder">
        <div class="kiosk-modal-card kiosk-modal-glow service-reminder-card">
          <span class="modal-orb orb-one" aria-hidden="true"></span>
          <span class="modal-orb orb-two" aria-hidden="true"></span>
          <p class="service-reminder-kicker">{{ labels.reminderKicker }}</p>
          <h2 class="service-reminder-title">{{ labels.reminderTitle }}</h2>
          <p class="service-reminder-body">{{ labels.reminderBody }}</p>
          <button class="mt-6 w-full kiosk-button text-lg py-3 rounded-2xl kiosk-action" type="button" @click="closeReminder">
            {{ labels.reminderButton }}
          </button>
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
const selectedServiceId = ref(null)
const hoveredServiceId = ref(null)
const showConfirm = ref(false)
const showReminder = ref(false)
const cardSpans = ref({})
const cardElements = new Map()
const resident = JSON.parse(localStorage.getItem('kiosk_resident') || 'null')

const copy = {
  en: {
    stepBadge: 'Kiosk Scan - Step 2 of 3',
    title: 'Select Service',
    subtitle: 'Tap a panel to expand and view details before choosing.',
    hint: 'Hover to preview, tap to select.',
    selected: 'Selected',
    selectedLabel: 'Selected service',
    selectedNote: 'Review the details below before proceeding.',
    cardHint: 'Tap for details',
    back: 'Go back',
    proceed: 'Proceed',
    requirements: 'Requirements',
    emptyTitle: 'No services available',
    emptyBody: 'Please ask staff for assistance.',
    confirmKicker: 'Confirmation',
    confirmTitle: 'Proceed with this service?',
    confirmSubtitle: 'Review your choice before continuing.',
    confirmChip: 'You selected',
    confirmNote: 'Please confirm to continue to Step 3.',
    change: 'Change selection',
    confirm: 'Confirm & Continue',
    reminderKicker: 'Reminder',
    reminderTitle: 'Select a service first',
    reminderBody: 'Please choose a service before proceeding.',
    reminderButton: 'Got it',
  },
  tl: {
    stepBadge: 'Kiosk Scan - Hakbang 2 sa 3',
    title: 'Piliin ang Serbisyo',
    subtitle: 'I-tap ang panel para buksan at makita ang detalye bago pumili.',
    hint: 'I-hover para makita, i-tap para piliin.',
    selected: 'Napili',
    selectedLabel: 'Napiling serbisyo',
    selectedNote: 'Suriin ang detalye bago magpatuloy.',
    cardHint: 'I-tap para sa detalye',
    back: 'Bumalik',
    proceed: 'Magpatuloy',
    requirements: 'Kailangan',
    emptyTitle: 'Walang serbisyong available',
    emptyBody: 'Mangyaring humingi ng tulong sa staff.',
    confirmKicker: 'Kumpirmasyon',
    confirmTitle: 'Magpatuloy sa serbisyong ito?',
    confirmSubtitle: 'Suriin muna ang napili bago magpatuloy.',
    confirmChip: 'Napili mo',
    confirmNote: 'Kumpirmahin para magpatuloy sa Hakbang 3.',
    change: 'Palitan ang pili',
    confirm: 'Kumpirmahin at Magpatuloy',
    reminderKicker: 'Paalala',
    reminderTitle: 'Pumili muna ng serbisyo',
    reminderBody: 'Mangyaring pumili ng serbisyo bago magpatuloy.',
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

const serviceMeta = {
  BC: {
    icon: 'shield',
    accent: '#0B2C6F',
    accentSoft: 'rgba(11, 44, 111, 0.16)',
    accentGlow: 'rgba(11, 44, 111, 0.32)',
    tagline: {
      en: 'Get a barangay clearance for jobs, travel, and official paperwork.',
      tl: 'Kunin ang barangay clearance para sa trabaho, biyahe, at dokumento.',
    },
    summary: {
      en: 'We will check your records, guide the steps, and release it the same day.',
      tl: 'Sasala ang rekord, may gabay na proseso, at kadalasang same day.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '15-25 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window A' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Proof of address', tl: 'Patunay ng tirahan' },
    ],
  },
  BP: {
    icon: 'briefcase',
    accent: '#185ADB',
    accentSoft: 'rgba(24, 90, 219, 0.16)',
    accentGlow: 'rgba(24, 90, 219, 0.32)',
    tagline: {
      en: 'Apply or renew your business permit with clear, guided steps.',
      tl: 'Mag-apply o mag-renew ng business permit na may malinaw na gabay.',
    },
    summary: {
      en: 'Submit business details, verify requirements, and get quick processing.',
      tl: 'Ihanda ang detalye, i-verify ang requirements, at mabilis ang proseso.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '20-30 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window B' },
      { label: { en: 'Form', tl: 'Porma' }, value: 'BP-01' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Business details', tl: 'Detalye ng negosyo' },
    ],
  },
  RC: {
    icon: 'home',
    accent: '#1C7C54',
    accentSoft: 'rgba(28, 124, 84, 0.16)',
    accentGlow: 'rgba(28, 124, 84, 0.32)',
    tagline: {
      en: 'Confirm your residency for school, work, and ID applications.',
      tl: 'Patunay ng paninirahan para sa paaralan, trabaho, at pagkuha ng ID.',
    },
    summary: {
      en: 'We verify your address and issue a residency certificate promptly.',
      tl: 'Bine-verify ang address at mabilis na naglalabas ng sertipikasyon.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '10-15 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window C' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Same day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Barangay form', tl: 'Barangay form' },
    ],
  },
  HC: {
    icon: 'heart',
    accent: '#D84343',
    accentSoft: 'rgba(216, 67, 67, 0.16)',
    accentGlow: 'rgba(216, 67, 67, 0.32)',
    tagline: {
      en: 'Health clearance for employment, school, and compliance needs.',
      tl: 'Health clearance para sa trabaho, paaralan, at compliance.',
    },
    summary: {
      en: 'Follow guided checks and receive your health certificate after review.',
      tl: 'May gabay na pagsusuri at makukuha ang health certificate pagkatapos.',
    },
    stats: [
      { label: { en: 'Est. time', tl: 'Tantyang oras' }, value: '25-35 min' },
      { label: { en: 'Counter', tl: 'Counter' }, value: 'Window D' },
      { label: { en: 'Release', tl: 'Paglabas' }, value: 'Next day' },
    ],
    requirements: [
      { en: 'Valid ID', tl: 'Balidong ID' },
      { en: 'Medical history', tl: 'Kasaysayan ng kalusugan' },
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

const selectedService = computed(() =>
  services.value.find((service) => service.id === selectedServiceId.value)
)

const getServiceMeta = (service) => {
  const code = (service?.code || '').toUpperCase()
  return serviceMeta[code] || defaultMeta
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
  selectedService.value ? getServiceMeta(selectedService.value) : null
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

const isSelected = (serviceId) => selectedServiceId.value === serviceId

const isExpanded = (serviceId) => {
  if (selectedServiceId.value) {
    return selectedServiceId.value === serviceId
  }
  return hoveredServiceId.value === serviceId
}

const setHovered = (serviceId) => {
  if (selectedServiceId.value) return
  const positions = capturePositions()
  hoveredServiceId.value = serviceId
  animateReflow(positions)
}

const clearHovered = () => {
  if (selectedServiceId.value) return
  const positions = capturePositions()
  hoveredServiceId.value = null
  animateReflow(positions)
}

const clearSelection = () => {
  if (!selectedServiceId.value) return
  selectedServiceId.value = null
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
  } catch (err) {
    error.value = err.message
  }
}

const selectService = (service, event) => {
  triggerRipple(event)
  const positions = capturePositions()
  selectedServiceId.value = selectedServiceId.value === service.id ? null : service.id
  if (!selectedServiceId.value) {
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
  () => selectedServiceId.value,
  async () => {
    await nextTick()
    refreshCardSpans()
  }
)


const handleProceed = () => {
  if (!selectedService.value) {
    showReminder.value = true
    return
  }
  openConfirm()
}

const handleBack = () => {
  router.back()
}

const openConfirm = () => {
  if (!selectedService.value) return
  showConfirm.value = true
}

const closeConfirm = () => {
  showConfirm.value = false
}

const closeReminder = () => {
  showReminder.value = false
}

const confirmProceed = async () => {
  if (!selectedService.value) return
  if (!resident) {
    error.value = 'Missing resident information.'
    return
  }
  isCreatingTicket.value = true
  error.value = ''
  try {
    const idempotencyKey =
      crypto.randomUUID?.() || `${Date.now()}-${resident.id}-${selectedService.value.id}`
    const data = await request('/api/kiosk/tickets', {
      method: 'POST',
      body: JSON.stringify({
        resident_id: resident.id,
        service_id: selectedService.value.id,
        kiosk_device_id: 1,
        idempotency_key: idempotencyKey,
      }),
    })
    localStorage.setItem('kiosk_ticket', JSON.stringify(data.ticket))
    localStorage.setItem('kiosk_service', JSON.stringify(selectedService.value))
    localStorage.setItem(
      'kiosk_service_requirements',
      JSON.stringify(getServiceMeta(selectedService.value).requirements.map(text))
    )
    showConfirm.value = false
    router.push('/ticket')
  } catch (err) {
    error.value = err.message
  } finally {
    isCreatingTicket.value = false
  }
}

onMounted(() => {
  loadServices()
})

onBeforeUnmount(() => {
  resizeObserver?.disconnect()
})
</script>
