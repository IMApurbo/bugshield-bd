<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function show(Request $r, Report $report)
    {
        abort_unless($this->canView($report, $r->user()), 403);
        $report->load('program.organization', 'researcher', 'comments.user');
        $canManage = $this->canManage($report, $r->user());
        return view('reports.show', compact('report', 'canManage'));
    }

    public function comment(Request $r, Report $report)
    {
        abort_unless($this->canView($report, $r->user()), 403);
        $d = $r->validate(['body' => 'required|max:2000']);
        Comment::create(['report_id' => $report->id, 'user_id' => $r->user()->id, 'body' => $d['body']]);
        return back()->with('ok', 'Comment posted.');
    }

    /** Triage: organization (owner) or admin changes status, severity and reward. */
    public function update(Request $r, Report $report)
    {
        abort_unless($this->canManage($report, $r->user()), 403);

        $d = $r->validate([
            'status' => 'required|in:new,triaged,resolved,duplicate,informative',
            'severity' => 'nullable|in:critical,high,medium,low,informative',
            'reward_amount' => 'nullable|integer|min:0',
            'org_response' => 'nullable|max:2000',
        ]);

        DB::transaction(function () use ($report, $d) {
            $report->status = $d['status'];
            $report->org_response = $d['org_response'] ?? $report->org_response;
            if (!empty($d['severity'])) $report->severity = $d['severity'];

            if ($d['status'] === 'resolved' && !$report->reward_paid) {
                $amount = $d['reward_amount'] ?? $report->program->rewardFor($report->severity);
                $report->reward_amount = (int) $amount;
                $report->reward_paid = true;
                $report->researcher->increment('balance', $amount);
                $report->researcher->increment('reputation', config('bugshield.reputation')[$report->severity] ?? 1);
            }
            $report->save();
            AuditLog::record('report.updated', "Report #{$report->id} -> {$report->status}");
        });

        return back()->with('ok', 'Report updated.');
    }

    private function canManage(Report $report, $u): bool
    {
        if ($u->role === 'admin') return true;
        $org = $report->program->organization;
        return $u->role === 'organization' && $org->user_id === $u->id && $org->status === 'approved';
    }

    private function canView(Report $report, $u): bool
    {
        return $u->id === $report->user_id || $this->canManage($report, $u);
    }
}
