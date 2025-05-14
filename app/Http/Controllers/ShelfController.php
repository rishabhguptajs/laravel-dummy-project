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
        $request->validate([
            'user_id' => ['required'],
            'name' => ['required']
        ]);

        $shelf = Shelf::create($request->all());

        return new ShelfResource($shelf);
    }

    public function getShelf(Request $request, Shelf $shelf)
    {
        $shelf->load('books');
        return new ShelfResource($shelf);
    }

    public function assignBooks(Request $request)
    {
        $validated = $request->validate([
            'shelf_id' => 'required|exists:shelf,id',
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $shelf = Shelf::findOrFail($validated['shelf_id']);
        if ($shelf->user_id !== $validated['user_id']) {
            abort(404, 'Shelf does not belong to user');
        }

        $book = Book::findOrFail($validated['book_id']);
        if ($book->shelves()->count() > 0) {
            abort(400, 'Book is already attached to a shelf');
        }

        $shelf->books()->attach($book->id);
        $shelf->load('books');

        return new ShelfResource($shelf);
    }

    public function getShelves(Request $request)
    {
        $shelves = Shelf::with('books')->paginate(5);

        return ShelfResource::collection($shelves);
    }
}