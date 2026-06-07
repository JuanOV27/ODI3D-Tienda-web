<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Producto;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(string $productoId)
    {
        Producto::where('visible', true)->findOrFail($productoId);

        $reviews = Review::where('producto_id', $productoId)
            ->where('aprobado', true)
            ->with('cliente:id,nombre')
            ->orderByDesc('fecha')
            ->get();

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    public function store(Request $request, string $productoId)
    {
        Producto::where('visible', true)->findOrFail($productoId);

        $data = $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'required|string|max:1000',
        ]);

        $ya = Review::where('producto_id', $productoId)
            ->where('cliente_id', $request->user()->id)
            ->exists();

        if ($ya) {
            return response()->json(['success' => false, 'error' => 'Ya tienes una reseña para este producto.'], 422);
        }

        $review = Review::create([
            'id'          => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
            'producto_id' => $productoId,
            'cliente_id'  => $request->user()->id,
            'calificacion'=> $data['calificacion'],
            'comentario'  => $data['comentario'],
            'aprobado'    => false,
            'fecha'       => now(),
        ]);

        return response()->json(['success' => true, 'data' => $review, 'message' => 'Reseña enviada. Será visible tras aprobación.'], 201);
    }
}
