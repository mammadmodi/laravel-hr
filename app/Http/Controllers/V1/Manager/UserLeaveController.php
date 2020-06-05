<?php

namespace App\Http\Controllers\V1\Manager;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
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
        if (!$authenticatedUser->can('index', [Leave::class, $user])) {
            return response(['message' => 'permission denied'], 403);
        }
        $page = (int)$request->get('page') ?? 1;

        return LeaveResource::collection($this->leaveRepository->getUsersLeaves($user, 10, $page));
    }


    /**
     * Shows leave of user.
     *
     * @param User $user
     * @param Leave $leaf
     * @return LeaveResource|ResponseFactory|Response
     */
    public function show(User $user, Leave $leaf)
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = auth()->user();
        if (!$authenticatedUser->can('view', [$leaf, $user])) {
            return response(['message' => 'permission denied'], 403);
        }

        return LeaveResource::make($leaf);
    }

    /**
     * Tries to approve a leave.
     *
     * @param User $user
     * @param Leave $leaf
     * @return ResponseFactory|Response
     */
    public function approve(User $user, Leave $leaf)
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = auth()->user();
        if (!$authenticatedUser->can('approve', [$leaf, $user])) {
            return response(['message' => 'permission denied'], 403);
        }

        $workflow = Leave::getWorkflow($leaf);
        if (!$workflow->can($leaf, Leave::TRANSITION_APPROVE)) {
            return response(['message' => 'leave can not be approved.'], 400);
        }

        Leave::getWorkflow($leaf)->apply($leaf, Leave::TRANSITION_APPROVE);
        if (!$leaf->save()) {
            return response(['message' => 'server error please request later.'], 500);
        }

        return response(['message' => 'leave successfully approved.'], 200);
    }

    /**
     * Tries to reject a leave.
     *
     * @param User $user
     * @param Leave $leaf
     * @return ResponseFactory|Response
     */
    public function reject(User $user, Leave $leaf)
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = auth()->user();
        if (!$authenticatedUser->can('reject', [$leaf, $user])) {
            return response(['message' => 'permission denied'], 403);
        }

        $workflow = Leave::getWorkflow($leaf);
        if (!$workflow->can($leaf, Leave::TRANSITION_REJECT)) {
            return response(['message' => 'leave can not be rejected.'], 400);
        }

        Leave::getWorkflow($leaf)->apply($leaf, Leave::TRANSITION_REJECT);
        if (!$leaf->save()) {
            return response(['message' => 'server error please request later.'], 500);
        }

        return response(['message' => 'leave successfully rejected.'], 200);
    }
}
