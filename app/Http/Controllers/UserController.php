<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function search(Request $request)
    {
        $request->validate(['username' => 'required|string|min:3']);
        $users = User::where('username', 'like', '%' . $request->username . '%')->get();
        return response()->json($users);
    }
}
