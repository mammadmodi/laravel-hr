<?php

namespace App\Http\Controllers\V1\Manager;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use App\Http\Resources\Leave as LeaveResource;

class UserLeaveController extends Controller
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
     * Display a listing of employee's leaves.
     *
     * @param Request $request
     * @param User $user
     * @return AnonymousResourceCollection|Response
     */
    public function index(Request $request, User $user)
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = auth()->user();
        if ($authenticatedUser->can('index', [Leave::class, $user])) {
            $page = (int)$request->get('page') ?? 1;

            return LeaveResource::collection($this->leaveRepository->getUsersLeaves($user, 10, $page));
        } else {
            return response(['message' => 'permission denied'], 403);
        }
    }
//
//    /**
//     * Tries to store a leave in database.
//     *
//     * @param LeaveRequest $request
//     * @return LeaveResource
//     */
//    public function store(LeaveRequest $request)
//    {
//        /** @var User $user */
//        $user = auth()->user();
//        if ($user->can('create', Leave::class)) {
//
//            $leave = new Leave($request->all());
//            $leave->user_id = auth()->user()->id;
//
//            if ($leave->save()) {
//                return LeaveResource::make($leave);
//            } else {
//                return response('Bad request.', 400);
//            }
//        } else {
//            return response(['message' => 'permission denied'], 403);
//        }
//    }
//
//    /**
//     * Shows leave of user.
//     *
//     * @param Leave $leaf
//     * @return LeaveResource
//     */
//    public function show(Leave $leaf)
//    {
//        /** @var User $user */
//        $user = auth()->user();
//        if ($user->can('viewOwn', $leaf)) {
//            return LeaveResource::make($leaf);
//        } else {
//            return response(['message' => 'permission denied'], 403);
//        }
//    }
//
//    /**
//     * Tries to cancel a leave.
//     *
//     * @param Leave $leaf
//     * @return ResponseFactory|Response
//     */
//    public function cancel(Leave $leaf)
//    {
//        /** @var User $user */
//        $user = auth()->user();
//        if ($user->can('cancelOwn', $leaf)) {
//            $workflow = Leave::getWorkflow($leaf);
//
//            if ($workflow->can($leaf, Leave::TRANSITION_CANCEL)) {
//                Leave::getWorkflow($leaf)->apply($leaf, Leave::TRANSITION_CANCEL);
//                if ($leaf->save()) {
//                    return response(['message' => 'leave successfully canceled.'], 200);
//                } else {
//                    return response(['message' => 'server error please request later.'], 500);
//                }
//            } else {
//                return response(['message' => 'leave can not be canceled.'], 400);
//            }
//        } else {
//            return response(['message' => 'permission denied'], 403);
//        }
//    }
}
