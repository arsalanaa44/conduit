<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\User\RegisterValidation;
use App\Http\Requests\User\LoginValidation;
use App\Http\Requests\User\UpdateValidation;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

use App\Services\UserService;


class UserController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }

    public function login(LoginValidation $request)
    {
        $credentials = $request->input('user', ['email', 'password']);
        $result = $this->userService->login(
            $credentials['email'],
            $credentials['password']
        );

        if (!$result) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $result['user'];
        $user->token = $result['token'];

        return new UserResource($user);
    }

    public function register(RegisterValidation $request)
    {

        $this->userService->create(
            $request->user['username'],
            $request->user['email'],
            $request->user['password']
        );

        $result = $this->userService->login(
            $request->user['email'],
            $request->user['password']
        );

        if (!$result) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $result['user'];
        $user->token = $result['token'];

        return new UserResource($user);}

    public function getCurrentUser(Request $request)
    {

        $user = Auth::user();

        $user->token = $request->bearerToken();
        return new UserResource($user);
    }
    public function updateCurrentUser(UpdateValidation $request)
    {
        $user = Auth::user();

        $validatedData = $request->validated();
        $user->fill($validatedData['user']);

        if (isset($validatedData['user']['password'])) {
            $user->password = bcrypt($validatedData['user']['password']);
        }

        $user->save();
        $user->token = $request->bearerToken();

        return new UserResource($user);
    }


}
