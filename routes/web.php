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
use App\Http\Controllers\MfaController;
use App\Http\Controllers\ContratDocumentController;
use App\Http\Controllers\ContratSignatureController;
use App\Http\Controllers\ReceiptSignatureController;
use App\Http\Controllers\DocumensoWebhookController;
use App\Http\Controllers\DashboardController;

Route::get("/", fn() => redirect("login"));

// Authentication Routes
Route::get("/register", [AuthController::class, "showRegistrationForm"])->name("register");
Route::post("/register", [AuthController::class, "register"])->middleware('throttle:register');
Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
Route::post("/login", [AuthController::class, "login"])->middleware('throttle:6,1');

// Webhook Documenso : appele par un service externe, pas de session/CSRF, verification par secret partage
Route::post('/webhooks/documenso', [DocumensoWebhookController::class, 'handle'])->name('webhooks.documenso');

// Logout : toujours accessible a un utilisateur connecte, meme si le compte
// n'est pas valide ou doit changer son mot de passe.
Route::middleware(['auth'])->group(function () {
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
});

// MFA : accessible une fois authentifie + compte valide, mais avant que la
// session soit marquee comme "mfa_verified" (evite la boucle de redirection
// puisque ces routes ne passent pas par le middleware 'mfa').
Route::middleware(['auth', 'check.validated'])->group(function () {
    Route::get('/mfa/challenge', [MfaController::class, 'showChallenge'])->name('mfa.challenge');
    Route::post('/mfa/verify', [MfaController::class, 'verify'])->name('mfa.verify')->middleware('throttle:10,1');
    Route::post('/mfa/resend', [MfaController::class, 'resend'])->name('mfa.resend')->middleware('throttle:3,1');
});

// Tout le reste de l'application authentifiee passe par ces 4 garde-fous :
// - auth : utilisateur connecte
// - check.validated : compte locataire valide par un admin/gestionnaire
// - mfa : code de verification par e-mail valide pour cette session
// - must.change.password : force le changement de mdp temporaire avant tout acces
Route::middleware(['auth', 'check.validated', 'mfa', 'must.change.password'])->group(function () {

    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");

    // Password Change Routes
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [PasswordController::class, 'updatePassword'])->name('password.update');

    // User Management Routes (reserve aux admins)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/validate', [UserController::class, 'validateUser'])->name('users.validate');
        Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    });
    // Profil : accessible a tout utilisateur connecte, le controleur verifie
    // que c'est son propre profil ou qu'il a un role de gestion.
    Route::get('/users/{id}', [UserController::class, 'consult'])->name('users.show');

    // Message Management Routes (tous roles, scoping fait au niveau service/controleur)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/read/{id}', [MessageController::class, 'read'])->name('messages.read');
    Route::get('/messages/consult/{id}', [MessageController::class, 'consult'])->name('messages.consult');
    Route::delete('/messages/{id}', [MessageController::class, 'delete'])->name('messages.delete');
    Route::get('/messages/request', [MessageController::class, 'create'])->name('messages.request.create');
    Route::post('/messages/request/send', [MessageController::class, 'store'])->name('messages.request.store');

    // Immeuble Management Routes (admin + gestionnaire uniquement)
    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::get('/immeubles', [ImmeubleController::class, 'index'])->name('immeubles.index');
        Route::get('/immeubles/add', [ImmeubleController::class, 'create'])->name('immeubles.create');
        Route::post('/immeubles', [ImmeubleController::class, 'store'])->name('immeubles.store');
        Route::get('/immeubles/edit/{id}', [ImmeubleController::class, 'edit'])->name('immeubles.edit');
        Route::put('/immeubles/{id}', [ImmeubleController::class, 'update'])->name('immeubles.update');
        Route::delete('/immeubles/{id}', [ImmeubleController::class, 'delete'])->name('immeubles.delete');
        Route::get('/immeubles/search', [ImmeubleController::class, 'search'])->name('immeubles.search');
        Route::get('/immeubles/{id}', [ImmeubleController::class, 'show'])->name('immeubles.show');
    });

    // Appartement Management Routes (admin + gestionnaire uniquement)
    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::get('/appartements', [AppartementController::class, 'index'])->name('appartements.index');
        Route::get('/appartements/create', [AppartementController::class, 'create'])->name('appartements.create');
        Route::post('/appartements', [AppartementController::class, 'store'])->name('appartements.store');
        Route::get('/appartements/edit/{id}', [AppartementController::class, 'edit'])->name('appartements.edit');
        Route::put('/appartements/{id}', [AppartementController::class, 'update'])->name('appartements.update');
        Route::delete('/appartements/{id}', [AppartementController::class, 'destroy'])->name('appartements.destroy');
        Route::get('/appartements/search', [AppartementController::class, 'search'])->name('appartements.search');
        Route::get('/appartements/{id}', [AppartementController::class, 'consult'])->name('appartements.consult');
    });

    // Manager routes (gestion des locataires par un admin/gestionnaire)
    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::get('/manager/locataires/index', [ManagerController::class, 'index'])->name('manager.index');
        Route::get('/manager/locataires/create', [ManagerController::class, 'create'])->name('manager.create');
        Route::post('/manager/locataires', [ManagerController::class, 'store'])->name('manager.store');
        Route::get('/manager/locataires/edit/{id}', [ManagerController::class, 'edit'])->name('manager.edit');
        Route::put('/manager/locataires/{id}', [ManagerController::class, 'update'])->name('manager.update');
        Route::delete('/manager/locataires/{id}', [ManagerController::class, 'destroy'])->name('manager.delete');
        Route::patch('/manager/locataires/{id}/activate', [ManagerController::class, 'activate'])->name('manager.activate');
        Route::patch('/manager/locataires/{id}/deactivate', [ManagerController::class, 'deactivate'])->name('manager.deactivate');
        Route::get('/manager/locataires/search', [ManagerController::class, 'search'])->name('manager.search');
    });

    // Contrat Management routes : la gestion est reservee a admin/gestionnaire,
    // la consultation d'un contrat reste ouverte (le controleur verifie que le
    // locataire ne consulte que son propre contrat).
    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::get('/contrats/create', [ContratController::class, 'create'])->name('contrats.create');
        Route::post('/contrats', [ContratController::class, 'store'])->name('contrats.store');
        Route::get('/contrats/edit/{id}', [ContratController::class, 'edit'])->name('contrats.edit');
        Route::put('/contrats/{id}', [ContratController::class, 'update'])->name('contrats.update');
        Route::delete('/contrats/{id}', [ContratController::class, 'destroy'])->name('contrats.destroy');
        Route::post('/contrats/terminate/{id}', [ContratController::class, 'terminate'])->name('contrats.terminate');
        Route::get('/contrats/search', [ContratController::class, 'search'])->name('contrats.search');
        Route::get('contrats/renew/{id}', [ContratController::class, 'renewForm'])->name('contrats.renewForm');
        Route::post('contrats/renew/{id}', [ContratController::class, 'renew'])->name('contrats.renew');
    });
    // contrats.index est deja scope par role dans ContratService::all() (un locataire ne recoit que son propre contrat)
    Route::get('/contrats', [ContratController::class, 'index'])->name('contrats.index');

    // Espace CNI (liste par locataire/contrat) : chemin litteral, doit rester AVANT /contrats/{id}
    Route::get('/contrats/documents', [ContratDocumentController::class, 'index'])
        ->name('contrats.documents')->middleware('role:admin,gestionnaire');

    Route::get('/contrats/{id}', [ContratController::class, 'consult'])->name('contrats.consult');

    // Piece jointe (CNI) : demande/validation reservees a admin+gestionnaire, upload reserve au locataire proprietaire
    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::post('/contrats/{contrat}/document/request', [ContratDocumentController::class, 'request'])->name('contrats.document.request');
        Route::post('/contrats/{contrat}/document/{document}/validate', [ContratDocumentController::class, 'validateDoc'])->name('contrats.document.validate');
        Route::post('/contrats/{contrat}/document/{document}/reject', [ContratDocumentController::class, 'reject'])->name('contrats.document.reject');
    });
    Route::post('/contrats/{contrat}/document', [ContratDocumentController::class, 'store'])->name('contrats.document.store');
    Route::get('/contrats/{contrat}/document/{document}/download', [ContratDocumentController::class, 'download'])->name('contrats.document.download');

    // Signature electronique du contrat (Documenso) : envoi reserve a admin/gestionnaire, telechargement ouvert au proprietaire
    Route::post('/contrats/{contrat}/signature/send', [ContratSignatureController::class, 'send'])
        ->name('contrats.signature.send')->middleware('role:admin,gestionnaire');
    Route::get('/contrats/{contrat}/pdf', [ContratSignatureController::class, 'download'])->name('contrats.pdf');

    // Locataire routes
    Route::get('/tenant/logement', [AppartementController::class, 'locataire'])->name('tenant.logement');

    // Payment et receipt routes : creation/edition/suppression reservees a
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/receipts', [PaymentController::class, 'indexReceipts'])->name('receipts.index');
    Route::post('/payments/send', [PaymentController::class, 'sendPaymentRequest'])->name('payments.sendRequest');
    
    // Signature electronique du recu (Documenso) : reservee a admin/gestionnaire (le locataire ne signe pas)
    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::post('/receipts/{receipt}/signature/send', [ReceiptSignatureController::class, 'send'])->name('receipts.signature.send');
    });
    Route::get('/receipts/{receipt}/pdf', [ReceiptSignatureController::class, 'download'])->name('receipts.pdf');

    Route::middleware('role:admin,gestionnaire')->group(function () {
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/edit/{id}', [PaymentController::class, 'editPayment'])->name('payments.edit');
        Route::put('/payments/{id}', [PaymentController::class, 'updatePayment'])->name('payments.update');
        Route::delete('/payments/{id}', [PaymentController::class, 'destroyPayment'])->name('payments.destroy');
        Route::get('/payments/search', [PaymentController::class, 'searchPayment'])->name('payments.search');

        Route::get('/receipts/periods/{id}', [PaymentController::class, 'formGenerateReceipt'])->name('receipts.periods');
        Route::post('/receipts/generate/{id}', [PaymentController::class, 'generateReceipt'])->name('receipts.generate');
        Route::get('/receipts/edit/{id}', [PaymentController::class, 'editReceipt'])->name('receipts.edit');
        Route::put('/receipts/{id}', [PaymentController::class, 'updateReceipt'])->name('receipts.update');
        Route::delete('/receipts/{id}', [PaymentController::class, 'destroyReceipt'])->name('receipts.destroy');
        Route::get('/receipts/search', [PaymentController::class, 'searchReceipts'])->name('receipts.search');
    });

    Route::get('/payments/{id}', [PaymentController::class, 'showPayment'])->name('payments.show');
    Route::get('/payments/receipt/{id}', [PaymentController::class, 'showReceipt'])->name('receipts.show');

});
