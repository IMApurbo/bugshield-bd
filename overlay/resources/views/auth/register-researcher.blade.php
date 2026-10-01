@extends('layouts.app')
@section('title','Join as researcher')
@section('content')
<div class="row justify-content-center"><div class="col-md-6"><div class="panel">
 <h3 class="mb-3">Create a researcher account</h3>
 <form method="post" action="{{ route('register.researcher') }}">@csrf
  <label class="form-label">Full name</label><input class="form-control mb-3" name="name" value="{{ old('name') }}" required>
  <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required>
  <label class="form-label">Mobile number</label><input class="form-control mb-3" name="phone" placeholder="017XXXXXXXX" value="{{ old('phone') }}" required>
  <div class="row"><div class="col"><label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" required></div>
  <div class="col"><label class="form-label">Confirm password</label><input class="form-control mb-3" type="password" name="password_confirmation" required></div></div>
  <button class="btn btn-primary">Create account</button>
 </form>
</div></div></div>
@endsection
