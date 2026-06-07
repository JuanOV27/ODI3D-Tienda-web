<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '@/lib/api'
import MantenimientoBanner from '@/components/MantenimientoBanner.vue'
import { useModulo } from '@/composables/useModulo'

const { activo, mensaje, loading: loadingModulo } = useModulo('catalogo')
const assetsUrl = import.meta.env.VITE_ASSETS_URL

const productos  = ref([])
const cargando   = ref(false)
const busqueda   = ref('')
const categoria  = ref('')

async function cargar() {
  cargando.value = true
  try {
    const params = {}
    if (busqueda.value)  params.q         = busqueda.value
    if (categoria.value) params.categoria  = categoria.value
    const { data } = await api.get('/catalogo/productos', { params })
    productos.value = data.data
  } catch {
    productos.value = []
  } finally {
    cargando.value = false
  }
}

onMounted(cargar)

let timer
function onBusqueda() {
  clearTimeout(timer)
  timer = setTimeout(cargar, 350)
}
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-display font-bold text-odi-texto mb-2">Catálogo</h1>
    <p class="text-odi-muted mb-8">Productos disponibles para entrega o personalización</p>

    <template v-if="loadingModulo">
      <div class="text-odi-muted text-center py-20">Cargando...</div>
    </template>
    <MantenimientoBanner v-else-if="!activo" :mensaje="mensaje" />

    <template v-else>
      <!-- Filtros -->
      <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <input v-model="busqueda" @input="onBusqueda" type="text"
          placeholder="Buscar producto..."
          class="input-field max-w-xs" />
        <select v-model="categoria" @change="cargar" class="input-field max-w-xs">
          <option value="">Todas las categorías</option>
          <option value="figuritas">Figuritas</option>
          <option value="piezas_tecnicas">Piezas técnicas</option>
          <option value="hogar">Hogar</option>
          <option value="educacion">Educación</option>
          <option value="otros">Otros</option>
        </select>
      </div>

      <!-- Grid -->
      <div v-if="cargando" class="text-odi-muted text-center py-20">Cargando productos...</div>
      <div v-else-if="productos.length === 0" class="text-center py-20">
        <p class="text-odi-muted">No se encontraron productos.</p>
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <RouterLink
          v-for="p in productos" :key="p.id"
          :to="`/catalogo/${p.id}`"
          class="card hover:bg-odi-gris2 hover:-translate-y-1 hover:shadow-lg hover:shadow-black/30 flex flex-col group">
          <!-- Imagen -->
          <div class="bg-odi-gris2 rounded-xl h-44 mb-4 overflow-hidden flex items-center justify-center">
            <img v-if="p.imagen_principal" :src="`${assetsUrl}/${p.imagen_principal}`"
              :alt="p.nombre" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <span v-else class="text-4xl opacity-20">🖨️</span>
          </div>
          <!-- Info -->
          <span class="text-odi-muted text-xs uppercase tracking-wide mb-1">{{ p.categoria }}</span>
          <h3 class="font-semibold text-odi-texto mb-2 line-clamp-2 leading-snug">{{ p.nombre }}</h3>
          <div class="mt-auto flex items-center justify-between pt-3 border-t border-odi-gris2">
            <span class="text-odi-amarillo font-bold">
              ${{ Number(p.precio_base).toLocaleString('es-CO') }}
            </span>
            <span class="text-odi-muted text-xs">Ver más →</span>
          </div>
        </RouterLink>
      </div>
    </template>
  </div>
</template>
