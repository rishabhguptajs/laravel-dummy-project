<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function createUser(Request $request){
        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');    
        $phone = $request->input('phone');

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return response()->json([
                'message' => 'Invalid email'
            ], 400);
        }

        if(!$name){
            return response()->json([
                'message' => 'Name is required'
            ], 400);
        }

        $userExists = User::where('email', $email)->exists();
        if($userExists){
            return response()->json([
                'message' => 'User already exists',
            ], 409);
        }

        if(!$phone){
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ]);
        
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function getUser(Request $request){
        $id = $request->route('id');
        $user = User::where('id', $id)->first();

        if(!$user){
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $load_books = $request->input('load_books');
        $response = [
            'message' => 'User found',
            'user' => $user,
            'shelves' => $user->shelves
        ];

        if($load_books){
            // Load books through shelves
            $books = [];
            foreach($user->shelves as $shelf){
                foreach($shelf->books as $book){
                    $books[] = $book;
                }
            }
            $response['books'] = $books;
        }

        return response()->json($response, 200);
    }

    public function deleteUser(Request $request){
        $id = $request->route('id');
        $user = User::find($id);

        if(!$user){
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User soft deleted successfully'
        ], 200);
    }

    public function getUsers(Request $request){
        $users = User::paginate(3);

        return response()->json($users, 200);
    }
}
