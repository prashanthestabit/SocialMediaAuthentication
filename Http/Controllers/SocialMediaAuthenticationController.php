<?php

namespace Modules\SocialMediaAuthentication\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use Modules\SocialMediaAuthentication\Repositories\SocialMediaRepositories;

/**
 * The SocialMediaAuthenticationController class handles authentication through social media platforms.
 * We using Socialite packege for this implementation
 */
class SocialMediaAuthenticationController extends Controller
{
    /**
     * The repository for social media authentication.
     * @var SocialMediaRepositories
     */
    protected $social;

    /**
     * Create a new instance of the controller.
     * @param SocialMediaRepositories $social The repository for social media authentication.
     */
    public function __construct(SocialMediaRepositories $social)
    {
        $this->social = $social;
    }

    /**
     * Redirect the user to the OAuth screen for the specified driver.
     *
     * @param string $driver The name of the social media driver to use.
     *
     * @return Response
     */
    public function redirectToProvider($driver)
    {
        try {
            // Redirect the user to the Driver OAuth screen
            $redirectUrl = Socialite::driver($driver)->stateless()->redirect()->getTargetUrl();

            return $this->social->responseMessage([
                'status' => true,
                'url' => $redirectUrl,
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return $this->social->responseMessage([
                'status' => false,
                'message' => __('socialmediaauthentication::messages.unable_auth'),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Handle the callback from
     * @param string $driver The name of the social media driver to use.
     * @return Response
     */
    public function handleProviderCallback(Request $request, $driver)
    {
        try {
            $user = Socialite::driver($driver)->stateless()->user();
        } catch (\Exception$e) {
            return $this->social->responseMessage([
                'status' => false,
                'message' => __('socialmediaauthentication::messages.unable_auth')], Response::HTTP_UNAUTHORIZED);
        }

        $provider = $this->social->findProvider(['provider_name' => $driver, 'provider_id' => $user->id]);

        $finduser = $this->social->findUser(['email' => $user->email]);

        $accessToken = $this->social->getUserToken($finduser, $provider, $driver, $user);

        // Return a JSON response with the token
        return $this->social->responseMessage([
            'status' => true,
            'access_token' => $accessToken,
        ], Response::HTTP_OK);
    }
}
