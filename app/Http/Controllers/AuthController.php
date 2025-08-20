<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate and create the user
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'type' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        $data['token'] =  $user->createToken('MyApp')->plainTextToken;
        $data['user']['name'] =  $user->name;
        $data['user']['username'] =  $user->username;

        return response()->json($data, 200);
    }

    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();

            $data['token'] =  $user->createToken('MyApp')->plainTextToken;
            $data['user']['name'] =  $user->name;
            $data['user']['username'] =  $user->username;

            return response()->json($data, 200);
        }else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
}
