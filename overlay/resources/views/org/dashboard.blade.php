@extends('layouts.app')
@section('title','Organization')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h2>{{ $org->name }}</h2>
 <a class="btn btn-primary" href="{{ route('org.programs.create') }}">New program</a></div>
<div class="panel mb-4"><h5>Programs</h5>
 <table class="table align-middle"><thead><tr><th>Title</th><th>Reports</th><th>Critical reward</th></tr></thead><tbody>
 @forelse($programs as $p)<tr><td><a href="{{ route('programs.show',$p) }}">{{ $p->title }}</a></td><td>{{ $p->reports_count }}</td><td>BDT {{ number_format($p->reward_critical) }}</td></tr>
 @empty<tr><td colspan="3" class="muted">No programs yet. Create one so researchers can start testing.</td></tr>@endforelse</tbody></table>
</div>
<div class="panel"><div class="d-flex justify-content-between"><h5>Latest reports</h5><a href="{{ route('org.reports') }}">All reports</a></div>
 @include('partials.report-table',['reports'=>$reports,'showResearcher'=>true])</div>
@endsection
