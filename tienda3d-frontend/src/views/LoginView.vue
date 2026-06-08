<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api from '@/lib/api'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route  = useRoute()
const auth   = useAuthStore()

// URL del endpoint cross_check de gestion3d (mismo servidor, accesible desde Vite vía proxy o directo)
const GESTION3D_CROSS_CHECK = 'http://localhost/ODI3D/gestion3d/api/api_auth.php?action=cross_check'
const GESTION3D_LOGIN_URL   = 'http://localhost/ODI3D/gestion3d/login.html'

const form     = ref({ email: '', password: '' })
const error    = ref('')
const cargando = ref(false)

// Estado de redirección a gestion3d
const redirigiendo      = ref(false)
const mensajeRedireccion = ref('')

onMounted(() => {
  // Pre-llenar email si viene como parámetro (?email=...) desde gestion3d
  const emailParam = route.query.email
  if (emailParam) {
    form.value.email = String(emailParam)
  }
})

/**
 * Verifica si el email+password pertenecen a un usuario interno de gestion3d.
 * Si sí, redirige al login de gestion3d con el email pre-llenado.
 */
async function verificarEnGestion(email, password) {
  try {
    const resp = await fetch(GESTION3D_CROSS_CHECK, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    })
    if (!resp.ok) return false
    const json = await resp.json()
    if (!json.success || !json.data?.nombre) return false

    // Es un empleado/admin — mostrar mensaje y redirigir
    const nombre = json.data.nombre
    mensajeRedireccion.value = `👋 ¡Hola, ${nombre}! Tu cuenta es del sistema interno. Redirigiendo...`
    redirigiendo.value = true

    setTimeout(() => {
      const loginUrl = `${GESTION3D_LOGIN_URL}?email=${encodeURIComponent(email)}`
      window.location.href = loginUrl
    }, 2200)
    return true
  } catch {
    return false
  }
}

async function login() {
  error.value    = ''
  cargando.value = true
  try {
    const { data } = await api.post('/auth/login', form.value)
    auth.setAuth(data.token, data.cliente)
    const destino = route.query.redirect || '/'
    router.push(destino)
  } catch (e) {
    // Login de cliente falló — verificar si son credenciales del sistema interno
    const esEmpleado = await verificarEnGestion(form.value.email, form.value.password)
    if (!esEmpleado) {
      error.value = e.response?.data?.error || 'Error al iniciar sesión.'
    }
    // Si esEmpleado=true, la redirección ya fue iniciada
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

      <!-- Banner de redirección al sistema correcto -->
      <div v-if="redirigiendo"
        class="mb-4 rounded-lg border border-amber-400/40 bg-amber-400/10 px-4 py-3 text-center">
        <p class="text-amber-300 text-sm font-medium">{{ mensajeRedireccion }}</p>
        <p class="text-amber-400/60 text-xs mt-1">
          <span class="inline-block w-2.5 h-2.5 border-2 border-amber-400/40 border-t-amber-400 rounded-full animate-spin mr-1 align-middle"></span>
          Espera un momento...
        </p>
      </div>

      <form v-else @submit.prevent="login" class="card flex flex-col gap-4">
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
          {{ cargando ? 'Verificando...' : 'Ingresar' }}
        </button>
      </form>

      <p v-if="!redirigiendo" class="text-center text-odi-muted text-sm mt-6">
        ¿No tienes cuenta?
        <RouterLink to="/registro" class="text-odi-amarillo hover:underline ml-1">Regístrate</RouterLink>
      </p>
    </div>
  </div>
</template>
