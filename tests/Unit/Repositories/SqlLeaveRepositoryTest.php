<?php

namespace Tests\Unit\Repositories;

use App\Repositories\Leaves\LeaveRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SqlLeaveRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function get_leaves_successfully()
    {
        $userWithLeaves = $this->getUserWithLeaves(10);
        $perPage = 10;
        $page = 1;
        $db = DB::shouldReceive('table')->with('leaves')
            ->andReturnSelf()
            ->getMock();

        $db->shouldReceive('where')
            ->with('user_id', '=', $userWithLeaves->id)
            ->andReturnSelf();

        $db->shouldReceive('orderBy')
            ->with('id', 'DESC')
            ->andReturnSelf();

        $db->shouldReceive('paginate')
            ->with($perPage, ['*'], 'page', $page)
            ->andReturnSelf();

        $db->shouldReceive('items')
            ->andReturn($userWithLeaves->leaves->sortByDesc('id')->all());

        /** @var LeaveRepositoryInterface $leavesRepo */
        $leavesRepo = $this->app->get(LeaveRepositoryInterface::class);
        $this->assertEquals(
            $userWithLeaves->leaves->sortByDesc('id')->all(),
            $leavesRepo->getUsersLeaves($userWithLeaves, $perPage, $page)
        );
    }
}
