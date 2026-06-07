import { ref, onMounted } from 'vue'
import api from '@/lib/api'

const cache = {}

export function useModulo(nombre) {
  const activo  = ref(true)
  const mensaje = ref('')
  const loading = ref(true)

  onMounted(async () => {
    if (cache[nombre] !== undefined) {
      activo.value  = cache[nombre].activo
      mensaje.value = cache[nombre].mensaje
      loading.value = false
      return
    }
    try {
      const { data } = await api.get(`/modulos/${nombre}/estado`)
      activo.value  = data.data.activo
      mensaje.value = data.data.mensaje_baja || ''
      cache[nombre] = { activo: activo.value, mensaje: mensaje.value }
    } catch {
      activo.value = true  // fallback: mostrar si falla el check
    } finally {
      loading.value = false
    }
  })

  return { activo, mensaje, loading }
}
