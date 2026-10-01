@extends('layouts.app')
@section('content')
<section class="hero">
 <div class="row align-items-center g-5">
  <div class="col-lg-7">
   <h1>Find the bug before someone else does.</h1>
   <p class="fs-5 muted mt-3" style="max-width:52ch">BugShield BD connects Bangladeshi security researchers with organizations that want their systems tested. Report a vulnerability, get triaged, and get paid through bKash.</p>
   <div class="d-flex flex-wrap gap-2 mt-4">
    <a class="btn btn-primary btn-lg" href="{{ route('register.researcher') }}">Start hunting</a>
    <a class="btn btn-outline-primary btn-lg" href="{{ route('register.org') }}">Open a program</a>
   </div>
  </div>
  <div class="col-lg-5">
   <div class="advisory">
    <div class="d-flex justify-content-between mb-2"><strong>Reflected XSS in search page</strong><span class="sev sev-medium">Medium 6.1</span></div>
    <div class="vec mb-3">CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N</div>
    <div class="d-flex justify-content-between"><span>Status: resolved</span><span>Bounty: BDT 8,000</span></div>
   </div>
  </div>
 </div>
</section>

<section class="row g-3 mb-5">
 @foreach([['Researchers',$researchers],['Approved organizations',$orgs],['Resolved reports',$resolved],['Paid in bounties (BDT)',number_format($paid)]] as [$l,$v])
  <div class="col-6 col-md-3"><div class="panel"><div class="stat">{{ $v }}</div><div class="muted">{{ $l }}</div></div></div>
 @endforeach
</section>

<section class="row g-4">
 <div class="col-md-6"><div class="panel h-100"><h4>For researchers</h4>
  <p class="muted">Register free, pick a program, submit a report with a CVSS v3.1 score and evidence. When it is resolved, the bounty lands in your wallet and you withdraw it to bKash.</p></div></div>
 <div class="col-md-6"><div class="panel h-100"><h4>For organizations</h4>
  <p class="muted">Registration costs BDT {{ number_format(config('bugshield.org_fee')) }}, paid once by bKash. Send the fee, enter the TrxID on the form, and an admin approves your account after checking the payment.</p></div></div>
</section>
@endsection
