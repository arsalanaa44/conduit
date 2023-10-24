<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\User\RegisterValidation;
use App\Http\Requests\User\LoginValidation;
use App\Http\Requests\User\UpdateValidation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RegisterValidation $request)
    {
        return User::create([
            'username' => $request->user['username'],
            'email' => $request->user['email'],
            'password' => Hash::make($request->user['password']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function login(LoginValidation $request)
    {
        $credentials = $request->input('user', ['email', 'password']);


        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $user->token = $token;

        return new UserResource($user);
    }

    public function register(RegisterValidation $request)
    {
        $user = $this->create($request);

        $loginRequest = new LoginValidation();
        $loginRequest->merge([
            'user' => [
                'email' => $request->input('user.email'),
                'password' => $request->input('user.password')
            ]
        ]);

        return $this->login($loginRequest);
    }

    public function getCurrentUser(Request $request)
    {

        $user = Auth::user();

        $user->token = $request->bearerToken();
        return new UserResource($user);
    }

    public function updateCurrentUser(UpdateValidation $request)
    {
        $user = Auth::user();

        if (isset($request['user']['email'])) {
            $user->email = $request['user']['email'];
        }

        if (isset($request['user']['username'])) {
            $user->username = $request['user']['username'];
        }

        if (isset($request['user']['password'])) {
            $user->password = bcrypt($request['user']['password']);
        }

        if (isset($request['user']['bio'])) {
            $user->bio = $request['user']['bio'];
        }

        if (isset($request['user']['image'])) {
            $user->image = $request['user']['image'];
        }

        $user->save();
        $user->token = $request->bearerToken();

        return new UserResource($user);
    }

}
