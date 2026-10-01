@extends('layouts.app')
@section('title','New program')
@section('content')
<h2 class="mb-3">New bug bounty program</h2>
<form method="post" action="{{ route('org.programs.store') }}" class="panel">@csrf
 <label class="form-label">Title</label><input class="form-control mb-3" name="title" value="{{ old('title') }}" required>
 <label class="form-label">Description</label><textarea class="form-control mb-3" name="description" rows="3" required>{{ old('description') }}</textarea>
 <label class="form-label">Scope (URLs, vulnerability types, exclusions)</label><textarea class="form-control mb-3" name="scope" rows="5" required>{{ old('scope') }}</textarea>
 <h5>Reward tiers (BDT)</h5>
 <div class="row g-3 mb-3">
 @foreach(['critical'=>50000,'high'=>20000,'medium'=>8000,'low'=>2000] as $s=>$def)
  <div class="col-6 col-md-3"><label class="form-label">{{ ucfirst($s) }}</label><input class="form-control" type="number" min="0" name="reward_{{ $s }}" value="{{ old('reward_'.$s,$def) }}" required></div>
 @endforeach
 </div>
 <button class="btn btn-primary">Publish program</button>
</form>
@endsection
