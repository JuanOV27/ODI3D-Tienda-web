<script setup>
import { ref, computed, onBeforeUnmount } from 'vue'
import api from '@/lib/api'
import Visor3D from './Visor3D.vue'

// `archivo` puede ser:
//  - un File local (aún no subido, p.ej. en el formulario o modo edición)
//  - un objeto remoto { id, nombre_original, tipo_mime, url } ya subido
const props = defineProps({
  archivo: { type: [File, Object], required: true },
  eliminable: { type: Boolean, default: false },
})
const emit = defineEmits(['eliminar'])

const EXTENSIONES_IMAGEN = ['jpg', 'jpeg', 'png', 'gif', 'webp']
const EXTENSIONES_3D = ['stl', 'obj']

const nombre = computed(() => (props.archivo instanceof File ? props.archivo.name : props.archivo.nombre_original))
const extension = computed(() => {
  const partes = nombre.value.split('.')
  return partes.length > 1 ? partes.pop().toLowerCase() : ''
})
const tipo = computed(() => {
  if (EXTENSIONES_IMAGEN.includes(extension.value)) return 'imagen'
  if (EXTENSIONES_3D.includes(extension.value)) return 'modelo3d'
  return 'otro'
})
const urlDescarga = computed(() => (props.archivo instanceof File ? null : props.archivo.url))

const cargando = ref(false)
const error = ref('')
const urlImagen = ref(null)
const bufferModelo = ref(null)
const modalAbierto = ref(false)
let objectUrlPropio = null

async function obtenerBlob() {
  if (props.archivo instanceof File) return props.archivo
  const { data } = await api.get(props.archivo.url, { responseType: 'blob' })
  return data
}

async function preparar() {
  if (cargando.value || error.value) return
  if (tipo.value === 'imagen' && urlImagen.value) return
  if (tipo.value === 'modelo3d' && bufferModelo.value) return
  if (tipo.value === 'otro') return

  cargando.value = true
  try {
    const blob = await obtenerBlob()
    if (tipo.value === 'imagen') {
      objectUrlPropio = URL.createObjectURL(blob)
      urlImagen.value = objectUrlPropio
    } else if (tipo.value === 'modelo3d') {
      bufferModelo.value = await blob.arrayBuffer()
    }
  } catch (e) {
    error.value = 'No se pudo cargar la vista previa.'
  } finally {
    cargando.value = false
  }
}

function abrirModal() {
  modalAbierto.value = true
  preparar()
}

if (tipo.value === 'imagen') preparar()

onBeforeUnmount(() => {
  if (objectUrlPropio) URL.revokeObjectURL(objectUrlPropio)
})
</script>

<template>
  <div class="relative">
    <button type="button" @click="abrirModal"
      class="flex items-center gap-2 bg-odi-gris2 hover:bg-odi-gris2/70 rounded-lg overflow-hidden transition-colors duration-200 text-left w-full pr-3">
      <div class="w-11 h-11 shrink-0 flex items-center justify-center bg-odi-negro/50 overflow-hidden">
        <img v-if="tipo === 'imagen' && urlImagen" :src="urlImagen" class="w-full h-full object-cover" alt="" />
        <span v-else-if="tipo === 'imagen'" class="text-lg">🖼️</span>
        <span v-else-if="tipo === 'modelo3d'" class="text-lg">🧊</span>
        <span v-else class="text-lg">📎</span>
      </div>
      <span class="text-sm text-odi-texto truncate">{{ nombre }}</span>
    </button>
    <button v-if="eliminable" type="button" @click.stop="emit('eliminar')"
      class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center transition-colors duration-200 hover:bg-red-600">
      ✕
    </button>

    <Teleport to="body">
      <div v-if="modalAbierto" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
        @click.self="modalAbierto = false">
        <div class="bg-odi-gris rounded-xl max-w-3xl w-full max-h-[85vh] flex flex-col overflow-hidden border border-odi-gris2">
          <div class="flex items-center justify-between px-4 py-3 border-b border-odi-gris2">
            <span class="text-sm font-medium text-odi-texto truncate">{{ nombre }}</span>
            <button type="button" @click="modalAbierto = false"
              class="text-odi-muted hover:text-odi-texto transition-colors duration-200 px-2">✕</button>
          </div>
          <div class="flex-1 min-h-[320px] flex items-center justify-center p-4 overflow-auto">
            <p v-if="cargando" class="text-odi-muted text-sm">Cargando vista previa...</p>
            <p v-else-if="error" class="text-red-400 text-sm">{{ error }}</p>
            <img v-else-if="tipo === 'imagen' && urlImagen" :src="urlImagen"
              class="max-w-full max-h-[70vh] object-contain rounded" alt="" />
            <div v-else-if="tipo === 'modelo3d' && bufferModelo" class="w-full h-[60vh]">
              <Visor3D :buffer="bufferModelo" :extension="extension" />
            </div>
            <div v-else class="text-odi-muted text-sm text-center">
              <p>Vista previa no disponible para este tipo de archivo.</p>
              <a v-if="urlDescarga" :href="urlDescarga" target="_blank"
                class="text-odi-amarillo hover:underline inline-block mt-2">Descargar archivo</a>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
