<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserRatingResource;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['user', 'rating']);
    }

    public function user()
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
            'top' => User::orderBy('score', 'desc')->where('score', '>', auth()->user()->score)->count() + 1,
        ]);
    }

    public function rating()
    {
        return UserRatingResource::collection(User::orderBy('score', 'desc')->take(60)->get());
    }
}
