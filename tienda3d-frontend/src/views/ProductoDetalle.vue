<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/lib/api'
import { useAuthStore } from '@/stores/auth'
import { useCarritoStore } from '@/stores/carrito'
import { useWhatsApp } from '@/composables/useWhatsApp'

const route   = useRoute()
const auth    = useAuthStore()
const carrito = useCarritoStore()
const { linkProducto } = useWhatsApp()
const assetsUrl = import.meta.env.VITE_ASSETS_URL

const producto    = ref(null)
const reviews     = ref([])
const cargando    = ref(true)
const agregado    = ref(false)
const imgActiva   = ref(0)

const nuevaResena    = ref({ calificacion: 5, comentario: '' })
const enviandoResena = ref(false)
const mensajeResena  = ref('')

async function cargar() {
  try {
    const { data } = await api.get(`/catalogo/productos/${route.params.id}`)
    producto.value = data.data
    reviews.value  = data.data.reviews || []
    imgActiva.value = 0
  } catch {
    producto.value = null
  } finally {
    cargando.value = false
  }
}

async function enviarResena() {
  enviandoResena.value = true
  mensajeResena.value  = ''
  try {
    await api.post(`/catalogo/productos/${route.params.id}/reviews`, nuevaResena.value)
    mensajeResena.value = '✓ Reseña enviada. Estará visible tras aprobación.'
    nuevaResena.value   = { calificacion: 5, comentario: '' }
    await cargar()
  } catch (e) {
    mensajeResena.value = e.response?.data?.error || 'Error al enviar la reseña.'
  } finally {
    enviandoResena.value = false
  }
}

function agregarAlCarrito() {
  carrito.agregar(producto.value)
  agregado.value = true
  setTimeout(() => { agregado.value = false }, 2000)
}

function imgUrl(ruta) {
  return `${assetsUrl}/${ruta}`
}

onMounted(cargar)
</script>

<template>
  <div class="max-w-5xl mx-auto px-4 py-10">
    <div v-if="cargando" class="text-odi-muted text-center py-20">Cargando...</div>
    <div v-else-if="!producto" class="text-center py-20">
      <p class="text-odi-muted">Producto no encontrado.</p>
    </div>

    <div v-else class="grid md:grid-cols-2 gap-10">
      <!-- Galería de imágenes -->
      <div class="flex flex-col gap-3">
        <!-- Imagen principal -->
        <div class="bg-odi-gris rounded-2xl overflow-hidden aspect-square flex items-center justify-center">
          <img v-if="producto.imagenes?.length"
            :src="imgUrl(producto.imagenes[imgActiva].ruta)"
            :alt="producto.nombre"
            class="w-full h-full object-cover transition-opacity duration-200" />
          <span v-else class="text-6xl opacity-20">🖨️</span>
        </div>
        <!-- Miniaturas -->
        <div v-if="producto.imagenes?.length > 1" class="flex gap-2 flex-wrap">
          <button
            v-for="(img, i) in producto.imagenes"
            :key="img.id"
            @click="imgActiva = i"
            class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all duration-200"
            :class="i === imgActiva ? 'border-odi-amarillo' : 'border-transparent opacity-60 hover:opacity-100'">
            <img :src="imgUrl(img.ruta)" :alt="`Imagen ${i + 1}`" class="w-full h-full object-cover" />
          </button>
        </div>
      </div>

      <!-- Detalle -->
      <div class="flex flex-col">
        <span class="text-odi-muted text-xs uppercase tracking-widest mb-2">{{ producto.categoria }}</span>
        <h1 class="text-3xl font-display font-bold text-odi-texto mb-4">{{ producto.nombre }}</h1>
        <p class="text-odi-muted leading-relaxed mb-6">{{ producto.descripcion }}</p>
        <div class="text-3xl font-bold text-odi-amarillo mb-8">
          ${{ Number(producto.precio_base).toLocaleString('es-CO') }}
        </div>

        <div class="flex flex-col gap-3">
          <button @click="agregarAlCarrito" class="btn-primary py-3 text-base">
            {{ agregado ? '✓ Agregado' : 'Agregar al carrito' }}
          </button>
          <a :href="linkProducto(producto)" target="_blank" class="btn-ghost py-3 text-base text-center">
            {{ producto.enlace_whatsapp ? 'Ver y comprar por WhatsApp' : 'Consultar por WhatsApp' }}
          </a>
        </div>
      </div>
    </div>

    <!-- Reseñas -->
    <div v-if="producto" class="mt-16">
      <h2 class="text-xl font-display font-semibold text-odi-texto mb-6">Reseñas</h2>

      <!-- Formulario nueva reseña -->
      <div v-if="auth.loggedIn" class="card mb-6">
        <h3 class="text-sm font-semibold text-odi-texto mb-4">Deja tu reseña</h3>
        <div class="flex gap-1 mb-3">
          <button v-for="n in 5" :key="n" @click="nuevaResena.calificacion = n"
            class="text-2xl transition-transform duration-100 hover:scale-110"
            :class="n <= nuevaResena.calificacion ? 'text-odi-amarillo' : 'text-odi-gris2'">
            ★
          </button>
        </div>
        <textarea v-model="nuevaResena.comentario" rows="3"
          placeholder="Cuéntanos tu experiencia..." class="input-field mb-3 resize-none" />
        <p v-if="mensajeResena" class="text-sm mb-3"
          :class="mensajeResena.startsWith('✓') ? 'text-green-400' : 'text-red-400'">
          {{ mensajeResena }}
        </p>
        <button @click="enviarResena" :disabled="enviandoResena" class="btn-primary">
          {{ enviandoResena ? 'Enviando...' : 'Publicar reseña' }}
        </button>
      </div>
      <p v-else class="text-odi-muted text-sm mb-6">
        <RouterLink to="/login" class="text-odi-amarillo hover:underline">Ingresa</RouterLink>
        para dejar una reseña.
      </p>

      <!-- Lista reseñas -->
      <div v-if="reviews.length === 0" class="text-odi-muted text-sm">Sin reseñas todavía.</div>
      <div v-else class="flex flex-col gap-4">
        <div v-for="r in reviews" :key="r.id" class="card">
          <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-odi-texto text-sm">{{ r.cliente?.nombre }}</span>
            <span class="text-odi-amarillo text-sm">{{ '★'.repeat(r.calificacion) }}</span>
          </div>
          <p class="text-odi-muted text-sm">{{ r.comentario }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
