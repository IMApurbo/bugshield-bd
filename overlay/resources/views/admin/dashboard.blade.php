@extends('layouts.app')
@section('title','Admin')
@section('content')
<h2 class="mb-3">Admin dashboard</h2>
@if($pendingOrgs)<div class="alert alert-warning d-flex justify-content-between align-items-center">
 <span><strong>{{ $pendingOrgs }}</strong> organization(s) are waiting for payment verification.</span><a class="btn btn-sm btn-primary" href="{{ route('admin.orgs') }}">Review now</a></div>@endif
<div class="row g-3 mb-4">
 @foreach([['Approved organizations',$orgs],['Researchers',$researchers],['Reports',$reports],['Org fees collected (BDT)',number_format($fees)],['Rewards credited (BDT)',number_format($rewards)],['Withdrawn (BDT)',number_format($withdrawn)]] as [$l,$v])
  <div class="col-6 col-md-4 col-lg-2"><div class="panel"><div class="stat">{{ $v }}</div><div class="muted small">{{ $l }}</div></div></div>
 @endforeach
</div>
<div class="row g-3">
 <div class="col-md-6"><div class="panel"><h5>Reports by severity</h5><canvas id="c1" height="160"></canvas></div></div>
 <div class="col-md-6"><div class="panel"><h5>Reports by status</h5><canvas id="c2" height="160"></canvas></div></div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
$.getJSON("{{ route('admin.stats') }}",function(d){
 const draw=(id,obj,colors)=>new Chart(document.getElementById(id),{type:'bar',
  data:{labels:Object.keys(obj),datasets:[{data:Object.values(obj),backgroundColor:colors}]},
  options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
 const sevColor={critical:'#d9363e',high:'#e0762b',medium:'#c99a12',low:'#3f8f6b',informative:'#6b7a73'};
 draw('c1',d.severity,Object.keys(d.severity).map(k=>sevColor[k]||'#6b7a73'));
 draw('c2',d.status,'#006a4e');
});
</script>
@endpush
