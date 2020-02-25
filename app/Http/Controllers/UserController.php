<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['user']);
    }

    public function user()
    {
        return response()->json([
            'user' => auth()->user()
        ]);
    }

    public function rating()
    {
        return User::orderBy('score', 'desc')->take(30)->get();
    }
}
