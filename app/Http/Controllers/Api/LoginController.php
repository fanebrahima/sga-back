<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => $validator->errors()
                ], 
                422
            );
        }

        $user = User::join('profils', 'profils.id', '=', 'users.profil_id')
                    ->join('statuses', 'statuses.id', '=', 'users.status_id')
                    ->select("users.*","profils.label as profil_label","profils.value as profil_value","statuses.label as status_label","statuses.value as status_value")
                    ->where('email', $request->all()['email'])
                    ->where('users.status_id', 1)
                    ->first();

        // Check Password
        if (!$user || !Hash::check($request->all()['password'], $user->password)) {
            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => 'Email ou mot de passe invalide !'
                ], 
                400
            );
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        return new JsonResponse(
                [
                    'success' => true, 
                    'token' => $token,
                    'user' => $user
                ], 
                200
            );
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return new JsonResponse(
            [
                'success' => true, 
                'message' =>'Logged Out Successfully'
            ], 
            200
        );

    }
}
