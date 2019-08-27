<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmqAuthController extends Controller
{
    /**
     * Checks that a token is valid or not.
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function validateToken(Request $request)
    {
        $username = $request->get('username');
        $token= $request->get('token');

        if ($this->isSuperuser($username, $token)) {
            return response([], 200);
        }

        try {
            JWTAuth::setToken($token);
            if (! $claim = JWTAuth::getPayload()) {
                return response(["error" => "token is not valid!"], 401);
            }
            return response([], 200);

        } catch (\Exception $exception) {
            return response(["error" => "token is not valid!"], 401);
        }
    }

    /**
     * Checks that acl for user
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function topicsAcl(Request $request)
    {
        $topic= $request->get('topic');
        $name= $request->get('name');
        $user = app(UserRepositoryInterface::class)->findByName($name);

        if (!$user instanceof User) {
            return response(["user not found."], 401);
        }

        if (array_search($topic, $user->getTopics()) !== false) {
            return response([], 200);
        }

        return response(["user has not access"], 401);
    }

    /**
     * Checks that user is super user or not.
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function superuser(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        if ($this->isSuperuser($username, $password)) {
            return response([], 200);
        }

        return response(["user is not super man"], 401);
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    private function isSuperuser($username, $password)
    {
        if ($username == env("EMQ_SUPER_USER") && $password == env("EMQ_SUPER_PASS")) {
            return true;
        } else {
            return false;
        }
    }
}
