<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/** Flags a new report that looks like an earlier report in the same program. */
class DetectDuplicateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $reportId) {}

    public function handle(): void
    {
        $report = Report::find($this->reportId);
        if (!$report) return;

        $mine = strtolower($report->title . ' ' . $report->type);
        $others = Report::where('program_id', $report->program_id)->where('id', '<', $report->id)->get();

        foreach ($others as $other) {
            similar_text($mine, strtolower($other->title . ' ' . $other->type), $pct);
            if ($pct >= 85) {
                $report->update(['duplicate_of' => $other->id]);
                break;
            }
        }
    }
}
