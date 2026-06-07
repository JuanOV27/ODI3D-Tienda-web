<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api from '@/lib/api'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth   = useAuthStore()

const form = ref({ nombre: '', email: '', telefono: '', password: '', password_confirmation: '' })
const aceptaTerminos = ref(false)
const error = ref('')
const cargando = ref(false)

async function registro() {
  error.value   = ''

  if (!aceptaTerminos.value) {
    error.value = 'Debes leer y aceptar los Términos y Condiciones para crear tu cuenta.'
    return
  }

  cargando.value = true
  try {
    const { data } = await api.post('/auth/registro', { ...form.value, acepta_terminos: aceptaTerminos.value })
    auth.setAuth(data.token, data.cliente)
    router.push('/mi-cuenta')
  } catch (e) {
    const errs = e.response?.data?.errors
    error.value = errs ? Object.values(errs).flat().join(' ') : (e.response?.data?.message || 'Error al registrarse.')
  } finally {
    cargando.value = false
  }
}
</script>

<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-sm">
      <h1 class="text-3xl font-display font-bold text-odi-texto mb-2 text-center">Crear cuenta</h1>
      <p class="text-odi-muted text-sm text-center mb-8">Accede a cotizaciones y seguimiento de pedidos</p>

      <form @submit.prevent="registro" class="card flex flex-col gap-4">
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Nombre completo</label>
          <input v-model="form.nombre" type="text" required placeholder="Juan Pérez" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Correo</label>
          <input v-model="form.email" type="email" required placeholder="tu@email.com" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Teléfono / WhatsApp</label>
          <input v-model="form.telefono" type="tel" placeholder="3001234567" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Contraseña</label>
          <input v-model="form.password" type="password" required minlength="8" placeholder="Mínimo 8 caracteres" class="input-field" />
        </div>
        <div>
          <label class="block text-xs font-medium text-odi-muted mb-1.5">Confirmar contraseña</label>
          <input v-model="form.password_confirmation" type="password" required placeholder="••••••••" class="input-field" />
        </div>

        <label class="flex items-start gap-2.5 text-xs text-odi-muted leading-relaxed cursor-pointer select-none">
          <input v-model="aceptaTerminos" type="checkbox" required
                 class="mt-0.5 w-4 h-4 rounded border-odi-gris2 bg-odi-gris2 text-odi-amarillo focus:ring-odi-amarillo focus:ring-offset-0 cursor-pointer" />
          <span>
            He leído y acepto los
            <RouterLink to="/terminos" target="_blank" class="text-odi-amarillo font-medium hover:underline">
              Términos y Condiciones
            </RouterLink>
            de uso de la Plataforma ODI3D.
          </span>
        </label>

        <p v-if="error" class="text-red-400 text-sm text-center">{{ error }}</p>

        <button type="submit" :disabled="cargando" class="btn-primary py-3 w-full">
          {{ cargando ? 'Registrando...' : 'Crear cuenta' }}
        </button>
      </form>

      <p class="text-center text-odi-muted text-sm mt-6">
        ¿Ya tienes cuenta?
        <RouterLink to="/login" class="text-odi-amarillo hover:underline ml-1">Ingresar</RouterLink>
      </p>
    </div>
  </div>
</template>
