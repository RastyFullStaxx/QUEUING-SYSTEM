<template>
  <div
    class="welcome-stage"
    role="button"
    tabindex="0"
    @click="goLogin"
    @keydown.enter="goLogin"
    @keydown.space.prevent="goLogin"
  >
    <div class="welcome-sky">
      <div class="welcome-sun" aria-hidden="true"></div>
      <div class="welcome-content">
        <img class="welcome-logo" src="/logo.png" alt="Barangay San Miguel logo" />
        <div class="welcome-text">
          <p class="welcome-kicker">Barangay San Miguel</p>
          <h1 class="welcome-title">Resident Portal</h1>
          <p class="welcome-subtitle">Services, updates, and queue access in one place.</p>
        </div>
      </div>
      <p class="welcome-tap">Tap anywhere to continue</p>
    </div>
    <img class="welcome-buildings" src="/buildings.png" alt="" aria-hidden="true" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const goLogin = () => {
  router.push('/login')
}
</script>

<style scoped>
.welcome-stage {
  position: relative;
  min-height: 100vh;
  background:
    radial-gradient(circle at 50% 10%, rgba(255, 248, 214, 0.95), rgba(255, 227, 150, 0.35) 35%, transparent 60%),
    linear-gradient(180deg, #cfe3ff 0%, #98b8f4 45%, #75a3eb 100%);
  overflow: hidden;
  cursor: pointer;
  outline: none;
}

.welcome-stage:focus-visible {
  box-shadow: 0 0 0 4px rgba(11, 44, 111, 0.35);
}

.welcome-sky {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 4rem 2rem 7rem;
  text-align: center;
  color: #0b2c6f;
}

.welcome-sun {
  position: absolute;
  top: -8rem;
  left: 50%;
  width: 38rem;
  height: 38rem;
  transform: translateX(-50%);
  border-radius: 999px;
  background: radial-gradient(
    circle at center,
    rgba(255, 255, 255, 0.95) 0%,
    rgba(255, 238, 186, 0.95) 28%,
    rgba(255, 214, 102, 0.6) 45%,
    rgba(255, 214, 102, 0.05) 68%,
    rgba(255, 214, 102, 0) 72%
  );
}

.welcome-sun::before {
  content: "";
  position: absolute;
  inset: -20%;
  border-radius: 999px;
  background: repeating-conic-gradient(
    from 0deg,
    rgba(255, 214, 102, 0.35) 0 12deg,
    rgba(255, 214, 102, 0) 12deg 24deg
  );
  -webkit-mask: radial-gradient(circle, rgba(0, 0, 0, 0.7) 0 45%, rgba(0, 0, 0, 0) 70%);
  mask: radial-gradient(circle, rgba(0, 0, 0, 0.7) 0 45%, rgba(0, 0, 0, 0) 70%);
  animation: sunspin 24s linear infinite;
}

.welcome-content {
  display: grid;
  gap: 1.5rem;
  justify-items: center;
}

.welcome-logo {
  width: 110px;
  height: 110px;
  object-fit: contain;
  filter: drop-shadow(0 16px 30px rgba(11, 44, 111, 0.25));
  animation: floatIn 0.8s ease-out;
}

.welcome-kicker {
  text-transform: uppercase;
  letter-spacing: 0.3em;
  font-size: 0.75rem;
  font-weight: 700;
  color: rgba(11, 44, 111, 0.7);
}

.welcome-title {
  font-size: clamp(2.4rem, 4vw, 3.8rem);
  font-weight: 800;
  line-height: 1.05;
  text-shadow: 0 18px 36px rgba(11, 44, 111, 0.18);
}

.welcome-subtitle {
  font-size: 1.05rem;
  color: rgba(11, 44, 111, 0.75);
}

.welcome-tap {
  margin-top: 2.5rem;
  font-weight: 600;
  font-size: 1rem;
  color: rgba(11, 44, 111, 0.9);
  letter-spacing: 0.08em;
  animation: pulseText 2.4s ease-in-out infinite;
}

.welcome-buildings {
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: auto;
  pointer-events: none;
  opacity: 0.95;
  z-index: 2;
}

@keyframes sunspin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes floatIn {
  0% {
    transform: translateY(12px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes pulseText {
  0%,
  100% {
    transform: translateY(0);
    opacity: 0.85;
  }
  50% {
    transform: translateY(6px);
    opacity: 1;
  }
}

@media (max-width: 640px) {
  .welcome-sun {
    width: 26rem;
    height: 26rem;
  }
}
</style>
