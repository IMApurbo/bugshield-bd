@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<h2 class="mb-3">Hello, {{ $u->name }}</h2>
<div class="row g-3 mb-4">
 <div class="col-6 col-md-3"><div class="panel"><div class="stat">BDT {{ number_format($u->balance,2) }}</div><div class="muted">Wallet balance</div></div></div>
 <div class="col-6 col-md-3"><div class="panel"><div class="stat">{{ $u->reputation }}</div><div class="muted">Reputation</div></div></div>
 <div class="col-6 col-md-3"><div class="panel"><div class="stat">{{ $counts->sum() }}</div><div class="muted">Reports submitted</div></div></div>
 <div class="col-6 col-md-3"><div class="panel"><div class="stat">{{ $counts['resolved'] ?? 0 }}</div><div class="muted">Resolved</div></div></div>
</div>
<div class="d-flex gap-2 mb-3"><a class="btn btn-primary" href="{{ route('programs') }}">Find a program</a><a class="btn btn-outline-primary" href="{{ route('researcher.wallet') }}">Withdraw to bKash</a></div>
<div class="panel"><h5>Recent reports</h5>@include('partials.report-table',['reports'=>$reports])</div>
@endsection
