<?php

namespace Tests\Feature\Controllers\V1;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeaveControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index_action_with_multi_page()
    {
        $userWithLeaves = $this->getUserWithLeaves(1, Role::ROLE_EMPLOYEE);
        $token = JWTAuth::fromUser($userWithLeaves);

        $this->get('api/v1/employee/leaves?$page=' . 1, ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJsonCount('1');
    }
}
