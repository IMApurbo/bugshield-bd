<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'pendingOrgs' => Organization::where('status', 'pending')->count(),
            'orgs' => Organization::where('status', 'approved')->count(),
            'researchers' => User::where('role', 'researcher')->count(),
            'reports' => Report::count(),
            'fees' => Organization::where('status', 'approved')->sum('fee_amount'),
            'rewards' => Report::where('reward_paid', true)->sum('reward_amount'),
            'withdrawn' => Withdrawal::sum('amount'),
        ]);
    }

    /** JSON for the dashboard charts (loaded with AJAX). */
    public function stats()
    {
        $sev = Report::selectRaw('severity, count(*) c')->groupBy('severity')->pluck('c', 'severity');
        $st = Report::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');
        return response()->json(['severity' => $sev, 'status' => $st]);
    }

    public function orgs(Request $r)
    {
        $status = $r->query('status', 'pending');
        $orgs = Organization::with('user')->when($status !== 'all', fn ($q) => $q->where('status', $status))->latest()->get();
        return view('admin.orgs', compact('orgs', 'status'));
    }

    public function approveOrg(Organization $org)
    {
        $org->update(['status' => 'approved', 'approved_at' => now(), 'admin_note' => null]);
        $org->user->update(['status' => 'active']);
        AuditLog::record('org.approved', "Org #{$org->id} {$org->name}, TrxID {$org->trx_id}");
        return back()->with('ok', "{$org->name} is approved and can now publish programs.");
    }

    public function rejectOrg(Request $r, Organization $org)
    {
        $d = $r->validate(['admin_note' => 'required|max:500']);
        $org->update(['status' => 'rejected', 'admin_note' => $d['admin_note']]);
        AuditLog::record('org.rejected', "Org #{$org->id}: {$d['admin_note']}");
        return back()->with('ok', "{$org->name} was rejected.");
    }

    public function users()
    {
        return view('admin.users', ['users' => User::where('role', '!=', 'admin')->latest()->get()]);
    }

    public function toggleUser(User $user)
    {
        abort_if($user->role === 'admin', 403);
        $user->update(['status' => $user->status === 'suspended' ? 'active' : 'suspended']);
        AuditLog::record('user.' . $user->status, "User #{$user->id}");
        return back()->with('ok', "{$user->name} is now {$user->status}.");
    }

    public function withdrawals()
    {
        return view('admin.withdrawals', ['items' => Withdrawal::with('user')->latest()->get()]);
    }

    public function reports()
    {
        return view('admin.reports', ['reports' => Report::with(['program.organization', 'researcher'])->latest()->get()]);
    }

    public function audit()
    {
        return view('admin.audit', ['logs' => AuditLog::with('user')->latest()->limit(200)->get()]);
    }
}
