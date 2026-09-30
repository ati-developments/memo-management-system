<?php

namespace App\Providers;

use App\Models\MemoApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer(['layouts.sidebar', 'layouts.workspace-header'], function ($view) {
            $user = Auth::user();

            $view->with([
                'user' => $user,
                'needsMyAction' => $user
                    ? MemoApproval::where('approver_id', $user->id)
                        ->where('action', 'pending')
                        ->whereHas('memo', fn ($query) => $query->where('status', 'pending'))
                        ->count()
                    : 0,
            ]);
        });
    }
}
