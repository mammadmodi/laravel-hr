<?php

namespace Tests\Feature\Controllers\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Generates a valid token.
     *
     * @return string
     */
    private function getValidToken()
    {
        $user = factory(User::class)->create();

        return JWTAuth::fromUser($user);
    }

    /**
     * @return mixed
     */
    private function getNotValidToken()
    {
        $user = factory(User::class)->make(['name' => 'not_exist_user']);

        return JWTAuth::fromUser($user);
    }

    /**
     * @test
     */
    public function login_with_bad_credentials()
    {
        $response = $this->post('api/v1/auth/login', ['email' => 'badEmail@gmail.com', 'password' => 'wrong_password']);

        $response->assertStatus(401);
        $response->assertJsonStructure(['error']);
    }

    /**
     * @test
     */
    public function login_with_right_credentials()
    {
        $email = 'right@email.com';
        $password = 'right_password';
        $user = factory(User::class)->create([
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        $response = $this->post('api/v1/auth/login', ['email' => $user->email, 'password' => $password]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    /**
     * @test
     */
    public function successful_logout()
    {
        $validToken = $this->getValidToken();
        $this->get('api/v1/auth/logout', ['Authorization' => 'Bearer ' . $validToken, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure(['message'])
        ;
        //after successful logout last token should not be work.
        $notValidToken = $validToken;
        $this->get('api/v1/auth/logout', ['Authorization' => 'Bearer ' . $notValidToken, 'Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
        ;
    }

    /**
     * @test
     */
    public function successful_me()
    {
        $validToken = $this->getValidToken();
        $this->get('api/v1/auth/me', ['Authorization' => 'Bearer ' . $validToken, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'department'])
        ;
    }

    /**
     * @test
     */
    public function not_successful_me()
    {
        $notValidToken = $this->getNotValidToken();
        $this->get('api/v1/auth/me', ['Authorization' => 'Bearer ' . $notValidToken, 'Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
        ;
    }

    /**
     * @test
     */
    public function successful_refresh_token()
    {
        $validToken = $this->getValidToken();
        $this->get('api/v1/auth/refresh', ['Authorization' => 'Bearer ' . $validToken, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
        ;
    }

    /**
     * @test
     */
    public function not_successful_refresh_token()
    {
        $notValidToken = $this->getNotValidToken();
        $this->get('api/v1/auth/refresh', ['Authorization' => 'Bearer ' . $notValidToken, 'Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
        ;
    }
}
