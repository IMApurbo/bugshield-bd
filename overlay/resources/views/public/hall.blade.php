@extends('layouts.app')
@section('title','Hall of Fame')
@section('content')
<h2 class="mb-3">Hall of Fame</h2>
<div class="panel"><table class="table align-middle mb-0">
 <thead><tr><th>Researcher</th><th>Finding</th><th>Organization</th><th>Severity</th><th>Date</th></tr></thead>
 <tbody>@forelse($rows as $r)<tr><td>{{ $r->researcher->name }}</td><td>{{ $r->type }}</td><td>{{ $r->program->organization->name }}</td>
 <td><span class="sev sev-{{ $r->severity }}">{{ ucfirst($r->severity) }}</span></td><td>{{ $r->updated_at->format('d M Y') }}</td></tr>
 @empty<tr><td colspan="5" class="muted">Resolved findings will appear here.</td></tr>@endforelse</tbody>
</table></div>
@endsection
