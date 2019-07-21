<?php

namespace App\Http\Controllers\V1\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Models\Leave;
use App\Models\User;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\Leave as LeaveResource;

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

    /**
     * Tries to store a leave in database.
     *
     * @param LeaveRequest $request
     * @return LeaveResource
     * @throws AuthorizationException
     */
    public function store(LeaveRequest $request)
    {
        $this->authorize('create', Leave::class);

        $leave = new Leave($request->all());
        $leave->user_id = auth()->user()->id;

        if ($leave->save()) {
            return LeaveResource::make($leave);
        } else {
            return response('Bad request.', 400);
        }
    }

    /**
     * Shows leave of user.
     *
     * @param Leave $leaf
     * @return LeaveResource
     * @throws AuthorizationException
     */
    public function show(Leave $leaf)
    {
        $this->authorize('viewOwn', $leaf);

        return LeaveResource::make($leaf);
    }

    /**
     * Tries to cancel a leave.
     *
     * @param Leave $leaf
     * @return ResponseFactory|Response
     * @throws AuthorizationException
     */
    public function cancel(Leave $leaf)
    {
        $this->authorize('cancelOwn', $leaf);
        $workflow = Leave::getWorkflow($leaf);

        if ($workflow->can($leaf, Leave::TRANSITION_CANCEL)) {
            Leave::getWorkflow($leaf)->apply($leaf, Leave::TRANSITION_CANCEL);
            if ($leaf->save()) {
                return response(['message' => 'leave successfully canceled.'], 200);
            } else {
                return response(['message' => 'server error please request later.'], 500);
            }
        } else {
            return response(['message' => 'leave can not be canceled.'], 400);
        }
    }
}
