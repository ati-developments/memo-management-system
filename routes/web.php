<?php

use App\Http\Controllers\MemoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApprovalWorkflowController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
| These routes are available only before login.
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

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
        ->middleware('menu:dashboard')->name('dashboard');


    // Memo creation
    Route::get('/memos/create', [MemoController::class, 'create'])
        ->middleware('menu:new_memo')->name('memos.create');

    Route::get('/memos/templates', [MemoController::class, 'templates'])
        ->middleware('menu:new_memo')->name('memos.templates');

    Route::get('/memos/create/template/{template}', [MemoController::class, 'createFromTemplate'])
        ->middleware('menu:new_memo')->name('memos.create.template');

    Route::get('/memos/create/blank', function () {
        return "Blank memo creation";
    })->middleware('menu:new_memo')->name('memos.create.blank');

    Route::post('/memos', [MemoController::class, 'store'])
        ->middleware('menu:new_memo')->name('memos.store');


    // Templates

    Route::get('/templates', [TemplateController::class, 'index'])
        ->middleware('menu:templates')->name('templates.index');

    Route::get('/templates/create', [TemplateController::class, 'create'])
    ->middleware('menu:templates')->name('templates.create');

    Route::post('/templates', [TemplateController::class, 'store'])
    ->middleware('menu:templates')->name('templates.store');

    Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])
    ->middleware('menu:templates')->name('templates.edit');

    Route::post('/templates/{template}/fields', [TemplateController::class, 'storeFields'])
    ->middleware('menu:templates')->name('templates.fields.store');

    Route::post('/templates/{template}/tables', [TemplateController::class, 'storeTables'])
    ->middleware('menu:templates')->name('templates.tables.store');

    Route::get(
    '/templates/{template}/approval-workflow', [ApprovalWorkflowController::class, 'edit']
            )->middleware('menu:templates')->name('templates.approval-workflow.edit');

    Route::post('/templates/{template}/approval-workflow',[ApprovalWorkflowController::class, 'update']
            )->middleware('menu:templates')->name('templates.approval-workflow.update');

    Route::get('/memos/my',[MemoController::class, 'myMemos']
            )->middleware('menu:memos')->name('memos.my');

    Route::get('/memos/all', [MemoController::class, 'allMemos'])
        ->middleware('admin')->name('memos.all');

    Route::get('/approvals', [MemoController::class, 'approvals'])
        ->middleware('menu:approvals')->name('approvals.index');

    Route::get('/approvals/{approval}', [MemoController::class, 'reviewApproval'])
        ->middleware('menu:approvals')->name('approvals.review');

    Route::post('/approvals/{approval}/decision', [MemoController::class, 'recordApprovalDecision'])
        ->middleware('menu:approvals')->name('approvals.decision');

    Route::get('/memos/new', [MemoController::class, 'new'])
        ->middleware('menu:new_memo')->name('memos.new');

    Route::get('/memos/{memo}', [\App\Http\Controllers\MemoDocumentController::class, 'show'])->name('memos.show');
    Route::get('/memos/{memo}/pdf', [\App\Http\Controllers\MemoDocumentController::class, 'pdf'])->name('memos.pdf');
    Route::get('/memos/{memo}/attachments/{attachment}', [\App\Http\Controllers\MemoDocumentController::class, 'downloadAttachment'])->name('memos.attachments.download');
    Route::get('/memos/{memo}/edit', [\App\Http\Controllers\MemoDocumentController::class, 'edit'])->name('memos.edit');
    Route::put('/memos/{memo}', [\App\Http\Controllers\MemoDocumentController::class, 'update'])->name('memos.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::delete('/memos/{memo}', [\App\Http\Controllers\MemoDocumentController::class, 'destroy'])->name('memos.destroy');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/access-menu', [AdminController::class, 'accessMenu'])->name('access-menu');
    Route::put('/access-menu', [AdminController::class, 'updateAccessMenu'])->name('access-menu.update');
    Route::put('/access-menu/sidebar', [AdminController::class, 'updateSidebarMenu'])->name('access-menu.sidebar.update');
    Route::post('/access-menu/items', [AdminController::class, 'storeSidebarMenuItem'])->name('access-menu.items.store');
    Route::post('/access-menu/items/{item}/restore', [AdminController::class, 'restoreSidebarMenuItem'])->name('access-menu.items.restore');
    Route::put('/access-menu/role-access', [AdminController::class, 'updateRoleMenuAccess'])->name('access-menu.role-access.update');
    Route::get('/roles', [AdminController::class, 'roles'])->name('roles.index');
    Route::put('/templates/{template}', [AdminController::class, 'updateTemplate'])->name('templates.update');
    Route::delete('/templates/{template}', [AdminController::class, 'destroyTemplate'])->name('templates.destroy');
    Route::post('/roles', [AdminController::class, 'storeRole'])->name('roles.store');
    Route::put('/roles/{role}', [AdminController::class, 'updateRole'])->name('roles.update');
    Route::put('/memo-status-labels', [AdminController::class, 'updateMemoStatusLabels'])->name('memo-status-labels.update');
});
