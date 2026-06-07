<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/api'
import { useWhatsApp } from '@/composables/useWhatsApp'

const route = useRoute()
const { linkSolicitud } = useWhatsApp()

const solicitud = ref(null)
const mensajes  = ref([])
const nuevoMsg  = ref('')
const enviando  = ref(false)
const cargando  = ref(true)

const estadoBadge = {
  recibida:   { text: 'Recibida',   class: 'bg-blue-900/40 text-blue-300' },
  en_proceso: { text: 'En proceso', class: 'bg-yellow-900/40 text-odi-amarillo' },
  cotizada:   { text: 'Cotizada',   class: 'bg-purple-900/40 text-purple-300' },
  aprobada:   { text: 'Aprobada',   class: 'bg-green-900/40 text-green-400' },
  rechazada:  { text: 'Rechazada',  class: 'bg-red-900/40 text-red-400' },
  completada: { text: 'Completada', class: 'bg-odi-gris2 text-odi-muted' },
}

async function cargarEstado() {
  const { data } = await api.get(`/solicitudes/${route.params.id}/estado`)
  if (solicitud.value) {
    solicitud.value.estado       = data.data.estado
    solicitud.value.precio_final = data.data.precio_final
  }
}

async function cargarMensajes() {
  const { data } = await api.get(`/solicitudes/${route.params.id}/mensajes`)
  mensajes.value = data.data
}

async function enviarMensaje() {
  if (!nuevoMsg.value.trim()) return
  enviando.value = true
  try {
    await api.post(`/solicitudes/${route.params.id}/mensajes`, { mensaje: nuevoMsg.value })
    nuevoMsg.value = ''
    await cargarMensajes()
  } catch {} finally { enviando.value = false }
}

let pollingId
onMounted(async () => {
  try {
    const { data } = await api.get(`/solicitudes/${route.params.id}/estado`)
    solicitud.value = { id: route.params.id, ...data.data }
    await cargarMensajes()
  } catch {} finally { cargando.value = false }

  pollingId = setInterval(async () => {
    await cargarMensajes()
    await cargarEstado()
  }, 5000)
})

onUnmounted(() => clearInterval(pollingId))
</script>

<template>
  <div class="max-w-3xl mx-auto px-4 py-10">
    <div v-if="cargando" class="text-odi-muted text-center py-20">Cargando...</div>

    <template v-else-if="solicitud">
      <!-- Header -->
      <div class="flex items-start justify-between gap-4 mb-8">
        <div>
          <h1 class="text-2xl font-display font-bold text-odi-texto">Solicitud #{{ solicitud.id.slice(-8) }}</h1>
          <div class="flex items-center gap-3 mt-2">
            <span :class="['badge', estadoBadge[solicitud.estado]?.class || 'bg-odi-gris2 text-odi-muted']">
              {{ estadoBadge[solicitud.estado]?.text || solicitud.estado }}
            </span>
            <span v-if="solicitud.precio_final" class="text-odi-amarillo font-bold">
              Cotización: ${{ Number(solicitud.precio_final).toLocaleString('es-CO') }}
            </span>
          </div>
        </div>
        <a :href="linkSolicitud(solicitud.id)" target="_blank" class="btn-ghost text-sm py-2 shrink-0">
          WhatsApp
        </a>
      </div>

      <!-- Chat -->
      <div class="card">
        <h2 class="text-sm font-semibold text-odi-muted mb-4 uppercase tracking-wide">Mensajes</h2>

        <!-- Mensajes -->
        <div class="flex flex-col gap-3 min-h-[200px] max-h-96 overflow-y-auto mb-4 pr-1">
          <div v-if="mensajes.length === 0" class="text-odi-muted text-sm text-center py-8">
            Sin mensajes. Escribe para iniciar la conversación.
          </div>
          <div v-for="m in mensajes" :key="m.id"
            :class="['flex', m.remitente === 'cliente' ? 'justify-end' : 'justify-start']">
            <div :class="['max-w-[75%] rounded-2xl px-4 py-2.5 text-sm',
              m.remitente === 'cliente'
                ? 'bg-odi-amarillo text-odi-negro rounded-br-sm'
                : 'bg-odi-gris2 text-odi-texto rounded-bl-sm']">
              <p>{{ m.mensaje }}</p>
              <p :class="['text-[10px] mt-1 text-right',
                m.remitente === 'cliente' ? 'text-odi-negro/60' : 'text-odi-muted']">
                {{ new Date(m.fecha).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' }) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Input -->
        <div class="flex gap-2 pt-3 border-t border-odi-gris2">
          <input v-model="nuevoMsg" @keyup.enter="enviarMensaje" type="text"
            placeholder="Escribe un mensaje..." class="input-field flex-1" />
          <button @click="enviarMensaje" :disabled="enviando || !nuevoMsg.trim()" class="btn-primary px-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
          </button>
        </div>
      </div>
    </template>
  </div>
</template>
