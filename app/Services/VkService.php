<?php


namespace App\Services;


use App\Http\Controllers\Auth\LoginController;
use App\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class VkService
{
    /**
     * @param array $userData
     * @param string $email
     * @return User
     */
    public function authFromVK(array $userData, string $email): User
    {
        $user = $this->getUserInstance($userData, $email);

        auth()->login($user, true);

        return $user;
    }

    /**
     * @param array $userData
     * @param string $email
     * @return User
     */
    private function getUserInstance(array $userData, string $email): User
    {
        try {
            return User::where('vk_id', $userData['id'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = null;

            $user = User::create([
                'vk_id' => $userData['id'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
            ]);

            return $user;
        }
    }

    /**
     * @param Authenticatable $user
     * @param string $userToken
     * @return bool
     */
    public function setBigAvatarUri(Authenticatable $user, string $userToken): bool
    {
        if (is_null($user->avatar_url)) {
            return $user->update([
                'avatar_url' => $this->getBigAvatarUri($userToken)
            ]);
        }

        return true;
    }

    /**
     * @param string $userToken
     * @return string|null
     */
    public function getBigAvatarUri(string $userToken): ?string
    {
        return $this->getUserData($userToken, ['photo_max_orig'])
            ->photo_max_orig;
    }

    /**
     * @param string $userToken
     * @param array $fields
     * @return object|null
     */
    public function getUserData(string $userToken, array $fields = []): ?object
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://api.vk.com/method/users.get?', [
                'query' => [
                    'access_token' => $userToken,
                    'v' => '5.92',
                    'fields' => implode($fields)
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error("Cannot load user (token: {$userToken}) avatar");

            return null;
        }

        return json_decode(
            $response->getBody()->getContents()
        )->response[0];
    }
}
