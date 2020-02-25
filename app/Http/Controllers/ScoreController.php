<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ScoreRequest;

class ScoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['setScore']);
    }

    public function setScore(ScoreRequest $request)
    {
        $authUser = auth()->user();
        $vk_id = $authUser->vk_id;

        $validated = $request->validated();

        $data = $vk_id.$validated['score'];
        $hashedData = md5(md5($data));

        if($hashedData !== $validated['code'])
        {
            return response()->json([
                'message' => 'Ошибка!',
                'request' => $request->all(),
                'validated' => $validated,
            ], 401);
        }

        if($authUser->score < $validated['score'])
        {
            $authUser->score = $validated['score'];
            $authUser->save();
        }

        return response()->json([
            'request' => $request->all(),
            'validated' => $validated,
        ], 201);
    }
}
