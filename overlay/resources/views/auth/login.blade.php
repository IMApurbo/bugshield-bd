@extends('layouts.app')
@section('title','Log in')
@section('content')
<div class="row justify-content-center"><div class="col-md-5"><div class="panel">
 <h3 class="mb-3">Log in</h3>
 <form method="post" action="{{ url('/login') }}">@csrf
  <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required autofocus>
  <label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" required>
  <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="rm"><label class="form-check-label" for="rm">Keep me logged in</label></div>
  <button class="btn btn-primary w-100">Log in</button>
 </form>
 <hr><div class="small muted">New here? <a href="{{ route('register.researcher') }}">Join as a researcher</a> or <a href="{{ route('register.org') }}">register an organization</a>.</div>
</div></div></div>
@endsection
