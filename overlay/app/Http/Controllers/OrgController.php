<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Program;
use App\Models\Report;
use Illuminate\Http\Request;

class OrgController extends Controller
{
    public function pending(Request $r)
    {
        $org = $r->user()->organization;
        if ($org && $org->status === 'approved') return redirect()->route('org.dashboard');
        return view('org.pending', compact('org'));
    }

    public function dashboard(Request $r)
    {
        $org = $r->user()->organization;
        $programs = $org->programs()->withCount('reports')->latest()->get();
        $reports = Report::with(['program', 'researcher'])
            ->whereIn('program_id', $org->programs()->pluck('id'))->latest()->limit(10)->get();
        return view('org.dashboard', compact('org', 'programs', 'reports'));
    }

    public function createProgram() { return view('org.program-form'); }

    public function storeProgram(Request $r)
    {
        $d = $r->validate([
            'title' => 'required|max:140', 'description' => 'required', 'scope' => 'required',
            'reward_critical' => 'required|integer|min:0', 'reward_high' => 'required|integer|min:0',
            'reward_medium' => 'required|integer|min:0', 'reward_low' => 'required|integer|min:0',
        ]);
        $p = $r->user()->organization->programs()->create($d);
        AuditLog::record('program.created', "Program #{$p->id}");
        return redirect()->route('org.dashboard')->with('ok', 'Program published. Researchers can now see it.');
    }

    public function reports(Request $r)
    {
        $org = $r->user()->organization;
        $reports = Report::with(['program', 'researcher'])
            ->whereIn('program_id', $org->programs()->pluck('id'))->latest()->get();
        return view('org.reports', compact('reports'));
    }
}
