<?php

use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\FineController;
use App\Controllers\LoanController;
use App\Controllers\LogController;
use App\Controllers\ReservationController;
use App\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/books'));

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendRecoveryEmail'])->name('password.email');

    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Librarian + Admin — must be registered before the generic {id} catch-all
Route::middleware(['auth', 'role:librarian,admin', 'log.request'])->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{id}/renew', [LoanController::class, 'renew'])->name('loans.renew');
    Route::post('/loans/{id}/return', [LoanController::class, 'return'])->name('loans.return');

    Route::post('/fines/{id}/pay', [FineController::class, 'pay'])->name('fines.pay');
});

// All authenticated routes
Route::middleware(['auth', 'log.request'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}/cover', [BookController::class, 'cover'])->name('books.cover');
    Route::get('/books/{id}/pdf', [BookController::class, 'pdf'])->name('books.pdf');
    Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{id}', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::get('/fines', [FineController::class, 'index'])->name('fines.index');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin', 'log.request'])->group(function () {
    Route::resource('/users', UserController::class)->parameters(['users' => 'id']);
    Route::post('/users/{id}/block', [UserController::class, 'block'])->name('users.block');
    Route::post('/users/{id}/unblock', [UserController::class, 'unblock'])->name('users.unblock');

    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});
