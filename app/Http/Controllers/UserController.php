<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function createUser(Request $request){
        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'name' => ['required', 'string'],
            'password' => ['required', 'string', Password::default()],
            'phone' => ['required', 'numeric'],
        ]);

        $user = User::create($request->all());

        return new UserResource($user);
    }

    public function getUser(Request $request, User $user)
    {
        $user->load(['shelves.books']);

        return new UserResource($user);
    }

    public function deleteUser(Request $request, User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User soft deleted successfully'
        ]);
    }

    public function getUsers(Request $request){
        $users = User::paginate(3);

        return UserResource::collection($users);
    }
}
