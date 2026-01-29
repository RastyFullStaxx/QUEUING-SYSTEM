<template>
  <div class="min-h-screen page-shell">
    <Login v-if="showLanding" class="page-view" />
    <router-view v-else v-slot="{ Component }">
      <transition name="page-fade" mode="out-in">
        <component :is="Component" class="page-view" />
      </transition>
    </router-view>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Login from './pages/Login.vue'

const route = useRoute()
const showLanding = computed(() => ['/', '/welcome', '/login'].includes(route.path))
</script>
