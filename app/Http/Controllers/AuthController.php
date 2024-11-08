<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function validateRequest(array $rules, array $messages = [])
    {
        return Validator::make(request()->all(), $rules, $messages);
    }

    private function sendResponse(string $message, $data = null, int $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function login(Request $request)
    {
        $validator = $this->validateRequest([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse('Invalid credentials', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendResponse('Unauthorized', 'Invalid credentials', 401);
        }

        $token = $user->createToken('YourAppName')->plainTextToken;

        return $this->sendResponse('Login successful', ['token' => $token]);
    }

    public function register(Request $request)
    {
        $validator = $this->validateRequest([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse('Validation Error', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('YourAppName')->plainTextToken;

        return $this->sendResponse('User created successfully', [
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });

            return $this->sendResponse('Logged out successfully');
        } catch (\Exception $e) {
            return $this->sendResponse('Error', $e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->input('query');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();

        return $this->sendResponse('Search results', $users);
    }
}
