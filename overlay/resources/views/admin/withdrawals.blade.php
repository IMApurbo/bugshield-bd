@extends('layouts.app')
@section('title','Withdrawals')
@section('content')
<h2 class="mb-3">Withdrawals</h2>
<div class="panel"><table class="table align-middle">
 <thead><tr><th>Reference</th><th>Researcher</th><th>bKash number</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>
 @forelse($items as $w)<tr><td>{{ $w->reference }}</td><td>{{ $w->user->name }}</td><td>{{ $w->bkash_number }}</td><td>BDT {{ number_format($w->amount,2) }}</td><td>{{ ucfirst($w->status) }}</td><td>{{ $w->created_at->format('d M Y, H:i') }}</td></tr>
 @empty<tr><td colspan="6" class="muted">No withdrawals yet.</td></tr>@endforelse
 </tbody></table></div>
@endsection
