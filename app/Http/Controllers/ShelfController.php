<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\ShelfResource;

class ShelfController extends Controller
{
    public function createShelf(Request $request)
    {
        $userId = $request->input('user_id');
        $name = $request->input('name');

        if (!$userId || !$name) {
            return response()->json([
                'message' => 'User ID and name are required'
            ], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $shelf = Shelf::create([
            'user_id' => $userId,
            'name' => $name,
        ]);

        return response()->json([
            'message' => 'Shelf created successfully',
            'shelf' => new ShelfResource($shelf)
        ], 201);
    }

    public function getShelf(Request $request, $id)
    {
        $shelf = Shelf::with(['books', 'user'])->find($id);

        if (!$shelf) {
            return response()->json([
                'message' => 'Shelf not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Shelf found',
            'shelf' => new ShelfResource($shelf)
        ], 200);
    }

    public function assignBooks(Request $request)
    {
        $shelfId = $request->input('shelf_id');
        $bookId = $request->input('book_id');
        $userId = $request->input('user_id');

        if (!$shelfId || !$bookId || !$userId) {
            return response()->json([
                'message' => 'Shelf ID, book ID and user ID are required'
            ], 400);
        }

        $shelf = Shelf::where('id', $shelfId)
                     ->where('user_id', $userId)
                     ->first();

        if (!$shelf) {
            return response()->json([
                'message' => 'Shelf not found or does not belong to user'
            ], 404);
        }

        $book = Book::find($bookId);
        if (!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }

        if ($book->shelves()->count() > 0) {
            return response()->json([
                'message' => 'Book is already attached to a shelf'
            ], 400);
        }

        $shelf->books()->attach($bookId);
        $shelf->books = $shelf->books;

        return response()->json([
            'message' => 'Book added to shelf',
            'shelf' => new ShelfResource($shelf)
        ], 200);
    }

    public function getShelves(Request $request)
    {
        $shelves = Shelf::paginate(5);
        
        foreach ($shelves as $shelf) {
            $shelf->books = $shelf->books;
        }

        return ShelfResource::collection($shelves);
    }
}