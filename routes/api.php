<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShelfController;

// User routes
Route::post('/create_user', [UserController::class, 'createUser'])->middleware('dummy');
Route::get('/get_user/{id}', [UserController::class, 'getUser'])->middleware('dummy');
Route::delete('/delete_user/{id}', [UserController::class, 'deleteUser'])->middleware('dummy');
Route::get('/get_users', [UserController::class, 'getUsers'])->middleware('dummy');

// Shelf routes
Route::post('/create_shelf', [ShelfController::class, 'createShelf'])->middleware('dummy');
Route::get('/get_shelf/{id}', [ShelfController::class, 'getShelf'])->middleware('dummy');
Route::post('/assign_books', [ShelfController::class, 'assignBooks'])->middleware('dummy');
Route::get('/get_shelves', [ShelfController::class, 'getShelves'])->middleware('dummy');