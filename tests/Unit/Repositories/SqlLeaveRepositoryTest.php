<?php

namespace Tests\Unit\Repositories;

use App\Models\Leave;
use App\Models\User;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SqlLeaveRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Gets an user with number of leaves.
     *
     * @param int $leavesNumber
     * @return User
     */
    private function getUserWithLeaves($leavesNumber = 10)
    {
        $user = factory(User::class)->create();
        factory(Leave::class, $leavesNumber)->create(['user_id' => $user->id]);

        return $user;
    }

    /**
     * @test
     */
    public function get_leaves_successfully()
    {
        $userWithLeaves = $this->getUserWithLeaves(10);
        $perPage = 10;
        $page = 1;
        $db = DB::shouldReceive('table')->with('leaves')->andReturnSelf()->getMock();

        $db->shouldReceive('where')
            ->with('user_id', '=', $userWithLeaves->id)
            ->andReturnSelf();

        $db->shouldReceive('orderBy')
            ->with('id', 'DESC')
            ->andReturnSelf();
        $db->shouldReceive('paginate')
            ->with($perPage, ['*'], 'page', $page)
            ->andReturnSelf();
        //TODO need to refactor

        /** @var LeaveRepositoryInterface $leavesRepo */
        $leavesRepo = $this->app->get(LeaveRepositoryInterface::class);
        $this->assertEquals($userWithLeaves->leaves->count(), count($leavesRepo->getUsersLeaves($userWithLeaves, $perPage, $page)));
    }
}
