@extends('layouts.app')
@section('title','Register organization')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><div class="panel">
 <h3 class="mb-3">Register your organization</h3>
 <form method="post" action="{{ route('register.org') }}">@csrf
  <h5 class="mt-1">Organization</h5>
  <label class="form-label">Organization name</label><input class="form-control mb-3" name="org_name" value="{{ old('org_name') }}" required>
  <label class="form-label">Website</label><input class="form-control mb-3" name="website" placeholder="https://example.com.bd" value="{{ old('website') }}" required>

  <h5 class="mt-4">Account owner</h5>
  <div class="row"><div class="col-md-6"><label class="form-label">Your name</label><input class="form-control mb-3" name="name" value="{{ old('name') }}" required></div>
  <div class="col-md-6"><label class="form-label">Mobile number</label><input class="form-control mb-3" name="phone" placeholder="017XXXXXXXX" value="{{ old('phone') }}" required></div></div>
  <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required>
  <div class="row"><div class="col"><label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" required></div>
  <div class="col"><label class="form-label">Confirm password</label><input class="form-control mb-3" type="password" name="password_confirmation" required></div></div>

  <h5 class="mt-4">Registration fee</h5>
  <div class="pay-box mb-3">
   <p class="mb-1">Send <strong>BDT {{ number_format(config('bugshield.org_fee')) }}</strong> by bKash ({{ config('bugshield.bkash_type') }} / Send Money) to:</p>
   <div class="fs-3 brand">{{ config('bugshield.bkash_number') }}</div>
   <div class="small muted">Then enter the number you paid from and the TrxID from your bKash confirmation SMS. An admin checks the payment before approving your account.</div>
  </div>
  <div class="row"><div class="col-md-6"><label class="form-label">bKash number you paid from</label><input class="form-control mb-3" name="sender_number" placeholder="017XXXXXXXX" value="{{ old('sender_number') }}" required></div>
  <div class="col-md-6"><label class="form-label">Transaction ID (TrxID)</label><input class="form-control mb-3 text-uppercase" name="trx_id" placeholder="8N7A6D5EFG" value="{{ old('trx_id') }}" required></div></div>
  <button class="btn btn-primary">Submit registration</button>
 </form>
</div></div></div>
@endsection
