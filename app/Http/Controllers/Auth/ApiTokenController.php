<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ApiTokenController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $credential = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credential)) {
            $token = Str::random(80);

            $request->user()->forceFill([
                'api_token' => hash('sha256', $token),
            ])->save();

            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Unauthorised. Username dan password salah'], 401);
    }
}
