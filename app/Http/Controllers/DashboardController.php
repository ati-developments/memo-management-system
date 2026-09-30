<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\MemoApproval;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Temporary fallback while authentication is being developed
        if (!$user) {
            $user = \App\Models\User::first();
        }

        $myMemos = Memo::where('created_by', $user?->id)
            ->count();

        $pendingApproval = Memo::where('created_by', $user?->id)
            ->where('status', 'pending')
            ->count();

        $approved = Memo::where('created_by', $user?->id)
            ->where('status', 'approved')
            ->count();

        $needsMyAction = MemoApproval::where('approver_id', $user?->id)
            ->where('action', 'pending')
            ->count();

        $recentMemos = Memo::with([
                'creator',
                'template',
                'department'
            ])
            ->where('created_by', $user?->id)
            ->latest()
            ->take(5)
            ->get();

        $approvalQueue = MemoApproval::with([
                'memo',
                'approver'
            ])
            ->where('approver_id', $user?->id)
            ->where('action', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'myMemos',
            'pendingApproval',
            'approved',
            'needsMyAction',
            'recentMemos',
            'approvalQueue'
        ));
    }
}