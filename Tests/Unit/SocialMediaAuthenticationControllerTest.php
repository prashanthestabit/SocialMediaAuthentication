<?php

namespace Modules\SocialMediaAuthentication\Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Mockery as Mock;
use Laravel\Socialite\Facades\Socialite;
use Modules\SocialMediaAuthentication\Entities\Provider;
use Modules\SocialMediaAuthentication\Repositories\SocialMediaRepositories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class SocialMediaAuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $socialMediaRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->socialMediaRepository = Mock::mock(SocialMediaRepositories::class);
        $this->app->instance(SocialMediaRepositories::class, $this->socialMediaRepository);
    }

    public function tearDown(): void
    {
        Mock::close();
        parent::tearDown();
    }

        /**
     * Test the redirectToProvider method in the SocialMediaAuthenticationController
     * should return the correct redirect URL.
     *
     * @return void
     */
    public function test_redirect_to_valid_provider_method()
    {
        $driver = 'google';
        $redirectUrl = 'http://127.0.0.1:8000/api/auth/google/callback?code=4%2F0AWtgzh4xKNgeCvgr-b3Pi3qPZUqkltzWtCn2cgH4RlCXUzn5zX76A44t3TNxyrD2yd8wWQ&scope=email+profile+openid+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&authuser=0&prompt=consent';

        Socialite::shouldReceive('driver->stateless->redirect->getTargetUrl')
            ->andReturn($redirectUrl);

        $this->socialMediaRepository->shouldReceive('responseMessage')
            ->with([
                'status' => true,
                'url' => $redirectUrl,
            ], Response::HTTP_OK)
            ->once()
            ->andReturn(response()->json([
                'status' => true,
                'url' => $redirectUrl,
            ], Response::HTTP_OK));

        $response = $this->get(route('auth.driver',$driver));

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

        $this->socialMediaRepository->shouldReceive('responseMessage')
        ->with([
            'status' => false,
            'message' => __('socialmediaauthentication::messages.unable_auth')
        ], Response::HTTP_UNAUTHORIZED)
        ->once()
        ->andReturn(response()->json([
            'status' => false,
            'message' => __('socialmediaauthentication::messages.unable_auth')
        ], Response::HTTP_UNAUTHORIZED));

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
        $user = Mock::mock();
        // Create a user
        $user = User::factory()->create();
        $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($user);

        $this->socialMediaRepository->shouldReceive('findProvider')
            ->with([
                'provider_name' => $driver,
                'provider_id' => $user->id,
            ])
            ->once()
            ->andReturn(new Provider());

    //     $this->socialMediaRepository->shouldReceive('findUser')
    //         ->with([
    //             'email' => $user->email,
    //         ])
    //         ->once()
    //         ->andReturn(null);

    //     $this->socialMediaRepository->shouldReceive('createUser')
    //         ->with([
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'password' => null,
    //         ])
    //         ->once()
    //         ->andReturn(new \App\Models\User());
     }
}
