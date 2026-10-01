@extends('layouts.app')
@section('title','Report #'.$report->id)
@section('content')
<div class="row g-4">
 <div class="col-lg-8">
  <div class="panel mb-3">
   <div class="d-flex justify-content-between flex-wrap gap-2"><h3>{{ $report->title }}</h3>
    <div><span class="sev sev-{{ $report->severity }}">{{ ucfirst($report->severity) }} {{ $report->cvss_score }}</span> <span class="st st-{{ $report->status }} ms-2">{{ ucfirst($report->status) }}</span></div></div>
   <div class="muted mb-3">{{ $report->program->title }} &middot; by {{ $report->researcher->name }} &middot; {{ $report->created_at->format('d M Y') }}</div>
   @if($report->duplicate_of)<div class="alert alert-warning">This looks similar to report #{{ $report->duplicate_of }}.</div>@endif
   <h6>Type</h6><p>{{ $report->type }}</p>
   <h6>Steps to reproduce</h6><p style="white-space:pre-line">{{ $report->steps }}</p>
   @if($report->impact)<h6>Impact</h6><p style="white-space:pre-line">{{ $report->impact }}</p>@endif
   <h6>CVSS vector</h6><p class="small" style="font-family:ui-monospace,monospace">{{ $report->cvss_vector }}</p>
   @if($report->evidence_path)<h6>Evidence</h6><p><a href="{{ asset('storage/'.$report->evidence_path) }}" target="_blank" rel="noopener">Open attachment</a></p>@endif
   @if($report->org_response)<h6>Organization response</h6><p style="white-space:pre-line">{{ $report->org_response }}</p>@endif
  </div>

  <div class="panel"><h5>Discussion</h5>
   @forelse($report->comments as $c)
    <div class="border-bottom py-2"><strong>{{ $c->user->name }}</strong> <span class="small muted">{{ ucfirst($c->user->role) }} &middot; {{ $c->created_at->diffForHumans() }}</span><div style="white-space:pre-line">{{ $c->body }}</div></div>
   @empty<p class="muted">No comments yet.</p>@endforelse
   <form method="post" action="{{ route('reports.comment',$report) }}" class="mt-3">@csrf
    <textarea class="form-control mb-2" name="body" rows="3" required></textarea><button class="btn btn-primary btn-sm">Post comment</button></form>
  </div>
 </div>

 <div class="col-lg-4">
  @if($canManage)
  <div class="panel"><h5>Triage</h5>
   <form method="post" action="{{ route('reports.update',$report) }}">@csrf @method('PATCH')
    <label class="form-label">Status</label>
    <select class="form-select mb-3" name="status">@foreach(['new','triaged','resolved','duplicate','informative'] as $s)<option value="{{ $s }}" @selected($report->status===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
    <label class="form-label">Severity</label>
    <select class="form-select mb-3" name="severity">@foreach(['critical','high','medium','low','informative'] as $s)<option value="{{ $s }}" @selected($report->severity===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
    @unless($report->reward_paid)
    <label class="form-label">Reward (BDT, optional)</label>
    <input class="form-control mb-1" type="number" min="0" name="reward_amount" placeholder="Program tier by default">
    <div class="form-text mb-3">Paid to the researcher's wallet when you set the status to Resolved.</div>
    @else<p class="small muted">Reward of BDT {{ number_format($report->reward_amount) }} already paid.</p>@endunless
    <label class="form-label">Response to researcher</label><textarea class="form-control mb-3" name="org_response" rows="3">{{ $report->org_response }}</textarea>
    <button class="btn btn-primary w-100">Save changes</button>
   </form>
  </div>
  @else
  <div class="panel"><h5>Reward</h5><div class="stat">{{ $report->reward_paid ? 'BDT '.number_format($report->reward_amount) : 'Not paid yet' }}</div></div>
  @endif
 </div>
</div>
@endsection
