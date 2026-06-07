<script setup>
import { RouterView } from 'vue-router'
import NavBar from '@/components/NavBar.vue'
</script>

<template>
  <div class="min-h-screen bg-odi-negro">
    <NavBar />
    <RouterView />
  </div>
</template>
