<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    // Obtener todos los usuarios
    public function index()
    {
        // Obtener todos los usuarios (se puede agregar paginación si es necesario)
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    // Activar o desactivar una cuenta de usuario
    public function toggleAccountStatus(Request $request, $id)
    {
        // Validar que el parámetro 'is_active' sea booleano
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Obtener el usuario
        $user = User::findOrFail($id);

        // Cambiar el estado de la cuenta
        $user->is_active = $request->input('is_active');
        $user->save();

        // Responder con el estado actualizado
        return response()->json(['message' => 'Account status updated successfully', 'user' => $user]);
    }
}
