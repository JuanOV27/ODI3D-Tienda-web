import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  scrollBehavior: () => ({ top: 0 }),
  routes: [
    { path: '/',           component: () => import('@/views/HomeView.vue') },
    { path: '/catalogo',   component: () => import('@/views/CatalogoView.vue') },
    { path: '/catalogo/:id', component: () => import('@/views/ProductoDetalle.vue') },
    { path: '/login',      component: () => import('@/views/LoginView.vue') },
    { path: '/registro',   component: () => import('@/views/RegistroView.vue') },
    { path: '/terminos',   component: () => import('@/views/TerminosView.vue') },
    { path: '/carrito',    component: () => import('@/views/CarritoView.vue') },
    {
      path: '/mi-cuenta',
      component: () => import('@/views/MiCuentaView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/solicitudes',
      component: () => import('@/views/SolicitudForm.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/solicitudes/:id',
      component: () => import('@/views/SolicitudDetalle.vue'),
      meta: { requiresAuth: true },
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.loggedIn) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }
})

export default router
