@extends('layouts.app')
@section('title','Organizations')
@section('content')
<h2 class="mb-3">Organizations</h2>
<ul class="nav nav-pills mb-3">@foreach(['pending','approved','rejected','all'] as $s)
 <li class="nav-item"><a class="nav-link {{ $status===$s?'active':'' }}" style="{{ $status===$s?'background:var(--forest)':'' }}" href="{{ route('admin.orgs',['status'=>$s]) }}">{{ ucfirst($s) }}</a></li>@endforeach</ul>
<div class="alert alert-light border small">Before approving, open your bKash app and confirm that the TrxID exists, the amount is BDT {{ number_format(config('bugshield.org_fee')) }}, and the sender number matches.</div>
<div class="panel"><div class="table-responsive"><table class="table align-middle">
 <thead><tr><th>Organization</th><th>Owner</th><th>Fee</th><th>Paid from</th><th>TrxID</th><th>Status</th><th>Action</th></tr></thead><tbody>
 @forelse($orgs as $o)
 <tr>
  <td><strong>{{ $o->name }}</strong><br><a class="small" href="{{ $o->website }}" target="_blank" rel="noopener">{{ $o->website }}</a></td>
  <td>{{ $o->user->name }}<br><span class="small muted">{{ $o->user->email }}<br>{{ $o->user->phone }}</span></td>
  <td>BDT {{ number_format($o->fee_amount) }}</td><td>{{ $o->sender_number }}</td><td><code>{{ $o->trx_id }}</code></td>
  <td>{{ ucfirst($o->status) }}@if($o->admin_note)<br><span class="small muted">{{ $o->admin_note }}</span>@endif</td>
  <td style="min-width:210px">
   @if($o->status!=='approved')
    <form method="post" action="{{ route('admin.orgs.approve',$o) }}" class="mb-2">@csrf<button class="btn btn-sm btn-primary w-100">Approve</button></form>
   @endif
   @if($o->status==='pending')
    <form method="post" action="{{ route('admin.orgs.reject',$o) }}">@csrf
     <input class="form-control form-control-sm mb-1" name="admin_note" placeholder="Reason for rejecting" required>
     <button class="btn btn-sm btn-outline-danger w-100">Reject</button></form>
   @endif
  </td>
 </tr>
 @empty<tr><td colspan="7" class="muted py-4">Nothing here.</td></tr>@endforelse
 </tbody></table></div></div>
@endsection
