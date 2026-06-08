<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/api'
import { useWhatsApp } from '@/composables/useWhatsApp'
import VisorArchivo from '@/components/VisorArchivo.vue'

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
    solicitud.value.estado                 = data.data.estado
    solicitud.value.precio_final           = data.data.precio_final
    solicitud.value.fecha_estimada_entrega = data.data.fecha_estimada_entrega
  }
}

async function cargarMensajes() {
  const { data } = await api.get(`/solicitudes/${route.params.id}/mensajes`)
  mensajes.value = [...data.data].sort((a, b) => new Date(a.fecha) - new Date(b.fecha))
}

// ── Edición de solicitud (solo mientras está "recibida") ──────
const editando        = ref(false)
const formEdicion     = ref({ descripcion: '', material_preferido: '', cantidad: 1, uso_final: '' })
const archivosNuevos  = ref([])
const arrastreEdicion = ref(false)
const guardando       = ref(false)
const errorEdicion    = ref('')
const eliminandoArchivo = ref(null)

function iniciarEdicion() {
  formEdicion.value = {
    descripcion:        solicitud.value.descripcion,
    material_preferido: solicitud.value.material_preferido || '',
    cantidad:           solicitud.value.cantidad || 1,
    uso_final:          solicitud.value.uso_final || '',
  }
  archivosNuevos.value = []
  errorEdicion.value = ''
  editando.value = true
}
function cancelarEdicion() { editando.value = false }

function agregarArchivosNuevos(files) {
  const espacio = 5 - (solicitud.value.archivos?.length || 0) - archivosNuevos.value.length
  if (espacio <= 0) return
  archivosNuevos.value = [...archivosNuevos.value, ...files.slice(0, espacio)]
}
function onDropEdicion(e) {
  arrastreEdicion.value = false
  agregarArchivosNuevos(Array.from(e.dataTransfer?.files || []))
}
function onFileInputEdicion(e) {
  agregarArchivosNuevos(Array.from(e.target.files))
}
function quitarArchivoNuevo(i) { archivosNuevos.value.splice(i, 1) }

async function eliminarArchivoExistente(archivoId) {
  if (!confirm('¿Eliminar este archivo de la solicitud?')) return
  eliminandoArchivo.value = archivoId
  try {
    await api.delete(`/solicitudes/${solicitud.value.id}/archivos/${archivoId}`)
    solicitud.value.archivos = solicitud.value.archivos.filter(a => a.id !== archivoId)
  } catch (e) {
    alert(e.response?.data?.error || 'No se pudo eliminar el archivo.')
  } finally {
    eliminandoArchivo.value = null
  }
}

async function guardarEdicion() {
  errorEdicion.value = ''
  guardando.value = true
  try {
    const fd = new FormData()
    fd.append('_method', 'PUT')
    Object.entries(formEdicion.value).forEach(([k, v]) => fd.append(k, v))
    archivosNuevos.value.forEach(f => fd.append('archivos[]', f))

    await api.post(`/solicitudes/${solicitud.value.id}`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    const { data } = await api.get(`/solicitudes/${solicitud.value.id}`)
    solicitud.value = data.data
    editando.value = false
  } catch (e) {
    const errs = e.response?.data?.errors
    errorEdicion.value = errs
      ? Object.values(errs).flat().join(' ')
      : (e.response?.data?.error || e.response?.data?.message || 'Error al guardar los cambios.')
  } finally {
    guardando.value = false
  }
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
    const { data } = await api.get(`/solicitudes/${route.params.id}`)
    solicitud.value = data.data
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
          <p v-if="solicitud.fecha_estimada_entrega" class="text-sm text-odi-amarillo mt-2">
            📅 Entrega estimada: {{ new Date(solicitud.fecha_estimada_entrega).toLocaleDateString('es-CO', { timeZone: 'UTC' }) }}
          </p>
        </div>
        <a :href="linkSolicitud(solicitud.id)" target="_blank" class="btn-ghost text-sm py-2 shrink-0">
          WhatsApp
        </a>
      </div>

      <!-- Detalle de la solicitud -->
      <div class="card mb-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-odi-muted uppercase tracking-wide">Detalle de la solicitud</h2>
          <button v-if="solicitud.estado === 'recibida' && !editando" type="button" @click="iniciarEdicion"
            class="text-xs text-odi-amarillo hover:underline transition-colors duration-200">
            ✏️ Editar
          </button>
        </div>

        <template v-if="!editando">
          <p class="text-odi-texto text-sm whitespace-pre-wrap">{{ solicitud.descripcion }}</p>
          <div class="flex flex-wrap gap-x-6 gap-y-1 mt-3 text-xs text-odi-muted">
            <span v-if="solicitud.material_preferido">Material: <span class="text-odi-texto">{{ solicitud.material_preferido }}</span></span>
            <span v-if="solicitud.cantidad">Cantidad: <span class="text-odi-texto">{{ solicitud.cantidad }}</span></span>
            <span v-if="solicitud.uso_final">Uso final: <span class="text-odi-texto">{{ solicitud.uso_final }}</span></span>
          </div>

          <div v-if="solicitud.archivos?.length" class="mt-4">
            <p class="text-xs font-semibold text-odi-muted uppercase tracking-wide mb-2">Archivos adjuntos</p>
            <div class="flex flex-col gap-2">
              <VisorArchivo v-for="a in solicitud.archivos" :key="a.id" :archivo="a" />
            </div>
          </div>
        </template>

        <form v-else @submit.prevent="guardarEdicion" class="flex flex-col gap-4 mt-1">
          <div>
            <label class="block text-xs font-medium text-odi-muted mb-1.5">Descripción del proyecto *</label>
            <textarea v-model="formEdicion.descripcion" rows="4" required class="input-field resize-none" />
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-odi-muted mb-1.5">Material preferido</label>
              <select v-model="formEdicion.material_preferido" class="input-field">
                <option value="">Sin preferencia</option>
                <option value="PLA">PLA</option>
                <option value="PETG">PETG</option>
                <option value="ABS">ABS</option>
                <option value="Resina">Resina</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-odi-muted mb-1.5">Cantidad</label>
              <input v-model.number="formEdicion.cantidad" type="number" min="1" class="input-field" />
            </div>
          </div>
          <div>
            <label class="block text-xs font-medium text-odi-muted mb-1.5">Uso final</label>
            <input v-model="formEdicion.uso_final" type="text" class="input-field" />
          </div>

          <!-- Archivos existentes -->
          <div v-if="solicitud.archivos?.length">
            <label class="block text-xs font-medium text-odi-muted mb-1.5">Archivos actuales</label>
            <div class="flex flex-col gap-1.5">
              <div v-for="a in solicitud.archivos" :key="a.id" class="relative">
                <VisorArchivo :archivo="a" />
                <button type="button" @click="eliminarArchivoExistente(a.id)" :disabled="eliminandoArchivo === a.id"
                  class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center transition-colors duration-200 hover:bg-red-600 disabled:opacity-50">
                  ✕
                </button>
              </div>
            </div>
          </div>

          <!-- Agregar archivos nuevos -->
          <div v-if="(solicitud.archivos?.length || 0) + archivosNuevos.length < 5">
            <label class="block text-xs font-medium text-odi-muted mb-1.5">Agregar archivos (STL, OBJ, imágenes, PDF — máx 5 en total)</label>
            <div
              @dragover.prevent="arrastreEdicion = true"
              @dragleave="arrastreEdicion = false"
              @drop.prevent="onDropEdicion"
              :class="['border-2 border-dashed rounded-xl p-6 text-center transition-colors duration-200 cursor-pointer',
                arrastreEdicion ? 'border-odi-amarillo bg-odi-amarillo/5' : 'border-odi-gris2 hover:border-odi-muted']"
              @click="$refs.fileInputEdicion.click()">
              <p class="text-odi-muted text-sm">Arrastra archivos aquí o <span class="text-odi-amarillo">haz clic</span> para seleccionar</p>
              <input ref="fileInputEdicion" type="file" multiple accept=".stl,.obj,.3mf,.jpg,.jpeg,.png,.pdf"
                class="hidden" @change="onFileInputEdicion" />
            </div>
            <div v-if="archivosNuevos.length" class="mt-2 flex flex-col gap-1.5">
              <VisorArchivo v-for="(f, i) in archivosNuevos" :key="i" :archivo="f" eliminable @eliminar="quitarArchivoNuevo(i)" />
            </div>
          </div>

          <p v-if="errorEdicion" class="text-red-400 text-sm">{{ errorEdicion }}</p>

          <div class="flex gap-3">
            <button type="submit" :disabled="guardando" class="btn-primary px-5 py-2.5 text-sm">
              {{ guardando ? 'Guardando...' : 'Guardar cambios' }}
            </button>
            <button type="button" @click="cancelarEdicion" class="btn-ghost px-5 py-2.5 text-sm">Cancelar</button>
          </div>
        </form>
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
