@extends('layouts.app')
@section('title','My reports')
@section('content')
<h2 class="mb-3">My reports</h2>
<div class="panel">@include('partials.report-table',['reports'=>$reports])</div>
@endsection
