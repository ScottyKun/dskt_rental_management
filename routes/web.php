<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ImmeubleController;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\PaymentController;

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

    //profil ou show
    Route::get('/users/{id}', [UserController::class,'consult'])->name('users.show');

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

    //demande locataire
    Route::get('/messages/request', [MessageController::class, 'create'])->name('messages.request.create');
    Route::post('/messages/request/send', [MessageController::class, 'store']) ->name('messages.request.store');
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

// Manager routes
Route::middleware(['auth'])->group(function () { 
    //index
    Route::get('/manager/locataires/index', [ManagerController::class,'index'])->name('manager.index');
    //creer locataire
    Route::get('/manager/locataires/create', [ManagerController::class,'create'])->name('manager.create');
    Route::post('/manager/locataires', [ManagerController::class,'store'])->name('manager.store');
    //modifier locataire
    Route::get('/manager/locataires/edit/{id}', [ManagerController::class,'edit'])->name('manager.edit');
    Route::put('/manager/locataires/{id}', [ManagerController::class,'update'])->name('manager.update');
    //supprimer locataire
    Route::delete('/manager/locataires/{id}', [ManagerController::class,'destroy'])->name('manager.delete');
    //activer locataire
    Route::patch('/manager/locataires/{id}/activate', [ManagerController::class, 'activate'])->name('manager.activate');
    //desactiver locataire
    Route::patch('/manager/locataires/{id}/deactivate', [ManagerController::class, 'deactivate'])->name('manager.deactivate');
    //rechercher locataire
    Route::get('/manager/locataires/search', [ManagerController::class, 'search'])->name('manager.search');
});

//Contrat Management routes
Route::middleware(['auth'])->group(function () {
    //index
    Route::get('/contrats', [ContratController::class, 'index']) ->name('contrats.index');
    //create
    Route::get('/contrats/create', [ContratController::class, 'create'])->name('contrats.create');
    Route::post('/contrats', [ContratController::class, 'store'])->name('contrats.store');
    //update
    Route::get('/contrats/edit/{id}', [ContratController::class, 'edit'])->name('contrats.edit');
    Route::put('/contrats/{id}', [ContratController::class, 'update']) ->name('contrats.update');
    //delete
    Route::delete('/contrats/{id}', [ContratController::class, 'destroy'])->name('contrats.destroy');
    //terminate
    Route::post('/contrats/terminate/{id}', [ContratController::class, 'terminate'])->name('contrats.terminate');
    //search
    Route::get('/contrats/search', [ContratController::class, 'search'])->name('contrats.search');
    //renew
    Route::get('contrats/renew/{id}', [ContratController::class, 'renewForm'])->name('contrats.renewForm');
    Route::post('contrats/renew/{id}', [ContratController::class, 'renew'])->name('contrats.renew');
    //consult
    Route::get('/contrats/{id}', [ContratController::class, 'consult'])->name('contrats.consult');

});

//locataires routes
Route::middleware(['auth'])->group(function () {
    //mon logement
    Route::get('/tenant/logement', [AppartementController::class,'locataire'])->name('tenant.logement');

});

//payment et receipt routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/payments', [PaymentController::class,'index'])->name('payments.index');
    
    //payments
    Route::get('/payments/create', [PaymentController::class,'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class,'store'])->name('payments.store');
    Route::get('/payments/edit/{id}', [PaymentController::class,'editPayment'])->name('payments.edit');
    Route::put('/payments/{id}', [PaymentController::class,'updatePayment'])->name('payments.update');
    Route::delete('/payments/{id}', [PaymentController::class,'destroyPayment'])->name('payments.destroy');
    Route::get('/payments/search', [PaymentController::class,'searchPayment'])->name('payments.search');
    Route::post('/payments/send', [PaymentController::class,'sendPaymentRequest'])->name('payments.sendRequest');
    Route::get('/payments/{id}', [PaymentController::class,'showPayment'])->name('payments.show');
    
    //receipts
    Route::get('/receipts/periods/{id}', [PaymentController::class,'formGenerateReceipt'])->name('receipts.periods');
    Route::post('/receipts/generate/{id}', [PaymentController::class,'generateReceipt'])->name('receipts.generate');
    Route::get('/receipts/edit/{id}', [PaymentController::class,'editReceipt'])->name('receipts.edit');
    Route::put('/receipts/{id}', [PaymentController::class,'updateReceipt'])->name('receipts.update');
    Route::delete('/receipts/{id}', [PaymentController::class,'destroyReceipt'])->name('receipts.destroy');
    Route::get('/receipts/search', [PaymentController::class,'searchReceipts'])->name('receipts.search');
    Route::get('/payments/receipt/{id}', [PaymentController::class,'showReceipt'])->name('receipts.show');

});