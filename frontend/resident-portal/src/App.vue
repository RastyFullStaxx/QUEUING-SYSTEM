<template>
  <div class="min-h-screen page-shell">
    <router-view v-slot="{ Component }">
      <transition name="page-fade" mode="out-in">
        <component :is="Component" class="page-view" />
      </transition>
    </router-view>
  </div>
</template>
