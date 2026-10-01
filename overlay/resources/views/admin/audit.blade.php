@extends('layouts.app')
@section('title','Audit log')
@section('content')
<h2 class="mb-3">Audit log</h2>
<div class="panel"><table class="table table-sm align-middle">
 <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr></thead><tbody>
 @forelse($logs as $l)<tr><td>{{ $l->created_at->format('d M Y, H:i') }}</td><td>{{ $l->user->name ?? '-' }}</td><td>{{ $l->action }}</td><td>{{ $l->details }}</td><td>{{ $l->ip }}</td></tr>
 @empty<tr><td colspan="5" class="muted">No activity yet.</td></tr>@endforelse
 </tbody></table></div>
@endsection
