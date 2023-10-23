<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\User\RegisterValidation;
use App\Http\Requests\User\LoginValidation;

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
    public function updateCurrentUser(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user

        $data = $request->validate([
            'user.email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'user.username' => 'sometimes|required|unique:users,username,' . $user->id,
            'user.password' => 'sometimes|required|min:8',
            'user.bio' => 'sometimes|string',
            'user.image' => 'sometimes|url'
        ]);

        if (isset($data['user']['email'])) {
            $user->email = $data['user']['email'];
        }

        if (isset($data['user']['username'])) {
            $user->username = $data['user']['username'];
        }

        if (isset($data['user']['password'])) {
            $user->password = bcrypt($data['user']['password']);
        }

        if (isset($data['user']['bio'])) {
            $user->bio = $data['user']['bio'];
        }

        if (isset($data['user']['image'])) {
            $user->image = $data['user']['image'];
        }

        $user->save();
        $user->token = $request->bearerToken();

        return new UserResource($user);
    }

}
