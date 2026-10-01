@extends('layouts.app')
@section('title',$program->title)
@section('content')
<div class="row g-4">
 <div class="col-lg-8"><div class="panel">
  <h2>{{ $program->title }}</h2><div class="muted mb-3">{{ $program->organization->name }} &middot; {{ $program->organization->website }}</div>
  <h5>About</h5><p style="white-space:pre-line">{{ $program->description }}</p>
  <h5>Scope</h5><p style="white-space:pre-line">{{ $program->scope }}</p>
 </div></div>
 <div class="col-lg-4"><div class="panel">
  <h5>Rewards</h5>
  <table class="table mb-3">
   @foreach(['critical','high','medium','low'] as $s)<tr><td><span class="sev sev-{{ $s }}">{{ ucfirst($s) }}</span></td><td class="text-end">BDT {{ number_format($program->{'reward_'.$s}) }}</td></tr>@endforeach
  </table>
  @auth
   @if(auth()->user()->role==='researcher')<a class="btn btn-primary w-100" href="{{ route('researcher.report.create',$program) }}">Submit a report</a>@endif
  @else
   <a class="btn btn-primary w-100" href="{{ route('login') }}">Log in to submit a report</a>
  @endauth
 </div></div>
</div>
@endsection
