<div class="table-responsive">
<table class="table align-middle">
 <thead><tr><th>#</th><th>Title</th><th>Program</th>@if($showResearcher ?? false)<th>Researcher</th>@endif<th>Severity</th><th>Status</th><th>Reward</th><th></th></tr></thead>
 <tbody>
 @forelse($reports as $r)
  <tr>
   <td>{{ $r->id }}</td>
   <td>{{ $r->title }} @if($r->duplicate_of)<span class="badge text-bg-warning">possible duplicate of #{{ $r->duplicate_of }}</span>@endif</td>
   <td>{{ $r->program->title }}</td>
   @if($showResearcher ?? false)<td>{{ $r->researcher->name }}</td>@endif
   <td><span class="sev sev-{{ $r->severity }}">{{ ucfirst($r->severity) }} {{ $r->cvss_score }}</span></td>
   <td><span class="st st-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
   <td>{{ $r->reward_paid ? 'BDT '.number_format($r->reward_amount) : '-' }}</td>
   <td><a class="btn btn-sm btn-outline-primary" href="{{ route('reports.show',$r) }}">Open</a></td>
  </tr>
 @empty
  <tr><td colspan="8" class="muted py-4">No reports yet.</td></tr>
 @endforelse
 </tbody>
</table>
</div>
