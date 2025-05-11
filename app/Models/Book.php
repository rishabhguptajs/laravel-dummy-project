<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all shelves this book belongs to.
     */
    public function shelves()
    {
        return $this->belongsToMany(Shelf::class, 'shelf_book', 'book_id', 'shelf_id')->withTimestamps();
    }
}
