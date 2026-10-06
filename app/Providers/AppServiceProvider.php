<?php

namespace App\Providers;

use App\Models\MemoApproval;
use App\Models\MemoStatusLabel;
use App\Models\SidebarMenuItem;
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

        View::composer('layouts.app', function ($view) {
            $labels = ['draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved'];
            try {
                $labels = array_merge($labels, MemoStatusLabel::pluck('label', 'status_key')->all());
            } catch (\Throwable $exception) {
                // Keep defaults available before the status-label migration has run.
            }
            $view->with('memoStatusLabels', $labels);
        });

        View::composer(['layouts.sidebar', 'layouts.workspace-header'], function ($view) {
            $user = Auth::user();
            $sidebarMenuItems = SidebarMenuItem::with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get();

            $view->with([
                'user' => $user,
                'isAdmin' => in_array(strtolower((string) $user?->role?->role_name), ['admin', 'administrator'], true),
                'menuAccess' => $user?->role?->menu_access ?? $user?->menu_access ?? ['dashboard', 'templates', 'new_memo', 'memos', 'approvals'],
                'sidebarMenuItems' => $sidebarMenuItems,
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
