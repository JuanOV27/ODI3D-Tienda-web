<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'
import { STLLoader } from 'three/examples/jsm/loaders/STLLoader.js'
import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader.js'

const props = defineProps({
  buffer: { type: ArrayBuffer, required: true },
  extension: { type: String, required: true },
})

const contenedor = ref(null)
let renderer, scene, camera, controls, frameId

function centrarYEncuadrar(objeto) {
  const caja = new THREE.Box3().setFromObject(objeto)
  const centro = caja.getCenter(new THREE.Vector3())
  const tamano = caja.getSize(new THREE.Vector3())
  objeto.position.sub(centro)

  const radio = Math.max(tamano.x, tamano.y, tamano.z) || 1
  camera.position.set(radio * 1.6, radio * 1.2, radio * 1.6)
  camera.near = radio / 100
  camera.far = radio * 100
  camera.updateProjectionMatrix()
  controls.target.set(0, 0, 0)
  controls.update()
}

function materialEstandar() {
  return new THREE.MeshStandardMaterial({ color: 0xf5c518, metalness: 0.15, roughness: 0.6 })
}

function cargarModelo() {
  const ext = props.extension.toLowerCase()
  if (ext === 'stl') {
    const geometria = new STLLoader().parse(props.buffer)
    geometria.computeVertexNormals()
    const malla = new THREE.Mesh(geometria, materialEstandar())
    scene.add(malla)
    centrarYEncuadrar(malla)
  } else if (ext === 'obj') {
    const texto = new TextDecoder().decode(props.buffer)
    const objeto = new OBJLoader().parse(texto)
    objeto.traverse((nodo) => {
      if (nodo.isMesh) nodo.material = materialEstandar()
    })
    scene.add(objeto)
    centrarYEncuadrar(objeto)
  }
}

function animar() {
  frameId = requestAnimationFrame(animar)
  controls.update()
  renderer.render(scene, camera)
}

function ajustarTamano() {
  if (!contenedor.value) return
  const ancho = contenedor.value.clientWidth
  const alto = contenedor.value.clientHeight
  camera.aspect = ancho / alto
  camera.updateProjectionMatrix()
  renderer.setSize(ancho, alto)
}

onMounted(() => {
  const ancho = contenedor.value.clientWidth
  const alto = contenedor.value.clientHeight

  scene = new THREE.Scene()
  scene.background = new THREE.Color(0x1a1a1a)

  camera = new THREE.PerspectiveCamera(45, ancho / alto, 0.1, 1000)

  renderer = new THREE.WebGLRenderer({ antialias: true })
  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.setSize(ancho, alto)
  contenedor.value.appendChild(renderer.domElement)

  scene.add(new THREE.AmbientLight(0xffffff, 0.6))
  const luz1 = new THREE.DirectionalLight(0xffffff, 0.8)
  luz1.position.set(1, 1, 1)
  scene.add(luz1)
  const luz2 = new THREE.DirectionalLight(0xffffff, 0.4)
  luz2.position.set(-1, -0.5, -1)
  scene.add(luz2)

  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true

  cargarModelo()
  animar()

  window.addEventListener('resize', ajustarTamano)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', ajustarTamano)
  cancelAnimationFrame(frameId)
  controls?.dispose()
  renderer?.dispose()
  if (renderer && contenedor.value?.contains(renderer.domElement)) {
    contenedor.value.removeChild(renderer.domElement)
  }
})
</script>

<template>
  <div ref="contenedor" class="w-full h-full rounded-lg overflow-hidden"></div>
</template>
