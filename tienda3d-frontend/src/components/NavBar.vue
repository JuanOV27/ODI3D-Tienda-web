<script setup>
import { computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCarritoStore } from '@/stores/carrito'
import api from '@/lib/api'

const auth    = useAuthStore()
const carrito = useCarritoStore()
const router  = useRouter()

async function cerrarSesion() {
  try { await api.post('/auth/logout') } catch {}
  auth.logout()
  router.push('/login')
}
</script>

<template>
  <nav class="sticky top-0 z-50 border-b border-odi-gris2 bg-odi-negro/90 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-6">

      <!-- Logo -->
      <RouterLink to="/" class="font-display font-bold text-xl tracking-tight text-odi-amarillo hover:opacity-80 transition-opacity duration-200">
        ODI<span class="text-odi-texto">3D</span>
      </RouterLink>

      <!-- Links centro -->
      <div class="hidden md:flex items-center gap-6 text-sm font-medium">
        <RouterLink to="/catalogo"
          class="text-odi-muted hover:text-odi-texto transition-colors duration-200"
          active-class="text-odi-amarillo">
          Catálogo
        </RouterLink>
        <RouterLink to="/solicitudes"
          class="text-odi-muted hover:text-odi-texto transition-colors duration-200"
          active-class="text-odi-amarillo">
          Cotizar proyecto
        </RouterLink>
      </div>

      <!-- Derecha -->
      <div class="flex items-center gap-3">
        <!-- Carrito -->
        <RouterLink to="/carrito" class="relative p-2 text-odi-muted hover:text-odi-amarillo transition-colors duration-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span v-if="carrito.totalItems > 0"
            class="absolute -top-0.5 -right-0.5 bg-odi-amarillo text-odi-negro text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
            {{ carrito.totalItems }}
          </span>
        </RouterLink>

        <!-- Auth -->
        <template v-if="auth.loggedIn">
          <RouterLink to="/mi-cuenta" class="text-sm text-odi-muted hover:text-odi-texto transition-colors duration-200">
            {{ auth.cliente?.nombre?.split(' ')[0] }}
          </RouterLink>
          <button @click="cerrarSesion" class="btn-ghost text-sm py-1.5 px-3">
            Salir
          </button>
        </template>
        <template v-else>
          <RouterLink to="/login" class="text-sm text-odi-muted hover:text-odi-texto transition-colors duration-200">
            Ingresar
          </RouterLink>
          <RouterLink to="/registro" class="btn-primary text-sm py-1.5 px-4">
            Registrarse
          </RouterLink>
        </template>
      </div>
    </div>
  </nav>
</template>
