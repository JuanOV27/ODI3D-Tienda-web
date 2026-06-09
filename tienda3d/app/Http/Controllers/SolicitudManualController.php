<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\SolicitudCotizacion;
use App\Models\SolicitudItem;
use Illuminate\Http\Request;

/**
 * Gestión de solicitudes creadas manualmente por empleados/admins desde gestion3d.
 * Estas rutas están bajo /interno/ — sin Sanctum, solo modulo:solicitudes.
 */
class SolicitudManualController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // Crear solicitud manual
    // ──────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_solicitud'    => 'required|in:cotizacion,compra',
            'cliente_nombre'    => 'required|string|max:255',
            'cliente_telefono'  => 'required|string|max:50',
            'cliente_whatsapp'  => 'nullable|string|max:50',
            'cliente_email'     => 'nullable|email|max:255',
            'descripcion'       => 'nullable|string|max:2000',
            'material_preferido'=> 'nullable|string|max:100',
            'cantidad'          => 'nullable|integer|min:1',
            'uso_final'         => 'nullable|string|max:500',
            // Ítems opcionales (compra o multi-ítem cotización)
            'items'                           => 'nullable|array',
            'items.*.producto_nombre'         => 'required_with:items|string|max:255',
            'items.*.cantidad'                => 'nullable|integer|min:1',
            'items.*.producto_precio_unitario'=> 'nullable|numeric|min:0',
            'items.*.tipo_item'               => 'nullable|in:catalogo,personalizado',
            'items.*.material'                => 'nullable|string|max:100',
            'items.*.descripcion_extra'       => 'nullable|string|max:1000',
        ]);

        // Detectar si ya existe un cliente con ese teléfono
        $clienteExistente = null;
        $vinculacionPendiente = false;
        if (!empty($data['cliente_telefono'])) {
            $clienteExistente = Cliente::where('telefono', $data['cliente_telefono'])
                ->where('activo', true)
                ->first();
            if ($clienteExistente) {
                $vinculacionPendiente = true;
            }
        }

        $solicitud = SolicitudCotizacion::create([
            'id'                 => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
            'cliente_id'         => $clienteExistente?->id,
            'origen'             => 'manual',
            'tipo_solicitud'     => $data['tipo_solicitud'],
            'cliente_nombre'     => $data['cliente_nombre'],
            'cliente_telefono'   => $data['cliente_telefono'],
            'cliente_whatsapp'   => $data['cliente_whatsapp'] ?? $data['cliente_telefono'],
            'cliente_email'      => $data['cliente_email'] ?? null,
            'descripcion'        => $data['descripcion'] ?? null,
            'material_preferido' => $data['material_preferido'] ?? null,
            'cantidad'           => $data['cantidad'] ?? 1,
            'uso_final'          => $data['uso_final'] ?? null,
            'estado'             => 'recibida',
            'vinculacion_pendiente' => $vinculacionPendiente,
            'fecha_solicitud'    => now(),
        ]);

        // Crear ítems si vienen en el payload (para compras o solicitudes multi-ítem)
        $itemsCreados = [];
        if (!empty($data['items'])) {
            foreach ($data['items'] as $itemData) {
                if (empty(trim($itemData['producto_nombre'] ?? ''))) continue;

                $item = SolicitudItem::create([
                    'id'                      => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
                    'solicitud_id'            => $solicitud->id,
                    'tipo_item'               => $itemData['tipo_item'] ?? 'personalizado',
                    'producto_id'             => $itemData['producto_id'] ?? null,
                    'producto_nombre'         => $itemData['producto_nombre'],
                    'producto_precio_unitario'=> isset($itemData['producto_precio_unitario'])
                                                ? (float) $itemData['producto_precio_unitario']
                                                : null,
                    'cantidad'                => (int) ($itemData['cantidad'] ?? 1),
                    'material'                => $itemData['material'] ?? null,
                    'descripcion_extra'       => $itemData['descripcion_extra'] ?? null,
                    'datos_cotizacion'        => $itemData['datos_cotizacion'] ?? null,
                    'created_at'              => now(),
                ]);
                $itemsCreados[] = $item->id;
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                   => $solicitud->id,
                'estado'               => $solicitud->estado,
                'vinculacion_pendiente'=> $vinculacionPendiente,
                'items_creados'        => count($itemsCreados),
                'cliente_vinculado'    => $clienteExistente ? [
                    'id'     => $clienteExistente->id,
                    'nombre' => $clienteExistente->nombre,
                    'email'  => $clienteExistente->email,
                ] : null,
            ],
            'message' => 'Solicitud creada correctamente.',
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Agregar ítems a una solicitud
    // ──────────────────────────────────────────────────────────────────────────
    public function storeItems(Request $request, string $solicitudId)
    {
        $solicitud = SolicitudCotizacion::findOrFail($solicitudId);

        $data = $request->validate([
            'items'                           => 'required|array|min:1',
            'items.*.tipo_item'               => 'required|in:catalogo,personalizado',
            'items.*.producto_id'             => 'nullable|string',
            'items.*.producto_nombre'         => 'required|string|max:255',
            'items.*.producto_precio_unitario'=> 'nullable|numeric|min:0',
            'items.*.cantidad'                => 'required|integer|min:1',
            'items.*.material'                => 'nullable|string|max:100',
            'items.*.descripcion_extra'       => 'nullable|string|max:1000',
            'items.*.datos_cotizacion'        => 'nullable|array',
        ]);

        $creados = [];
        foreach ($data['items'] as $itemData) {
            // Snapshot de precio desde catálogo si viene producto_id
            $precioUnitario = $itemData['producto_precio_unitario'] ?? null;
            if ($itemData['tipo_item'] === 'catalogo' && !empty($itemData['producto_id']) && $precioUnitario === null) {
                $producto = Producto::find($itemData['producto_id']);
                $precioUnitario = $producto?->precio;
            }

            $item = SolicitudItem::create([
                'id'                      => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
                'solicitud_id'            => $solicitud->id,
                'tipo_item'               => $itemData['tipo_item'],
                'producto_id'             => $itemData['producto_id'] ?? null,
                'producto_nombre'         => $itemData['producto_nombre'],
                'producto_precio_unitario'=> $precioUnitario,
                'cantidad'                => $itemData['cantidad'],
                'material'                => $itemData['material'] ?? null,
                'descripcion_extra'       => $itemData['descripcion_extra'] ?? null,
                'datos_cotizacion'        => $itemData['datos_cotizacion'] ?? null,
                'created_at'              => now(),
            ]);

            $creados[] = array_merge($item->toArray(), ['subtotal' => $item->subtotal]);
        }

        $total = array_sum(array_column($creados, 'subtotal'));

        return response()->json([
            'success' => true,
            'data'    => ['items' => $creados, 'total' => $total],
            'message' => count($creados) . ' ítem(s) agregados.',
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Listar ítems de una solicitud
    // ──────────────────────────────────────────────────────────────────────────
    public function indexItems(string $solicitudId)
    {
        $solicitud = SolicitudCotizacion::findOrFail($solicitudId);

        $items = $solicitud->items()->get()->map(fn($item) => array_merge(
            $item->toArray(),
            ['subtotal' => $item->subtotal]
        ));

        $total = $items->sum('subtotal');

        return response()->json([
            'success' => true,
            'data'    => ['items' => $items, 'total' => $total],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Vincular / desvincular cliente de solicitud manual
    // ──────────────────────────────────────────────────────────────────────────
    public function vincularCliente(Request $request, string $solicitudId)
    {
        $solicitud = SolicitudCotizacion::findOrFail($solicitudId);

        $data = $request->validate([
            'confirmar'  => 'required|boolean',
            'cliente_id' => 'nullable|string',
        ]);

        if ($data['confirmar'] && !empty($data['cliente_id'])) {
            $cliente = Cliente::findOrFail($data['cliente_id']);
            $solicitud->update([
                'cliente_id'           => $cliente->id,
                'vinculacion_pendiente'=> false,
            ]);
            $mensaje = "Solicitud vinculada a {$cliente->nombre}";
        } else {
            // Rechazado — limpiar flag pero mantener sin cliente_id
            $solicitud->update(['vinculacion_pendiente' => false]);
            $mensaje = 'Vinculación rechazada. La solicitud permanece sin cliente.';
        }

        return response()->json(['success' => true, 'message' => $mensaje]);
    }
}
