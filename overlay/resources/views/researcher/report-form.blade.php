@extends('layouts.app')
@section('title','Submit report')
@section('content')
<h2>Submit a report</h2><p class="muted">Program: {{ $program->title }} ({{ $program->organization->name }})</p>
<form method="post" enctype="multipart/form-data" action="{{ route('researcher.report.store',$program) }}" class="panel">@csrf
 <div class="row"><div class="col-md-8"><label class="form-label">Title</label><input class="form-control mb-3" name="title" value="{{ old('title') }}" required></div>
 <div class="col-md-4"><label class="form-label">Vulnerability type</label><input class="form-control mb-3" name="type" placeholder="XSS, SQL injection, IDOR..." value="{{ old('type') }}" required></div></div>
 <label class="form-label">Steps to reproduce</label><textarea class="form-control mb-3" name="steps" rows="6" required>{{ old('steps') }}</textarea>
 <label class="form-label">Impact</label><textarea class="form-control mb-3" name="impact" rows="3">{{ old('impact') }}</textarea>

 <h5 class="mt-3">CVSS v3.1 severity</h5>
 @php $m=[
  'av'=>['Attack vector',['N'=>'Network','A'=>'Adjacent','L'=>'Local','P'=>'Physical']],
  'ac'=>['Attack complexity',['L'=>'Low','H'=>'High']],
  'pr'=>['Privileges required',['N'=>'None','L'=>'Low','H'=>'High']],
  'ui'=>['User interaction',['N'=>'None','R'=>'Required']],
  's'=>['Scope',['U'=>'Unchanged','C'=>'Changed']],
  'c'=>['Confidentiality',['N'=>'None','L'=>'Low','H'=>'High']],
  'i'=>['Integrity',['N'=>'None','L'=>'Low','H'=>'High']],
  'a'=>['Availability',['N'=>'None','L'=>'Low','H'=>'High']]]; @endphp
 <div class="row g-3 mb-3">
 @foreach($m as $k=>[$label,$opts])
  <div class="col-6 col-md-3"><label class="form-label small">{{ $label }}</label>
   <select class="form-select cv" id="{{ $k }}" name="{{ $k }}">@foreach($opts as $v=>$t)<option value="{{ $v }}" @selected(old($k,['av'=>'N','ac'=>'L','pr'=>'N','ui'=>'N','s'=>'U','c'=>'N','i'=>'N','a'=>'N'][$k])===$v)>{{ $t }}</option>@endforeach</select></div>
 @endforeach
 </div>
 <div class="mb-3">Score: <strong id="score">0.0</strong> <span id="sev" class="sev sev-informative">Informative</span></div>

 <label class="form-label">Evidence (png, jpg, pdf, txt, mp4, zip; max 10 MB)</label><input class="form-control mb-3" type="file" name="evidence">
 <button class="btn btn-primary">Submit report</button>
</form>
@endsection
@push('scripts')
<script>
const W={AV:{N:.85,A:.62,L:.55,P:.2},AC:{L:.77,H:.44},UI:{N:.85,R:.62},CIA:{H:.56,L:.22,N:0}};
function up(x){const i=Math.round(x*100000);return i%10000===0?i/100000:(Math.floor(i/10000)+1)/10;}
function calc(){
 const g=id=>$('#'+id).val(), ch=g('s')==='C';
 const pr={N:.85,L:ch?.68:.62,H:ch?.5:.27}[g('pr')];
 const iss=1-(1-W.CIA[g('c')])*(1-W.CIA[g('i')])*(1-W.CIA[g('a')]);
 const imp=ch?7.52*(iss-.029)-3.25*Math.pow(iss-.02,15):6.42*iss;
 const ex=8.22*W.AV[g('av')]*W.AC[g('ac')]*pr*W.UI[g('ui')];
 const sc=imp<=0?0:up(Math.min((ch?1.08:1)*(imp+ex),10));
 const sev=sc===0?'informative':sc<4?'low':sc<7?'medium':sc<9?'high':'critical';
 $('#score').text(sc.toFixed(1));
 $('#sev').attr('class','sev sev-'+sev).text(sev.charAt(0).toUpperCase()+sev.slice(1));
}
$('.cv').on('change',calc);calc();
</script>
@endpush
