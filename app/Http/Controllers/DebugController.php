<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class DebugController extends Controller
{
    public function checkAuth(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ] : null,
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'headers' => [
                'X-CSRF-TOKEN' => $request->header('X-CSRF-TOKEN'),
                'X-Requested-With' => $request->header('X-Requested-With'),
            ]
        ]);
    }
}
