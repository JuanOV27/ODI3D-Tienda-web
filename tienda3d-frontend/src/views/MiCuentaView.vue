<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '@/lib/api'
import { useAuthStore } from '@/stores/auth'

const auth       = useAuthStore()
const solicitudes = ref([])
const cargando   = ref(true)

const estadoBadge = {
  recibida:   { text: 'Recibida',   class: 'bg-blue-900/40 text-blue-300' },
  en_proceso: { text: 'En proceso', class: 'bg-yellow-900/40 text-odi-amarillo' },
  cotizada:   { text: 'Cotizada',   class: 'bg-purple-900/40 text-purple-300' },
  aprobada:   { text: 'Aprobada',   class: 'bg-green-900/40 text-green-400' },
  rechazada:  { text: 'Rechazada',  class: 'bg-red-900/40 text-red-400' },
  completada: { text: 'Completada', class: 'bg-odi-gris2 text-odi-muted' },
}

onMounted(async () => {
  try {
    const { data } = await api.get('/solicitudes')
    solicitudes.value = data.data
  } catch {} finally { cargando.value = false }
})
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-display font-bold text-odi-texto mb-1">Mi cuenta</h1>
    <p class="text-odi-muted mb-8">Hola, <span class="text-odi-texto">{{ auth.cliente?.nombre }}</span></p>

    <!-- Acciones rápidas -->
    <div class="grid sm:grid-cols-2 gap-4 mb-10">
      <RouterLink to="/solicitudes" class="card hover:bg-odi-gris2 flex items-center gap-4">
        <span class="text-3xl">📐</span>
        <div>
          <p class="font-semibold text-odi-texto">Nueva solicitud</p>
          <p class="text-odi-muted text-sm">Cotiza un proyecto nuevo</p>
        </div>
      </RouterLink>
      <RouterLink to="/catalogo" class="card hover:bg-odi-gris2 flex items-center gap-4">
        <span class="text-3xl">🛍</span>
        <div>
          <p class="font-semibold text-odi-texto">Ver catálogo</p>
          <p class="text-odi-muted text-sm">Productos listos para pedir</p>
        </div>
      </RouterLink>
    </div>

    <!-- Mis solicitudes -->
    <h2 class="text-lg font-display font-semibold text-odi-texto mb-4">Mis solicitudes</h2>
    <div v-if="cargando" class="text-odi-muted text-sm">Cargando...</div>
    <div v-else-if="solicitudes.length === 0" class="text-odi-muted text-sm">
      No tienes solicitudes. <RouterLink to="/solicitudes" class="text-odi-amarillo hover:underline">Crear una</RouterLink>
    </div>
    <div v-else class="flex flex-col gap-3">
      <RouterLink v-for="s in solicitudes" :key="s.id" :to="`/solicitudes/${s.id}`"
        class="card hover:bg-odi-gris2 flex items-center justify-between gap-4">
        <div class="min-w-0">
          <p class="text-sm font-medium text-odi-texto truncate">{{ s.descripcion }}</p>
          <p class="text-xs text-odi-muted mt-0.5">{{ new Date(s.fecha_solicitud).toLocaleDateString('es-CO') }}</p>
          <p v-if="s.fecha_estimada_entrega" class="text-xs text-odi-amarillo mt-0.5">
            📅 Entrega estimada: {{ new Date(s.fecha_estimada_entrega).toLocaleDateString('es-CO', { timeZone: 'UTC' }) }}
          </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <span v-if="s.precio_final" class="text-odi-amarillo font-bold text-sm">
            ${{ Number(s.precio_final).toLocaleString('es-CO') }}
          </span>
          <span :class="['badge', estadoBadge[s.estado]?.class || 'bg-odi-gris2 text-odi-muted']">
            {{ estadoBadge[s.estado]?.text || s.estado }}
          </span>
        </div>
      </RouterLink>
    </div>
  </div>
</template>
