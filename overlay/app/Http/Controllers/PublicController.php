<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Program;
use App\Models\Report;
use App\Models\User;

class PublicController extends Controller
{
    public function home()
    {
        return view('home', [
            'researchers' => User::where('role', 'researcher')->count(),
            'orgs' => Organization::where('status', 'approved')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'paid' => Report::where('reward_paid', true)->sum('reward_amount'),
        ]);
    }

    public function leaderboard()
    {
        $rows = User::where('role', 'researcher')->where('status', 'active')
            ->withCount(['reports as resolved_count' => fn ($q) => $q->where('status', 'resolved')])
            ->orderByDesc('reputation')->limit(50)->get();
        return view('public.leaderboard', compact('rows'));
    }

    public function hallOfFame()
    {
        $rows = Report::with(['researcher', 'program.organization'])
            ->where('status', 'resolved')->latest()->limit(100)->get();
        return view('public.hall', compact('rows'));
    }

    public function programs()
    {
        $programs = Program::with('organization')->where('active', true)
            ->whereHas('organization', fn ($q) => $q->where('status', 'approved'))->latest()->get();
        return view('public.programs', compact('programs'));
    }

    public function program(Program $program)
    {
        abort_unless($program->active && $program->organization->status === 'approved', 404);
        return view('public.program', compact('program'));
    }
}
