<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/api'
import MantenimientoBanner from '@/components/MantenimientoBanner.vue'
import VisorArchivo from '@/components/VisorArchivo.vue'
import { useModulo } from '@/composables/useModulo'

const { activo, mensaje, loading: loadingModulo } = useModulo('solicitudes')
const router = useRouter()

// ── Modo: artículo único vs. múltiples ────────────────────────
const multiItem = ref(false)

// Artículo único (modo original)
const form = ref({
  descripcion:        '',
  material_preferido: '',
  cantidad:           1,
  uso_final:          '',
})

// Lista de artículos (modo multi-ítem)
const items = ref([
  { descripcion: '', material: '', cantidad: 1 },
])

function agregarItem() {
  items.value.push({ descripcion: '', material: '', cantidad: 1 })
}
function quitarItem(i) {
  if (items.value.length > 1) items.value.splice(i, 1)
}

// ── Archivos adjuntos ─────────────────────────────────────────
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

// ── Toggle multi-ítem ─────────────────────────────────────────
function toggleMultiItem() {
  multiItem.value = !multiItem.value
  if (multiItem.value && items.value.length === 0) {
    items.value = [{ descripcion: '', material: '', cantidad: 1 }]
  }
  // Si volvemos a modo único y el primer ítem tiene datos, transferirlos al form
  if (!multiItem.value && items.value.length > 0 && items.value[0].descripcion) {
    form.value.descripcion        = items.value[0].descripcion
    form.value.material_preferido = items.value[0].material || ''
    form.value.cantidad           = items.value[0].cantidad  || 1
  }
}

// ── Enviar ────────────────────────────────────────────────────
async function enviar() {
  error.value   = ''
  enviando.value = true
  try {
    const fd = new FormData()

    if (multiItem.value) {
      // Modo multi-ítem: validar que todos tengan descripción
      const validos = items.value.filter(it => it.descripcion.trim())
      if (!validos.length) {
        error.value = 'Agrega al menos un artículo con descripción.'
        enviando.value = false
        return
      }
      // Serializar items en FormData con notación de array
      validos.forEach((it, i) => {
        fd.append(`items[${i}][descripcion]`, it.descripcion.trim())
        if (it.material) fd.append(`items[${i}][material]`, it.material)
        fd.append(`items[${i}][cantidad]`, String(it.cantidad || 1))
      })
      // Descripción principal = resumen de los ítems
      const desc = validos.map((it, i) => `${i+1}. ${it.descripcion}`).join('; ')
      fd.append('descripcion', desc)
    } else {
      // Modo único: comportamiento original
      if (!form.value.descripcion.trim()) {
        error.value = 'La descripción del proyecto es requerida.'
        enviando.value = false
        return
      }
      Object.entries(form.value).forEach(([k, v]) => fd.append(k, v))
    }

    archivos.value.forEach(f => fd.append('archivos[]', f))

    const { data } = await api.post('/solicitudes', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    router.push(`/solicitudes/${data.data.id}`)
  } catch (e) {
    const errs = e.response?.data?.errors
    error.value = errs
      ? Object.values(errs).flat().join(' ')
      : (e.response?.data?.message || 'Error al enviar la solicitud.')
  } finally {
    enviando.value = false
  }
}

// Materiales disponibles
const materiales = ['PLA', 'PETG', 'ABS', 'Resina', 'TPU', 'Nylon']
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

      <!-- ── Toggle multi-ítem ─────────────────────────────── -->
      <div class="flex items-center gap-3 p-3 rounded-xl border border-odi-gris2 bg-odi-fondo cursor-pointer select-none"
           @click="toggleMultiItem">
        <div :class="['w-10 h-5 rounded-full transition-colors duration-200 relative flex-shrink-0',
                       multiItem ? 'bg-odi-amarillo' : 'bg-odi-gris2']">
          <div :class="['absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200',
                         multiItem ? 'translate-x-5' : 'translate-x-0.5']" />
        </div>
        <div>
          <p class="text-sm font-semibold text-odi-texto leading-tight">
            Cotizar varios artículos en una solicitud
          </p>
          <p class="text-xs text-odi-muted">
            {{ multiItem ? 'Activo — puedes agregar múltiples artículos' : 'Artículo único — activa para agregar más' }}
          </p>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════════
           MODO ARTÍCULO ÚNICO (original)
      ════════════════════════════════════════════════════ -->
      <template v-if="!multiItem">
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Descripción del proyecto *</label>
          <textarea v-model="form.descripcion" rows="4"
            placeholder="Describe qué quieres imprimir, dimensiones aproximadas, colores, cantidad..."
            class="input-field resize-none" />
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-odi-muted mb-1.5">Material preferido</label>
            <select v-model="form.material_preferido" class="input-field">
              <option value="">Sin preferencia</option>
              <option v-for="m in materiales" :key="m" :value="m">{{ m }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-odi-muted mb-1.5">Cantidad</label>
            <input v-model.number="form.cantidad" type="number" min="1" class="input-field" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Uso final</label>
          <input v-model="form.uso_final" type="text"
            placeholder="Prototipo, decoración, pieza funcional..."
            class="input-field" />
        </div>
      </template>

      <!-- ════════════════════════════════════════════════════
           MODO MULTI-ÍTEM
      ════════════════════════════════════════════════════ -->
      <template v-else>
        <div>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-odi-texto">📦 Artículos a cotizar</h3>
            <span class="text-xs text-odi-muted">{{ items.length }} artículo{{ items.length !== 1 ? 's' : '' }}</span>
          </div>

          <div v-for="(item, i) in items" :key="i"
               class="border border-odi-gris2 rounded-xl p-4 mb-3 bg-odi-fondo">
            <div class="flex items-start justify-between gap-2 mb-3">
              <span class="text-xs font-bold text-odi-amarillo bg-odi-amarillo/10 rounded px-2 py-0.5">
                Artículo {{ i + 1 }}
              </span>
              <button v-if="items.length > 1" type="button" @click="quitarItem(i)"
                class="text-red-400 hover:text-red-600 text-sm transition-colors">✕</button>
            </div>

            <div class="flex flex-col gap-3">
              <div>
                <label class="block text-xs font-medium text-odi-muted mb-1">Descripción *</label>
                <textarea v-model="item.descripcion" rows="2" required
                  :placeholder="`¿Qué es el artículo ${i+1}? Dimensiones, colores, detalles...`"
                  class="input-field resize-none" />
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-odi-muted mb-1">Material</label>
                  <select v-model="item.material" class="input-field">
                    <option value="">Sin preferencia</option>
                    <option v-for="m in materiales" :key="m" :value="m">{{ m }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-medium text-odi-muted mb-1">Cantidad</label>
                  <input v-model.number="item.cantidad" type="number" min="1" class="input-field" />
                </div>
              </div>
            </div>
          </div>

          <button type="button" @click="agregarItem"
            class="w-full py-2.5 border-2 border-dashed border-odi-gris2 rounded-xl text-sm font-semibold text-odi-muted hover:border-odi-amarillo hover:text-odi-amarillo transition-colors duration-200">
            + Agregar artículo
          </button>
        </div>
      </template>

      <!-- ── Drop zone de archivos (común a ambos modos) ───── -->
      <div>
        <label class="block text-xs font-medium text-odi-muted mb-1.5">
          Archivos adjuntos (STL, OBJ, imágenes, PDF — máx 5)
        </label>
        <div
          @dragover.prevent="arrastre = true"
          @dragleave="arrastre = false"
          @drop.prevent="onDrop"
          :class="['border-2 border-dashed rounded-xl p-8 text-center transition-colors duration-200 cursor-pointer',
            arrastre ? 'border-odi-amarillo bg-odi-amarillo/5' : 'border-odi-gris2 hover:border-odi-muted']"
          @click="$refs.fileInput.click()">
          <p class="text-odi-muted text-sm">
            Arrastra archivos aquí o <span class="text-odi-amarillo">haz clic</span> para seleccionar
          </p>
          <input ref="fileInput" type="file" multiple accept=".stl,.obj,.3mf,.jpg,.jpeg,.png,.pdf"
            class="hidden" @change="onFileInput" />
        </div>
        <div v-if="archivos.length" class="mt-3 flex flex-col gap-1.5">
          <VisorArchivo v-for="(f, i) in archivos" :key="i" :archivo="f" eliminable @eliminar="quitarArchivo(i)" />
        </div>
      </div>

      <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

      <button type="submit" :disabled="enviando" class="btn-primary py-3 text-base">
        {{ enviando ? 'Enviando solicitud...' : (multiItem ? `Enviar ${items.filter(i=>i.descripcion.trim()).length} artículo(s)` : 'Enviar solicitud') }}
      </button>
    </form>
  </div>
</template>
