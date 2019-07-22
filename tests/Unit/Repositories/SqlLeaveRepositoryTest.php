<?php

namespace Tests\Unit\Repositories;

use App\Models\Department;
use App\Models\Role;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use App\Repositories\Leaves\SqlLeaveRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SqlLeaveRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return SqlLeaveRepository
     */
    private function getSqlLeaveRepository()
    {
        return $this->app->get(LeaveRepositoryInterface::class);
    }

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

        $this->assertEquals(
            $userWithLeaves->leaves->sortByDesc('id')->all(),
            $this->getSqlLeaveRepository()->getUsersLeaves($userWithLeaves, $perPage, $page)
        );
    }

    /**
     * @test
     */
    public function get_empty_leaves()
    {
        $userWithOutLeaves = $this->getUserWithLeaves(0);
        $leaves = $this->getSqlLeaveRepository()->getUsersLeaves($userWithOutLeaves);

        $this->assertEquals(count($leaves), $userWithOutLeaves->leaves->count());
    }

    /**
     * @test
     */
    public function get_managers_for_not_existed_department()
    {
        $notExistedDepartment = factory(Department::class)->make();
        $managers = $this->getSqlLeaveRepository()->getDepartmentManagers($notExistedDepartment);

        $this->assertEmpty($managers);
    }

    /**
     * @test
     */
    public function get_managers_successfully()
    {
        $department = factory(Department::class)->create();
        $managersCount = 5;
        $managers = [];
        for ($i = 0; $i < $managersCount; ++$i) {
            $managers[] = $this->createUser(Role::ROLE_MANAGER, $department)->toArray();
        }

        $managersCollection = $this->getSqlLeaveRepository()->getDepartmentManagers($department);
        $this->assertEquals($managersCollection->count(), $managersCount);
    }
}
