@extends('layouts.app')
@section('title','Awaiting approval')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><div class="panel">
 @if($org->status==='rejected')
  <h3>Registration was not approved</h3>
  <p>Reason from the admin: <strong>{{ $org->admin_note }}</strong></p>
  <p class="muted">Contact the BugShield team with your TrxID <strong>{{ $org->trx_id }}</strong> to fix the issue.</p>
 @else
  <h3>Waiting for admin approval</h3>
  <p>We received your registration for <strong>{{ $org->name }}</strong>. An admin is verifying your bKash payment. You can publish programs once you are approved.</p>
 @endif
 <table class="table mb-0">
  <tr><th>Fee</th><td>BDT {{ number_format($org->fee_amount) }}</td></tr>
  <tr><th>Paid from</th><td>{{ $org->sender_number }}</td></tr>
  <tr><th>TrxID</th><td>{{ $org->trx_id }}</td></tr>
  <tr><th>Status</th><td>{{ ucfirst($org->status) }}</td></tr>
 </table>
</div></div></div>
@endsection
