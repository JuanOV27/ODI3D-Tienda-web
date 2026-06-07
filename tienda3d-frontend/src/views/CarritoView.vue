<script setup>
import { RouterLink } from 'vue-router'
import { useCarritoStore } from '@/stores/carrito'
import { useWhatsApp } from '@/composables/useWhatsApp'

const carrito = useCarritoStore()
const { linkCarrito } = useWhatsApp()

const assetsUrl = import.meta.env.VITE_ASSETS_URL

function imgUrl(item) {
  const ruta = item.imagenes?.[0]?.ruta || item.imagen_principal
  return ruta ? `${assetsUrl}/${ruta}` : null
}

function subtotal(item) {
  return Number(item.precio_base) * item.cantidad
}

function onCantidadInput(id, e) {
  carrito.actualizarCantidad(id, e.target.value)
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-display font-bold text-odi-texto mb-1">Carrito de compras</h1>
    <p class="text-odi-muted mb-8">Revisa tus productos y envíalos por WhatsApp para confirmar tu pedido</p>

    <!-- Carrito vacío -->
    <div v-if="carrito.items.length === 0" class="card text-center py-16">
      <div class="text-4xl mb-4">🛒</div>
      <p class="text-odi-texto font-medium mb-2">Tu carrito está vacío</p>
      <p class="text-odi-muted text-sm mb-6">Explora el catálogo y agrega los productos que te interesen.</p>
      <RouterLink to="/catalogo" class="btn-primary inline-block px-6">Ver catálogo</RouterLink>
    </div>

    <!-- Lista de productos -->
    <div v-else class="grid lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 flex flex-col gap-3">
        <div v-for="item in carrito.items" :key="item.id"
             class="card flex items-center gap-4">
          <RouterLink :to="`/catalogo/${item.id}`" class="shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-odi-gris2 flex items-center justify-center">
            <img v-if="imgUrl(item)" :src="imgUrl(item)" :alt="item.nombre" class="w-full h-full object-cover" />
            <span v-else class="text-2xl">🖼️</span>
          </RouterLink>

          <div class="flex-1 min-w-0">
            <RouterLink :to="`/catalogo/${item.id}`" class="font-display font-semibold text-odi-texto hover:text-odi-amarillo transition-colors duration-200 truncate block">
              {{ item.nombre }}
            </RouterLink>
            <p class="text-odi-muted text-sm">
              ${{ Number(item.precio_base).toLocaleString('es-CO') }} c/u
            </p>
          </div>

          <!-- Selector de cantidad -->
          <div class="flex items-center gap-2 shrink-0">
            <button @click="carrito.decrementar(item.id)"
                    class="w-8 h-8 rounded-lg border border-odi-gris2 text-odi-texto hover:border-odi-amarillo hover:text-odi-amarillo transition-colors duration-200 flex items-center justify-center">
              −
            </button>
            <input type="number" min="1" :value="item.cantidad"
                   @change="onCantidadInput(item.id, $event)"
                   class="w-12 text-center bg-transparent text-odi-texto text-sm focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
            <button @click="carrito.incrementar(item.id)"
                    class="w-8 h-8 rounded-lg border border-odi-gris2 text-odi-texto hover:border-odi-amarillo hover:text-odi-amarillo transition-colors duration-200 flex items-center justify-center">
              +
            </button>
          </div>

          <!-- Subtotal -->
          <div class="text-odi-amarillo font-semibold shrink-0 w-28 text-right">
            ${{ subtotal(item).toLocaleString('es-CO') }}
          </div>

          <!-- Eliminar -->
          <button @click="carrito.quitar(item.id)"
                  class="shrink-0 p-2 text-odi-muted hover:text-red-400 transition-colors duration-200"
                  title="Quitar del carrito">
            ✕
          </button>
        </div>

        <button @click="carrito.vaciar" class="self-start text-odi-muted text-sm hover:text-red-400 transition-colors duration-200 mt-2">
          Vaciar carrito
        </button>
      </div>

      <!-- Resumen y acción -->
      <div class="card h-fit flex flex-col gap-4">
        <h2 class="font-display font-semibold text-odi-texto">Resumen del pedido</h2>

        <div class="flex justify-between text-sm text-odi-muted">
          <span>Productos ({{ carrito.totalItems }})</span>
          <span class="text-odi-texto">${{ carrito.total.toLocaleString('es-CO') }}</span>
        </div>

        <div class="border-t border-odi-gris2 pt-4 flex justify-between items-baseline">
          <span class="text-odi-texto font-medium">Total estimado</span>
          <span class="text-2xl font-bold text-odi-amarillo">${{ carrito.total.toLocaleString('es-CO') }}</span>
        </div>

        <p class="text-odi-muted text-xs leading-relaxed">
          ODI3D no procesa pagos en línea. Al continuar, se abrirá WhatsApp con un mensaje
          listo para enviar que incluye todos los productos, cantidades y el total estimado,
          para que confirmes tu pedido directamente con nosotros.
        </p>

        <a :href="linkCarrito(carrito.items, carrito.total)" target="_blank"
           class="btn-primary w-full text-center inline-flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Continuar por WhatsApp
        </a>

        <RouterLink to="/catalogo" class="text-odi-muted text-sm text-center hover:text-odi-amarillo transition-colors duration-200">
          ← Seguir explorando el catálogo
        </RouterLink>
      </div>
    </div>
  </div>
</template>
