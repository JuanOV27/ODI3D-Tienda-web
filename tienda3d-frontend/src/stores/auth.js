import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token   = ref(sessionStorage.getItem('odi_token') || null)
  const cliente = ref(JSON.parse(sessionStorage.getItem('odi_cliente') || 'null'))

  const loggedIn = computed(() => !!token.value)

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

  return { token, cliente, loggedIn, setAuth, logout }
})
