import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCarritoStore = defineStore('carrito', () => {
  const items = ref(JSON.parse(sessionStorage.getItem('odi_carrito') || '[]'))

  const total     = computed(() => items.value.reduce((s, i) => s + i.precio_base * i.cantidad, 0))
  const totalItems = computed(() => items.value.reduce((s, i) => s + i.cantidad, 0))

  function agregar(producto) {
    const existe = items.value.find(i => i.id === producto.id)
    if (existe) {
      existe.cantidad++
    } else {
      items.value.push({ ...producto, cantidad: 1 })
    }
    _persist()
  }

  function quitar(id) {
    items.value = items.value.filter(i => i.id !== id)
    _persist()
  }

  function incrementar(id) {
    const item = items.value.find(i => i.id === id)
    if (item) item.cantidad++
    _persist()
  }

  function decrementar(id) {
    const item = items.value.find(i => i.id === id)
    if (!item) return
    if (item.cantidad <= 1) {
      quitar(id)
    } else {
      item.cantidad--
      _persist()
    }
  }

  function actualizarCantidad(id, cantidad) {
    const item = items.value.find(i => i.id === id)
    if (!item) return
    const nueva = Math.floor(Number(cantidad))
    if (!nueva || nueva < 1) {
      quitar(id)
    } else {
      item.cantidad = nueva
      _persist()
    }
  }

  function vaciar() {
    items.value = []
    _persist()
  }

  function _persist() {
    sessionStorage.setItem('odi_carrito', JSON.stringify(items.value))
  }

  return { items, total, totalItems, agregar, quitar, incrementar, decrementar, actualizarCantidad, vaciar }
})
