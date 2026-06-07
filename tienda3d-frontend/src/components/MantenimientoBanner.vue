<script setup>
defineProps({ mensaje: { type: String, default: 'Este módulo está temporalmente en mantenimiento.' } })
</script>

<template>
  <div class="flex flex-col items-center justify-center py-32 px-6 text-center">
    <div class="text-5xl mb-6">🔧</div>
    <h2 class="text-2xl font-display font-bold text-odi-texto mb-3">En mantenimiento</h2>
    <p class="text-odi-muted max-w-md">{{ mensaje }}</p>
  </div>
</template>
