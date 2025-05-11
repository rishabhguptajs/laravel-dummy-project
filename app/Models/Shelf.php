<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    protected $table = 'shelf';
    protected $fillable = ['name', 'user_id'];

    /**
     * Get the user that owns the shelf.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all books associated with this shelf.
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'shelf_book', 'shelf_id', 'book_id')->withTimestamps();
    }
}
