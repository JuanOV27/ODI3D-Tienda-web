<script setup>
import { ref } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api from '@/lib/api'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route  = useRoute()
const auth   = useAuthStore()

const form   = ref({ email: '', password: '' })
const error  = ref('')
const cargando = ref(false)

async function login() {
  error.value   = ''
  cargando.value = true
  try {
    const { data } = await api.post('/auth/login', form.value)
    auth.setAuth(data.token, data.cliente)
    const destino = route.query.redirect || '/'
    router.push(destino)
  } catch (e) {
    error.value = e.response?.data?.error || 'Error al iniciar sesión.'
  } finally {
    cargando.value = false
  }
}
</script>

<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
      <h1 class="text-3xl font-display font-bold text-odi-texto mb-2 text-center">Bienvenido</h1>
      <p class="text-odi-muted text-sm text-center mb-8">Ingresa a tu cuenta ODI3D</p>

      <form @submit.prevent="login" class="card flex flex-col gap-4">
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Correo</label>
          <input v-model="form.email" type="email" required placeholder="tu@email.com" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Contraseña</label>
          <input v-model="form.password" type="password" required placeholder="••••••••" class="input-field" />
        </div>

        <p v-if="error" class="text-red-400 text-sm text-center">{{ error }}</p>

        <button type="submit" :disabled="cargando" class="btn-primary py-3 w-full">
          {{ cargando ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </form>

      <p class="text-center text-odi-muted text-sm mt-6">
        ¿No tienes cuenta?
        <RouterLink to="/registro" class="text-odi-amarillo hover:underline ml-1">Regístrate</RouterLink>
      </p>
    </div>
  </div>
</template>
