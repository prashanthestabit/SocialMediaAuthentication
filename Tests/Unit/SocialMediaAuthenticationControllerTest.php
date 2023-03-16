<?php

namespace Modules\SocialMediaAuthentication\Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class SocialMediaAuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;



        /**
     * Test the redirectToProvider method in the SocialMediaAuthenticationController
     * should return the correct redirect URL.
     *
     * @return void
     */
    public function test_redirect_to_valid_provider_method()
    {
        $driver = 'google';
        $redirectUrl = url('/').
        '/api/auth/google/callback?
        code=4%2F0AWtgzh4xKNgeCvgr-b3Pi3qPZUqkltzWtCn2cgH4RlCXUzn5zX76A44t3TNxyrD2yd8wWQ&scope=
        email+profile+openid+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+
        https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&authuser=0&prompt=consent';

        Socialite::shouldReceive('driver->stateless->redirect->getTargetUrl')
            ->andReturn($redirectUrl);

        $response = $this->get(route('auth.driver', $driver));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'url' => $redirectUrl,
            ]);
    }



    /**
     * Test if an error response is returned when an invalid provider is given.
     *
     * @return void
     */
    public function test_handle_provider_callback_with_invalid_provider()
    {
        $driver = 'invalid_provider';

        // Mock the Socialite driver method to return an exception
        Socialite::shouldReceive('driver')->with('invalid_provider')->andThrow(new \InvalidArgumentException());

        // Make a request to the handleProviderCallback method with an invalid provider
        $response = $this->get(route('auth.driver',$driver));
        $response->assertStatus(401);
        $response->assertJson([
            'status' => false,
            'message' => __('socialmediaauthentication::messages.unable_auth'),
        ]);
    }

    /**
     * Test the handleProviderCallback method in the SocialMediaAuthenticationController
     * should return the access token for the user.
     *
     * @return void
     */
    public function test_handle_provider_callback_method()
    {
        $driver = 'google';

        // Create a user
        $user = User::factory()->create();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($user);

        $response = $this->get(route('auth.callback',$driver));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'status','access_token'
        ]);
        $response->assertJson([
            'status' => true,
        ]);

        //Check log table data in database
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

    }
}
