<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ImmeubleController;
use App\Http\Controllers\AppartementController;

Route::get("/", fn() => redirect("login"));

// Authentication Routes
Route::get("/register", [AuthController::class, "showRegistrationForm"])->name("register");
Route::post("/register", [AuthController::class, "register"]);
Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
Route::post("/login", [AuthController::class, "login"]);

Route::middleware(['auth','check.validated','must.change.password'])->group(function () {
    Route::get("/dashboard", [AuthController::class, "dashboard"])->name("dashboard");
});

Route::middleware(['auth'])->group(function () {
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
});

// Password Change Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [PasswordController::class, 'updatePassword'])->name('password.update');
});

// User Management Routes
Route::middleware(['auth'])->group(function () {

    // Liste des utilisateurs + recherche
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Formulaire de création
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    // Création d'un utilisateur
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Formulaire d'édition
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Mise à jour d'un utilisateur
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    // Supprimer un utilisateur
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Valider un utilisateur
    Route::patch('/users/{id}/validate', [UserController::class, 'validateUser'])->name('users.validate');

    // Désactiver un utilisateur
    Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
});

// Message Management Routes
Route::middleware(['auth'])->group(function () {

    // Afficher tous les messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    // Lire un message
    Route::get('/messages/read/{id}', [MessageController::class, 'read'])->name('messages.read');

    // Consulter un message
    Route::get('/messages/consult/{id}', [MessageController::class, 'consult'])->name('messages.consult');

    // Supprimer un message
    Route::delete('/messages/{id}', [MessageController::class, 'delete'])->name('messages.delete');
});

// Immeuble Management Routes
Route::middleware(['auth'])->group(function () {
    //afficher tous les immeubles
    Route::get('/immeubles', [ImmeubleController::class, 'index'])->name('immeubles.index');
    //creer un immeuble
    Route::get('/immeubles/add', [ImmeubleController::class,'create'])->name('immeubles.create');
    Route::post('/immeubles', [ImmeubleController::class,'store'])->name('immeubles.store');
    //modifier un immeuble
    Route::get('/immeubles/edit/{id}', [ImmeubleController::class,'edit'])->name('immeubles.edit');
    Route::put('/immeubles/{id}', [ImmeubleController::class,'update'])->name('immeubles.update');
    //supprimer un immeuble
    Route::delete('/immeubles/{id}', [ImmeubleController::class,'delete'])->name('immeubles.delete');
    //rechercher des immeubles
    Route::get('/immeubles/search', [ImmeubleController::class,'search'])->name('immeubles.search');
    //afficher un immeuble
    Route::get('/immeubles/{id}', [ImmeubleController::class,'show'])->name('immeubles.show');
});

// Appartement Management Routes
Route::middleware(['auth'])->group(function () {
    //index
    route::get('/appartements', [AppartementController::class,'index'])->name('appartements.index');
    //creer un appartement
    Route::get('/appartements/create', [AppartementController::class,'create'])->name('appartements.create');
    Route::post('/appartements', [AppartementController::class,'store'])->name('appartements.store');
    //modifier un appartement
    Route::get('/appartements/edit/{id}', [AppartementController::class,'edit'])->name('appartements.edit');
    Route::put('/appartements/{id}', [AppartementController::class,'update'])->name('appartements.update');
    //supprimer un appartement
    Route::delete('/appartements/{id}', [AppartementController::class,'destroy'])->name('appartements.destroy');
    //rechercher des appartements
    Route::get('/appartements/search', [AppartementController::class,'search'])->name('appartements.search');
    //consulter un appartement
    Route::get('/appartements/{id}', [AppartementController::class,'consult'])->name('appartements.consult');
});