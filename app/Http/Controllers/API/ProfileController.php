<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['getProfile']]);

    }
    public function getProfile($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        return new ProfileResource($user);
    }

    public function followUser($username)
    {
        $userToFollow = User::where('username', $username)->firstOrFail();
        $currentUser = auth()->user();

        $currentUser->following()->attach($userToFollow->id);

        return new ProfileResource($userToFollow);
    }

    public function unfollowUser($username)
    {
        $userToUnfollow = User::where('username', $username)->firstOrFail();
        $currentUser = auth()->user();

        $currentUser->following()->detach($userToUnfollow->id);

        return new ProfileResource($userToUnfollow);
    }



}
