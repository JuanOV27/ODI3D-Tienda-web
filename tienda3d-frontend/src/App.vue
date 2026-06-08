<script setup>
import { onMounted } from 'vue'
import { RouterView, useRouter } from 'vue-router'
import NavBar from '@/components/NavBar.vue'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

onMounted(() => {
  const params = new URLSearchParams(location.search)

  // ── Auto-login de cliente redirigido desde gestion3d ──────────────────────
  // gestion3d pasa ?__odi_token=<token>&__odi_cliente=<json> cuando un cliente
  // intentó hacer login allá pero en realidad pertenece a la tienda.
  const odi_token   = params.get('__odi_token')
  const odi_cliente = params.get('__odi_cliente')
  if (odi_token && odi_cliente) {
    try {
      const clienteObj = JSON.parse(odi_cliente)
      auth.setAuth(odi_token, clienteObj)
    } catch {}
    // Limpiar params del historial para no re-procesar en navegación posterior
    const url = new URL(location.href)
    url.searchParams.delete('__odi_token')
    url.searchParams.delete('__odi_cliente')
    history.replaceState(null, '', url.toString())
    router.replace('/')
    return
  }

  // ── Modo empleado: empleado/admin que visita tienda3d desde gestion3d ─────
  // gestion3d pasa ?empleado=1&nombre=<nombre>&rol=<rol>&back=<url>
  const modoEmpleado = params.get('empleado')
  if (modoEmpleado === '1') {
    const nombre  = params.get('nombre')  || 'Empleado'
    const rol     = params.get('rol')     || 'empleado'
    const backUrl = params.get('back') || 'http://localhost/ODI3D/gestion3d/index.html'
    auth.setEmpleadoMode(nombre, rol, backUrl)
    // Limpiar params del historial
    const url = new URL(location.href)
    ;['empleado', 'nombre', 'rol', 'back'].forEach(k => url.searchParams.delete(k))
    history.replaceState(null, '', url.toString())
  }
})
</script>

<template>
  <div class="min-h-screen bg-odi-negro">
    <NavBar />
    <RouterView />
  </div>
</template>
