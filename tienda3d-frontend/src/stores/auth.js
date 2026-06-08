import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token   = ref(sessionStorage.getItem('odi_token') || null)
  const cliente = ref(JSON.parse(sessionStorage.getItem('odi_cliente') || 'null'))

  // Modo empleado: cuando un usuario de gestion3d visita tienda3d desde el panel interno.
  // No crea sesión de cliente; solo activa el banner "Vista interna".
  const empleado = ref(JSON.parse(sessionStorage.getItem('odi_empleado') || 'null'))

  const loggedIn    = computed(() => !!token.value)
  const enModoEmpleado = computed(() => !!empleado.value)

  function setAuth(t, c) {
    token.value   = t
    cliente.value = c
    sessionStorage.setItem('odi_token',   t)
    sessionStorage.setItem('odi_cliente', JSON.stringify(c))
  }

  function logout() {
    token.value   = null
    cliente.value = null
    sessionStorage.removeItem('odi_token')
    sessionStorage.removeItem('odi_cliente')
  }

  /**
   * Activa el modo empleado: guarda nombre, rol y URL de regreso a gestion3d.
   * @param {string} nombre    Nombre del empleado/admin
   * @param {string} rol       'admin' | 'empleado'
   * @param {string} backUrl   URL completa a la que volver (ej. http://localhost/gestion3d/index.html)
   */
  function setEmpleadoMode(nombre, rol, backUrl) {
    const datos = { nombre, rol, backUrl }
    empleado.value = datos
    sessionStorage.setItem('odi_empleado', JSON.stringify(datos))
  }

  function clearEmpleadoMode() {
    empleado.value = null
    sessionStorage.removeItem('odi_empleado')
  }

  return { token, cliente, loggedIn, empleado, enModoEmpleado, setAuth, logout, setEmpleadoMode, clearEmpleadoMode }
})
