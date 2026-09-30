<?php

use App\Http\Controllers\MemoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApprovalWorkflowController;


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
| These routes are available only before login.
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| These routes require the user to be logged in.
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/signature', [\App\Http\Controllers\ProfileController::class, 'signature'])->name('profile.signature');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    // Memo creation
    Route::get('/memos/create', [MemoController::class, 'create'])
        ->name('memos.create');

    Route::get('/memos/templates', [MemoController::class, 'templates'])
        ->name('memos.templates');

    Route::get('/memos/create/template/{template}', [MemoController::class, 'createFromTemplate'])
        ->name('memos.create.template');

    Route::get('/memos/create/blank', function () {
        return "Blank memo creation";
    })->name('memos.create.blank');

    Route::post('/memos', [MemoController::class, 'store'])
        ->name('memos.store');


    // Templates
    Route::post('/templates/{template}/insert', [TemplateController::class, 'insert'])
        ->name('templates.insert');

    Route::get('/templates', [TemplateController::class, 'index'])
        ->name('templates.index');

    Route::get('/templates/create', [TemplateController::class, 'create'])
    ->name('templates.create');

    Route::post('/templates', [TemplateController::class, 'store'])
    ->name('templates.store');

    Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])
    ->name('templates.edit');

    Route::post('/templates/{template}/fields', [TemplateController::class, 'storeFields'])
    ->name('templates.fields.store');

    Route::post('/templates/{template}/tables', [TemplateController::class, 'storeTables'])
    ->name('templates.tables.store');

    Route::get(
    '/templates/{template}/approval-workflow', [ApprovalWorkflowController::class, 'edit']
            )->name('templates.approval-workflow.edit');

    Route::post('/templates/{template}/approval-workflow',[ApprovalWorkflowController::class, 'update']
            )->name('templates.approval-workflow.update');

    Route::get('/memos/my',[MemoController::class, 'myMemos']
            )->name('memos.my');

    Route::get('/memos/all', [MemoController::class, 'allMemos'])
        ->name('memos.all');

    Route::get('/approvals', [MemoController::class, 'approvals'])
        ->name('approvals.index');

    Route::get('/approvals/{approval}', [MemoController::class, 'reviewApproval'])
        ->name('approvals.review');

    Route::post('/approvals/{approval}/decision', [MemoController::class, 'recordApprovalDecision'])
        ->name('approvals.decision');

    Route::get('/memos/new', [MemoController::class, 'new'])
        ->name('memos.new');

    Route::get('/memos/{memo}', [\App\Http\Controllers\MemoDocumentController::class, 'show'])->name('memos.show');
    Route::get('/memos/{memo}/pdf', [\App\Http\Controllers\MemoDocumentController::class, 'pdf'])->name('memos.pdf');
    Route::get('/memos/{memo}/edit', [\App\Http\Controllers\MemoDocumentController::class, 'edit'])->name('memos.edit');
    Route::put('/memos/{memo}', [\App\Http\Controllers\MemoDocumentController::class, 'update'])->name('memos.update');
});
