const NUMERO = import.meta.env.VITE_WHATSAPP_NUMERO || '573147889080'

export function useWhatsApp() {
  function linkProducto(producto) {
    if (producto.enlace_whatsapp) {
      return producto.enlace_whatsapp
    }
    const texto = `Hola ODI3D! Me interesa el producto: *${producto.nombre}* (Ref: ${producto.id}). ¿Está disponible?`
    return `https://wa.me/${NUMERO}?text=${encodeURIComponent(texto)}`
  }

  function linkCarrito(items, total) {
    const lineas = items.map(i =>
      `• ${i.nombre}  x${i.cantidad}  —  $${Number(i.precio_base * i.cantidad).toLocaleString('es-CO')}`
    )
    const texto =
      `Hola ODI3D! Quiero hacer un pedido con estos productos del catálogo:\n\n` +
      `${lineas.join('\n')}\n\n` +
      `Total estimado: $${Number(total).toLocaleString('es-CO')} COP\n\n` +
      `¿Podrían confirmarme disponibilidad y los siguientes pasos para completar el pedido?`
    return `https://wa.me/${NUMERO}?text=${encodeURIComponent(texto)}`
  }

  function linkSolicitud(solicitudId) {
    const texto = `Hola ODI3D! Quiero hacer seguimiento de mi solicitud #${solicitudId}`
    return `https://wa.me/${NUMERO}?text=${encodeURIComponent(texto)}`
  }

  function linkGeneral(texto) {
    return `https://wa.me/${NUMERO}?text=${encodeURIComponent(texto)}`
  }

  return { linkProducto, linkCarrito, linkSolicitud, linkGeneral }
}
