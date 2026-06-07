<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::where('visible', true)->with('imagenes');

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('q')) {
            $query->where('nombre', 'like', '%' . $request->q . '%');
        }

        $productos = $query->orderBy('nombre')->get()->map(fn($p) => [
            'id'              => $p->id,
            'nombre'          => $p->nombre,
            'descripcion'     => $p->descripcion,
            'precio_base'     => $p->precio,   // alias para el frontend
            'categoria'       => $p->categoria,
            'enlace_whatsapp' => $p->enlace_whatsapp,
            'imagen_principal' => $p->imagenes->sortBy('orden')->first()?->ruta,
        ]);

        return response()->json(['success' => true, 'data' => $productos]);
    }

    public function show(string $id)
    {
        $producto = Producto::where('visible', true)
            ->with([
                'imagenes' => fn($q) => $q->orderBy('orden'),
                'reviews'  => fn($q) => $q->where('aprobado', true)->with('cliente:id,nombre'),
            ])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => array_merge($producto->toArray(), [
            'precio_base' => $producto->precio,
        ])]);
    }
}
