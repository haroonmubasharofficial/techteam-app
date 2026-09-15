<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q')->toString());
                $query->where(function ($q) use ($term) {
                    $q->where('action', 'like', "%{$term}%")
                        ->orWhere('auditable_type', 'like', "%{$term}%")
                        ->orWhere('route', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('audit_logs.index', [
            'logs' => $logs,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user:id,name,email');
        return view('audit_logs.show', compact('auditLog'));
    }
}
