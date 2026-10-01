@extends('layouts.app')
@section('title','Leaderboard')
@section('content')
<h2 class="mb-3">Leaderboard</h2>
<div class="panel"><table class="table align-middle mb-0">
 <thead><tr><th>Rank</th><th>Researcher</th><th>Resolved reports</th><th>Reputation</th></tr></thead>
 <tbody>@forelse($rows as $i=>$u)<tr><td>{{ $i+1 }}</td><td>{{ $u->name }}</td><td>{{ $u->resolved_count }}</td><td><strong>{{ $u->reputation }}</strong></td></tr>
 @empty<tr><td colspan="4" class="muted">No researchers yet.</td></tr>@endforelse</tbody>
</table></div>
@endsection
