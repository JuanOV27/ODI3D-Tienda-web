<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/api'
import MantenimientoBanner from '@/components/MantenimientoBanner.vue'
import { useModulo } from '@/composables/useModulo'

const { activo, mensaje, loading: loadingModulo } = useModulo('solicitudes')
const router = useRouter()

const form = ref({
  descripcion:        '',
  material_preferido: '',
  cantidad:           1,
  uso_final:          '',
})
const archivos  = ref([])
const arrastre  = ref(false)
const enviando  = ref(false)
const error     = ref('')

function onDrop(e) {
  arrastre.value = false
  const files = Array.from(e.dataTransfer?.files || [])
  archivos.value = [...archivos.value, ...files].slice(0, 5)
}
function onFileInput(e) {
  archivos.value = [...archivos.value, ...Array.from(e.target.files)].slice(0, 5)
}
function quitarArchivo(i) { archivos.value.splice(i, 1) }

async function enviar() {
  error.value  = ''
  enviando.value = true
  try {
    const fd = new FormData()
    Object.entries(form.value).forEach(([k, v]) => fd.append(k, v))
    archivos.value.forEach(f => fd.append('archivos[]', f))

    const { data } = await api.post('/solicitudes', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    router.push(`/solicitudes/${data.data.id}`)
  } catch (e) {
    const errs = e.response?.data?.errors
    error.value = errs ? Object.values(errs).flat().join(' ') : (e.response?.data?.message || 'Error al enviar.')
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-display font-bold text-odi-texto mb-2">Cotizar proyecto</h1>
    <p class="text-odi-muted mb-8">Cuéntanos tu idea y te enviamos una cotización en 24h</p>

    <template v-if="loadingModulo">
      <div class="text-odi-muted text-center py-20">Cargando...</div>
    </template>
    <MantenimientoBanner v-else-if="!activo" :mensaje="mensaje" />

    <form v-else @submit.prevent="enviar" class="flex flex-col gap-5">
      <div>
        <label class="block text-xs font-medium text-odi-muted mb-1.5">Descripción del proyecto *</label>
        <textarea v-model="form.descripcion" rows="4" required
          placeholder="Describe qué quieres imprimir, dimensiones aproximadas, colores, cantidad..."
          class="input-field resize-none" />
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Material preferido</label>
          <select v-model="form.material_preferido" class="input-field">
            <option value="">Sin preferencia</option>
            <option value="PLA">PLA</option>
            <option value="PETG">PETG</option>
            <option value="ABS">ABS</option>
            <option value="Resina">Resina</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Cantidad</label>
          <input v-model.number="form.cantidad" type="number" min="1" class="input-field" />
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-odi-muted mb-1.5">Uso final</label>
        <input v-model="form.uso_final" type="text" placeholder="Prototipo, decoración, pieza funcional..."
          class="input-field" />
      </div>

      <!-- Drop zone -->
      <div>
        <label class="block text-xs font-medium text-odi-muted mb-1.5">Archivos (STL, OBJ, imágenes, PDF — máx 5)</label>
        <div
          @dragover.prevent="arrastre = true"
          @dragleave="arrastre = false"
          @drop.prevent="onDrop"
          :class="['border-2 border-dashed rounded-xl p-8 text-center transition-colors duration-200 cursor-pointer',
            arrastre ? 'border-odi-amarillo bg-odi-amarillo/5' : 'border-odi-gris2 hover:border-odi-muted']"
          @click="$refs.fileInput.click()">
          <p class="text-odi-muted text-sm">Arrastra archivos aquí o <span class="text-odi-amarillo">haz clic</span> para seleccionar</p>
          <input ref="fileInput" type="file" multiple accept=".stl,.obj,.3mf,.jpg,.jpeg,.png,.pdf"
            class="hidden" @change="onFileInput" />
        </div>
        <div v-if="archivos.length" class="mt-3 flex flex-col gap-1.5">
          <div v-for="(f, i) in archivos" :key="i"
            class="flex items-center justify-between bg-odi-gris2 rounded-lg px-3 py-2 text-sm">
            <span class="text-odi-texto truncate">{{ f.name }}</span>
            <button type="button" @click="quitarArchivo(i)" class="text-odi-muted hover:text-red-400 ml-3 transition-colors">✕</button>
          </div>
        </div>
      </div>

      <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

      <button type="submit" :disabled="enviando" class="btn-primary py-3 text-base">
        {{ enviando ? 'Enviando solicitud...' : 'Enviar solicitud' }}
      </button>
    </form>
  </div>
</template>
