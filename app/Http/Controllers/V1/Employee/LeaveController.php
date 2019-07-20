<?php

namespace App\Http\Controllers\V1\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LeaveController extends Controller
{
    /**
     * @var LeaveRepositoryInterface
     */
    private $leaveRepository;

    /**
     * Create a new LeaveController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->leaveRepository = app(LeaveRepositoryInterface::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $this->authorize('indexOwn', Leave::class);
        $page = (int)$request->get('page') ?? 1;

        return $this->leaveRepository->getUsersLeaves($user, 10, $page);
    }
//
//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return \Illuminate\Http\Response
//     */
//    public function store(Request $request)
//    {
//        //
//    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param  \App\Models\Leave  $leave
//     * @return \Illuminate\Http\Response
//     */
//    public function show(Leave $leave)
//    {
//        //
//    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \App\Models\Leave  $leave
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, Leave $leave)
//    {
//        //
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  \App\Models\Leave  $leave
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(Leave $leave)
//    {
//        //
//    }
}
