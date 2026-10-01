@extends('layouts.app')
@section('title','Programs')
@section('content')
<h2 class="mb-3">Bug bounty programs</h2>
<div class="row g-3">
@forelse($programs as $p)
 <div class="col-md-6"><div class="panel h-100">
  <h5><a href="{{ route('programs.show',$p) }}" class="text-decoration-none">{{ $p->title }}</a></h5>
  <div class="muted mb-2">{{ $p->organization->name }}</div>
  <p>{{ \Illuminate\Support\Str::limit($p->description,140) }}</p>
  <div class="small">Up to <strong>BDT {{ number_format($p->reward_critical) }}</strong> for critical findings</div>
 </div></div>
@empty
 <p class="muted">No programs are open yet. Approved organizations will publish them here.</p>
@endforelse
</div>
@endsection
