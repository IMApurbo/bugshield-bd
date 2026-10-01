@extends('layouts.app')
@section('title','Users')
@section('content')
<h2 class="mb-3">Users</h2>
<div class="panel"><table class="table align-middle">
 <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Balance</th><th></th></tr></thead><tbody>
 @foreach($users as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ ucfirst($u->role) }}</td><td>{{ ucfirst($u->status) }}</td><td>BDT {{ number_format($u->balance,2) }}</td>
 <td><form method="post" action="{{ route('admin.users.toggle',$u) }}">@csrf<button class="btn btn-sm btn-outline-primary">{{ $u->status==='suspended'?'Reactivate':'Suspend' }}</button></form></td></tr>@endforeach
 </tbody></table></div>
@endsection
