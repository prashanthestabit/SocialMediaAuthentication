<?php

namespace Modules\SocialMediaAuthentication\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\SocialMediaAuthentication\Entities\Provider;
use Modules\SocialMediaAuthentication\Interface\SocialMediaInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class SocialMediaRepositories
 * This class is responsible for handling database operations for Social Media Authentication.
 */
class SocialMediaRepositories implements SocialMediaInterface
{
    /**
     * Generate a response with the given status, message, data and status code.
     *
     * @param array $responseData
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseMessage($responseData, $statusCode)
    {
        return response()->json($responseData, $statusCode);
    }

    /**
     * Find a Provider based on the given conditions.
     *
     * @param array $condition
     * @return \Modules\SocialMediaAuthentication\Entities\Provider|null
     */
        public function findProvider($condition)
        {
            return Provider::where($condition)->first();
        }

    /**
     * Find a User based on the given conditions.
     *
     * @param array $condition
     * @return \App\Models\User|null
     */
    public function findUser($condition)
    {
        return User::where($condition)->first();
    }

    /**
     * Create a User with the given data.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createUser($data)
    {
        return User::create($data);
    }

    /**
     * Create a Provider with the given data.
     *
     * @param array $data
     * @return \Modules\SocialMediaAuthentication\Entities\Provider
     */
    public function createProvider($data)
    {
        return Provider::create($data);
    }

    /**
     * Get the access token for the User based on the given Provider and Driver.
     *
     * @param \App\Models\User|null $user
     * @param \Modules\SocialMediaAuthentication\Entities\Provider|null $provider
     * @param string $driver
     * @return string
     */
    public function getUserToken($findUser, $provider, $driver,$user)
    {
        if ($findUser && $provider) {
            Auth::login($findUser);
            return JWTAuth::fromUser($findUser);
        }else if ($findUser && !$provider) {

            $this->saveProvider($findUser, $driver, $user);
            return JWTAuth::fromUser($findUser);

        }else{
            $newUser = $this->createUser([
                'name' => $user->name ?? '',
                'email' => $user->email,
                'password' => Hash::make('password'),
                'created_at' => now(),
            ]);

            $this->saveProvider($newUser, $driver, $user);
            Auth::login($newUser);
            return JWTAuth::fromUser($newUser);
        }


    }

    /**
     * Save a Provider with the given User, Driver and Provider information.
     *
     * @param \App\Models\User $newUser
     * @param string $driver
     * @param mixed $user
     * @return \Modules\SocialMediaAuthentication\Entities\Provider
     */
    private function saveProvider($newUser, $driver, $user)
    {
        return $this->createProvider([
            'user_id' => $newUser->id,
            'provider_name' => $driver,
            'provider_id' => $user->id,
            'created_at' => now(),
        ]);
    }
}
