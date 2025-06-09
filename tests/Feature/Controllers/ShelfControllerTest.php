<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Shelf;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShelfControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new shelf
     */
    public function test_create_shelf(): void
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('/api/create_shelf', [
            'user_id' => $user->id,
            'name' => 'My Test Shelf'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'user_id',
                        'name',
                        'books'
                    ]
                ]);

        $this->assertDatabaseHas('shelves', [
            'user_id' => $user->id,
            'name' => 'My Test Shelf'
        ]);
    }

    /**
     * Test creating shelf with invalid data
     */
    public function test_create_shelf_validation_fails(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson("/api/create_shelf", [
            'name' => 'My Test Shelf' //not passing user_id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id']);

        $response = $this->postJson("/api/create_shelf", [
            'user_id' => $user->id, //not passing name
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test getting a specific shelf with books
     */
    public function test_get_shelf(): void
    {
        $user = User::factory()->create();
        $shelf = Shelf::factory()->create(['user_id' => $user->id]);
        $books = Book::factory()->count(3)->create();
        $shelf->books()->attach($books->pluck('id'));

        $response = $this->getJson("/api/get_shelf/{$shelf->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'user_id',
                        'name',
                        'books' => [
                            '*' => [
                                'id',
                                'title'
                            ]
                        ]
                    ]
                ]);
    }

    /**
     * Test getting non-existent shelf
     */
    public function test_get_shelf_not_found(): void
    {
        $response = $this->getJson('/api/get_shelf/999');

        $response->assertStatus(404);
    }

    /**
     * Test assigning books to shelf successfully
     */
    public function test_assign_books_to_shelf(): void
    {
        $user = User::factory()->create();
        $shelf = Shelf::factory()->create(['user_id' => $user->id]);
        $book = Book::factory()->create();

        $response = $this->postJson('/api/assign_books', [
            'shelf_id' => $shelf->id,
            'book_id' => $book->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'user_id',
                        'name',
                        'books'
                    ]
                ]);

        $this->assertDatabaseHas('book_shelf', [
            'shelf_id' => $shelf->id,
            'book_id' => $book->id
        ]);
    }

    /**
     * Test assigning books with invalid shelf_id
     */
    public function test_assign_books_invalid_shelf(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->postJson('/api/assign_books', [
            'shelf_id' => 999, // any number
            'book_id' => $book->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['shelf_id']);
    }

    /**
     * Test assigning books with invalid book_id
     */
    public function test_assign_books_invalid_book(): void
    {
        $user = User::factory()->create();
        $shelf = Shelf::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/assign_books', [
            'shelf_id' => $shelf->id,
            'book_id' => 999, // any number
            'user_id' => $user->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['book_id']);
    }

    /**
     * Test assigning books with invalid user_id
     */
    public function test_assign_books_invalid_user(): void
    {
        $user = User::factory()->create();
        $shelf = Shelf::factory()->create(['user_id' => $user->id]);
        $book = Book::factory()->create();

        $response = $this->postJson('/api/assign_books', [
            'shelf_id' => $shelf->id,
            'book_id' => $book->id,
            'user_id' => 999 // any number
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id']);
    }

    /**
     * Test assigning books when shelf doesn't belong to user
     */
    public function test_assign_books_shelf_not_belongs_to_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $shelf = Shelf::factory()->create(['user_id' => $user1->id]);
        $book = Book::factory()->create();

        $response = $this->postJson('/api/assign_books', [
            'shelf_id' => $shelf->id,
            'book_id' => $book->id,
            'user_id' => $user2->id // some other user trying to attach the book
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Shelf does not belong to user'
                ]);
    }

    /**
     * Test assigning books when book is already attached to shelf
     */
    public function test_assign_books_book_already_attached(): void
    {
        $user = User::factory()->create();
        $shelf1 = Shelf::factory()->create(['user_id' => $user->id]);
        $shelf2 = Shelf::factory()->create(['user_id' => $user->id]);
        $book = Book::factory()->create();

        // attach to the shelf first
        $shelf1->books()->attach($book->id);

        // try to attach same book to second shelf
        $response = $this->postJson('/api/assign_books', [
            'shelf_id' => $shelf2->id,
            'book_id' => $book->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Book is already attached to a shelf'
                ]);
    }
} 