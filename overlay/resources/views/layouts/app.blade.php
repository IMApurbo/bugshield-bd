<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'BugShield BD')</title>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@500;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--ink:#10231c;--forest:#006a4e;--forest-d:#004d38;--paper:#f5f6f2;--line:#d9ded6;--red:#d9363e;
 --bs-body-font-family:'Source Sans 3',system-ui,sans-serif;--bs-body-bg:var(--paper);--bs-body-color:var(--ink);--bs-primary:var(--forest)}
body{background:var(--paper)}
h1,h2,h3,h4,h5,.brand{font-family:'Bricolage Grotesque',system-ui,sans-serif;letter-spacing:-.01em}
.topbar{background:var(--ink)}
.topbar .nav-link,.topbar .navbar-brand{color:#dfe8e2}
.topbar .nav-link:hover{color:#fff}
.brand{font-weight:700;font-size:1.25rem;color:#fff!important}
.brand i{display:inline-block;width:.8rem;height:.8rem;border-radius:50%;background:var(--red);margin-right:.45rem;box-shadow:0 0 0 .22rem var(--forest)}
.btn-primary{--bs-btn-bg:var(--forest);--bs-btn-border-color:var(--forest);--bs-btn-hover-bg:var(--forest-d);--bs-btn-hover-border-color:var(--forest-d);--bs-btn-active-bg:var(--forest-d)}
.btn-outline-primary{--bs-btn-color:var(--forest);--bs-btn-border-color:var(--forest);--bs-btn-hover-bg:var(--forest);--bs-btn-hover-border-color:var(--forest)}
a{color:var(--forest)}
.panel{background:#fff;border:1px solid var(--line);border-radius:6px;padding:1.4rem}
.table{--bs-table-bg:transparent}
.table th{font-weight:600;color:#4a5b53;border-bottom-color:var(--line)}
.sev{display:inline-block;padding:.12rem .55rem;border-radius:3px;font-size:.82rem;font-weight:600;color:#fff;background:#6b7a73}
.sev-critical{background:var(--red)}.sev-high{background:#e0762b}.sev-medium{background:#c99a12;color:#2b2200}
.sev-low{background:#3f8f6b}.sev-informative{background:#6b7a73}
.st{font-weight:600}
.st-new{color:#1d5fa8}.st-triaged{color:#8a5a00}.st-resolved{color:var(--forest)}.st-duplicate,.st-informative{color:#6b7a73}
.stat{font-family:'Bricolage Grotesque';font-size:1.9rem;line-height:1.1}
.muted{color:#5d6d65}
.form-control:focus,.form-select:focus{border-color:var(--forest);box-shadow:0 0 0 .2rem rgba(0,106,78,.18)}
.hero{padding:4.5rem 0 3.5rem}
.hero h1{font-size:clamp(2.2rem,5vw,3.6rem);line-height:1.05;font-weight:700;max-width:14ch}
.advisory{background:var(--ink);color:#e7efe9;border-radius:6px;padding:1.5rem;border-left:6px solid var(--red)}
.advisory .vec{font-family:ui-monospace,Menlo,monospace;font-size:.82rem;color:#9fd8bf;word-break:break-all}
.pay-box{background:#fff8e6;border:1px solid #ecd9a0;border-radius:6px;padding:1rem 1.2rem}
footer{border-top:1px solid var(--line);color:#5d6d65}
@media (prefers-reduced-motion:no-preference){.btn{transition:background-color .15s,border-color .15s}}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg topbar">
 <div class="container">
  <a class="navbar-brand brand" href="{{ route('home') }}"><i></i>BugShield BD</a>
  <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon" style="filter:invert(1)"></span></button>
  <div class="collapse navbar-collapse" id="nav">
   <ul class="navbar-nav me-auto">
    <li class="nav-item"><a class="nav-link" href="{{ route('programs') }}">Programs</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('leaderboard') }}">Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('hall') }}">Hall of Fame</a></li>
   </ul>
   <ul class="navbar-nav align-items-lg-center gap-lg-2">
   @auth
    @if(auth()->user()->role==='researcher')
     <li class="nav-item"><a class="nav-link" href="{{ route('researcher.dashboard') }}">Dashboard</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('researcher.reports') }}">My reports</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('researcher.wallet') }}">Wallet</a></li>
    @elseif(auth()->user()->role==='organization')
     <li class="nav-item"><a class="nav-link" href="{{ route('org.dashboard') }}">My organization</a></li>
    @else
     <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('admin.orgs') }}">Organizations</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Users</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('admin.withdrawals') }}">Withdrawals</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports') }}">Reports</a></li>
     <li class="nav-item"><a class="nav-link" href="{{ route('admin.audit') }}">Audit log</a></li>
    @endif
    <li class="nav-item"><form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-light">Log out</button></form></li>
   @else
    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log in</a></li>
    <li class="nav-item"><a class="btn btn-sm btn-light" href="{{ route('register.researcher') }}">Join as researcher</a></li>
   @endauth
   </ul>
  </div>
 </div>
</nav>

<main class="container py-4">
 @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
 @yield('content')
</main>

<footer class="py-4 mt-5"><div class="container small">BugShield BD, Bangladesh's bug bounty platform. TMSS Engineering College, Bogura, CSE 2026.</div></footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name=csrf-token]').attr('content')}});</script>
@stack('scripts')
</body>
</html>
