<?php

namespace App\Http\Controllers;

use App\Services\VkService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class VkAuthController extends Controller
{
    /**
     * Redirect the user to the Vk authentication page.
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver('vkontakte')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Vk.
     *
     * @param VkService $vkService
     * @return RedirectResponse
     */
    public function handleProviderCallback(
        VkService $vkService
    )
    {
        try {
            $user = Socialite::driver('vkontakte')->stateless()->user();
        } catch (InvalidStateException $e) {  // If returned data is invalid
            return redirect()->home();
        } catch (ClientException $e) { // If access denied
            return redirect()->home();
        }

        $authUser = $vkService->authFromVK($user->user, $user->accessTokenResponseBody['email']);
        $vkService->setBigAvatarUri($authUser, $user->accessTokenResponseBody['access_token']);
        $token = $vkService->setToken($authUser);

        return response()->json([
            'message' => 'Успешный вход.',
            'token' => $token
        ],201);
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();

        return response()->json([
            'message' => 'Успешный выход!'
        ], 201);
    }
}
