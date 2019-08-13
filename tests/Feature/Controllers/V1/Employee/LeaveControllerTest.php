<?php

namespace Tests\Feature\Controllers\V1;

use App\Models\Leave;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LeaveControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function test_index_action_with_multi_page()
    {
        $userWithLeaves = $this->getUserWithLeaves(1, Role::ROLE_EMPLOYEE);
        $token = $this->makeToken($userWithLeaves);

        $this->get(route('v1.employee.leaves.index', ['page' => 1]), ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonCount('1')
        ;
    }

    /**
     * @test
     */
    public function test_show_action_for_not_owned_leave()
    {
        $userWithLeaves = $this->getUserWithLeaves(1,Role::ROLE_EMPLOYEE);
        $token = $this->makeToken($userWithLeaves);

        $otherUserWithLeaves = $this->getUserWithLeaves(1);
        $notOwnedLeave = $otherUserWithLeaves->leaves->first();

        $this->get(route('v1.employee.leaves.show', ['leaf' => $notOwnedLeave->id]), ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(403)
            ->assertJsonStructure(['message'])
        ;
    }

    /**
     * @test
     */
    public function test_show_action_for_owned_leave()
    {
        $userWithLeaves = $this->getUserWithLeaves(1, Role::ROLE_EMPLOYEE);
        $token = $this->makeToken($userWithLeaves);
        $ownedLeave = $userWithLeaves->leaves->first();

        $this->get(route('v1.employee.leaves.show', ['leaf' => $ownedLeave->id]), ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'start',
                    'end',
                    'status',
                    'created_at',
                    'updated_at',
                ]
            ])
        ;
    }

    /**
     * @test
     */
    public function test_create_action_with_bad_params()
    {
        $token = $this->getValidToken(Role::ROLE_EMPLOYEE);
        $payload = [
            'start' => 'not_valid_data',
            'end' => 'not_valid_data',
        ];

        $this->post(route('v1.employee.leaves.store'), $payload, ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(400)
        ;
    }

    /**
     * @test
     */
    public function test_create_action_with_bad_credentials()
    {
        $notValidToken = $this->getNotValidToken();
        $leave = factory(Leave::class)->make();
        $payload = [
            'start' => $leave->start,
            'end' => $leave->end,
        ];

        $this->post(route('v1.employee.leaves.store'), $payload, ['Authorization' => 'Bearer ' . $notValidToken, 'Accept' => 'application/json'])
            ->assertStatus(401)
        ;
    }

//    /**
//     * @test
//     */
//    public function test_create_action_successfully()
//    {
//        $user = $this->getUserWithLeaves(0, Role::ROLE_EMPLOYEE);
//        //create 5 manager for user's department.
//        $managersCount = 5;
//        for ($i = 0; $i < $managersCount; ++$i) {
//            $this->createUser(Role::ROLE_MANAGER, $user->department)->toArray();
//        }
//
//        $token = $this->makeToken($user);
//        $leave = factory(Leave::class)->make();
//        $payload = [
//            'start' => $leave->start,
//            'end' => $leave->end,
//        ];
//
//        $res = $this->post(route('v1.employee.leaves.store'), $payload, ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json']);
//        echo $res->baseResponse;
//        $res->assertStatus(201)
//            ->assertJsonStructure([
//                'data' => [
//                    'id',
//                    'start',
//                    'end',
//                    'status',
//                    'created_at',
//                    'updated_at',
//                ]
//            ])
//        ;
//    }

    /**
     * @test
     */
    public function test_cancel_action_not_exist_leave()
    {
        $token = $this->getValidToken(Role::ROLE_EMPLOYEE);

        $this->patch(route('v1.employee.leaves.cancel', ['leaf' => 0]), [], ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(404);
        ;
    }

    /**
     * @test
     */
    public function test_cancel_action_not_owned_leave()
    {
        $token = $this->getValidToken(Role::ROLE_EMPLOYEE);

        $otherUserWithLeaves = $this->getUserWithLeaves(1);
        $notOwnedLeave = $otherUserWithLeaves->leaves->first();

        $this->patch(route('v1.employee.leaves.cancel', ['leaf' => $notOwnedLeave->id]), [], ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(403)
            ->assertJsonStructure(['message'])
        ;
    }

    /**
     * @test
     */
    public function test_cancel_action_not_possible()
    {
        $user = $this->getUserWithLeaves(1, Role::ROLE_EMPLOYEE);
        $token = $this->makeToken($user);
        $leave = $user->leaves->first();
        $leave->setStatus(Leave::STATUS_REJECTED);
        $leave->save();

        $this->patch(route('v1.employee.leaves.cancel', ['leaf' => $leave->id]), [], ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJsonStructure(['message'])
        ;
    }

    /**
     * @test
     */
    public function test_cancel_action_successfully()
    {
        $user = $this->getUserWithLeaves(1, Role::ROLE_EMPLOYEE);
        $token = $this->makeToken($user);
        $leave = $user->leaves->first();

        $this->patch(route('v1.employee.leaves.cancel', ['leaf' => $leave->id]), [], ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure(['message'])
        ;
    }
}
