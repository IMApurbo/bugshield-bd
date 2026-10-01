<?php

namespace App\Http\Controllers;

use App\Jobs\DetectDuplicateReport;
use App\Models\AuditLog;
use App\Models\Program;
use App\Models\Report;
use App\Models\Withdrawal;
use App\Services\CvssService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResearcherController extends Controller
{
    public function dashboard(Request $r)
    {
        $u = $r->user();
        $reports = $u->reports()->with('program')->latest()->limit(8)->get();
        $counts = $u->reports()->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');
        return view('researcher.dashboard', compact('u', 'reports', 'counts'));
    }

    public function reports(Request $r)
    {
        $reports = $r->user()->reports()->with('program')->latest()->get();
        return view('researcher.reports', compact('reports'));
    }

    public function createReport(Program $program)
    {
        abort_unless($program->active && $program->organization->status === 'approved', 404);
        return view('researcher.report-form', compact('program'));
    }

    public function storeReport(Request $r, Program $program)
    {
        abort_unless($program->active && $program->organization->status === 'approved', 404);

        $d = $r->validate([
            'title' => 'required|max:160',
            'type' => 'required|max:80',
            'steps' => 'required|min:20',
            'impact' => 'nullable|max:2000',
            'av' => 'required|in:N,A,L,P', 'ac' => 'required|in:L,H', 'pr' => 'required|in:N,L,H',
            'ui' => 'required|in:N,R', 's' => 'required|in:U,C',
            'c' => 'required|in:H,L,N', 'i' => 'required|in:H,L,N', 'a' => 'required|in:H,L,N',
            'evidence' => 'nullable|file|max:10240|mimes:png,jpg,jpeg,pdf,txt,mp4,zip',
        ]);

        [$vector, $score, $severity] = CvssService::compute($d);

        $report = Report::create([
            'program_id' => $program->id, 'user_id' => $r->user()->id,
            'title' => $d['title'], 'type' => $d['type'], 'steps' => $d['steps'], 'impact' => $d['impact'] ?? null,
            'cvss_vector' => $vector, 'cvss_score' => $score, 'severity' => $severity,
            'evidence_path' => $r->file('evidence') ? $r->file('evidence')->store('evidence', 'public') : null,
        ]);

        DetectDuplicateReport::dispatch($report->id);
        AuditLog::record('report.submitted', "Report #{$report->id} to program #{$program->id}");

        return redirect()->route('reports.show', $report)->with('ok', 'Report submitted. The organization will triage it soon.');
    }

    public function wallet(Request $r)
    {
        return view('researcher.wallet', ['u' => $r->user(), 'items' => $r->user()->withdrawals()->limit(20)->get()]);
    }

    /** Simple bKash withdrawal: user gives number + amount, balance is debited, success is shown. */
    public function withdraw(Request $r)
    {
        $min = config('bugshield.min_withdrawal');
        $d = $r->validate([
            'bkash_number' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'amount' => "required|numeric|min:$min",
        ], [
            'bkash_number.regex' => 'Enter a valid bKash number, like 017XXXXXXXX.',
            'amount.min' => "The minimum withdrawal is BDT $min.",
        ]);

        $result = DB::transaction(function () use ($r, $d) {
            $user = $r->user()->newQuery()->lockForUpdate()->find($r->user()->id);
            if ($d['amount'] > $user->balance) return null;

            $user->decrement('balance', $d['amount']);
            $w = Withdrawal::create([
                'user_id' => $user->id, 'bkash_number' => $d['bkash_number'],
                'amount' => $d['amount'], 'status' => 'successful',
                'reference' => 'WD' . strtoupper(Str::random(8)),
            ]);
            AuditLog::record('withdrawal', "BDT {$d['amount']} to {$d['bkash_number']} ({$w->reference})");
            return [$w, $user->fresh()->balance];
        });

        if (!$result) {
            $msg = 'You cannot withdraw more than your balance.';
            return $r->expectsJson() ? response()->json(['errors' => ['amount' => [$msg]]], 422)
                                     : back()->withErrors(['amount' => $msg]);
        }

        [$w, $balance] = $result;
        $message = 'Withdrawal successful. BDT ' . number_format($w->amount, 2) . " sent to bKash {$w->bkash_number} (ref {$w->reference}).";

        if ($r->expectsJson()) {
            return response()->json([
                'message' => $message, 'balance' => number_format($balance, 2),
                'row' => ['reference' => $w->reference, 'number' => $w->bkash_number,
                          'amount' => number_format($w->amount, 2), 'date' => $w->created_at->format('d M Y, H:i')],
            ]);
        }
        return back()->with('ok', $message);
    }
}
