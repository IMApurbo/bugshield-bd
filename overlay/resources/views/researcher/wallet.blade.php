@extends('layouts.app')
@section('title','Wallet')
@section('content')
<h2 class="mb-3">Wallet</h2>
<div class="row g-4">
 <div class="col-lg-5"><div class="panel">
  <div class="muted">Available balance</div><div class="stat mb-3">BDT <span id="bal">{{ number_format($u->balance,2) }}</span></div>
  <h5>Withdraw to bKash</h5>
  <form id="wd" method="post" action="{{ route('researcher.withdraw') }}">@csrf
   <label class="form-label">bKash number</label><input class="form-control mb-3" name="bkash_number" placeholder="017XXXXXXXX" required>
   <label class="form-label">Amount (BDT)</label><input class="form-control mb-3" name="amount" type="number" min="{{ config('bugshield.min_withdrawal') }}" step="1" required>
   <button class="btn btn-primary w-100">Withdraw</button>
  </form>
  <div id="wdMsg" class="mt-3"></div>
 </div></div>
 <div class="col-lg-7"><div class="panel"><h5>Withdrawal history</h5>
  <table class="table align-middle" id="hist"><thead><tr><th>Reference</th><th>bKash number</th><th>Amount</th><th>Date</th></tr></thead><tbody>
  @foreach($items as $w)<tr><td>{{ $w->reference }}</td><td>{{ $w->bkash_number }}</td><td>BDT {{ number_format($w->amount,2) }}</td><td>{{ $w->created_at->format('d M Y, H:i') }}</td></tr>@endforeach
  </tbody></table>
 </div></div>
</div>
@endsection
@push('scripts')
<script>
$('#wd').on('submit',function(e){
 e.preventDefault();
 const box=$('#wdMsg').empty(), btn=$(this).find('button').prop('disabled',true);
 $.post($(this).attr('action'),$(this).serialize()).done(function(d){
   box.append($('<div class="alert alert-success mb-0">').text(d.message));
   $('#bal').text(d.balance);
   const r=d.row;
   $('#hist tbody').prepend($('<tr>').append($('<td>').text(r.reference),$('<td>').text(r.number),$('<td>').text('BDT '+r.amount),$('<td>').text(r.date)));
   $('#wd')[0].reset();
 }).fail(function(x){
   const er=x.responseJSON&&x.responseJSON.errors?Object.values(x.responseJSON.errors).flat():['Withdrawal failed. Try again.'];
   box.append($('<div class="alert alert-danger mb-0">').html(er.map(t=>$('<div>').text(t).html()).join('<br>')));
 }).always(()=>btn.prop('disabled',false));
});
</script>
@endpush
