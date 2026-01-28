<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketImageController extends Controller
{
    /**
     * ==========================================================
     * UPLOAD DE IMAGEM (CTRL + V)
     * ==========================================================
     * ✔ Usado por usuário e operador
     * ✔ Retorna URL pública
     * ✔ Seguro (auth + validação)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:51200', // 50MB
        ]);

        // Armazena temporariamente
        $path = $request->file('upload')
            ->store('tickets/temp', 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
        ]);
    }
}
