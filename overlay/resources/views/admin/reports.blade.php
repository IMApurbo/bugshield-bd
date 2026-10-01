@extends('layouts.app')
@section('title','All reports')
@section('content')
<h2 class="mb-3">All reports</h2>
<div class="panel">@include('partials.report-table',['reports'=>$reports,'showResearcher'=>true])</div>
@endsection
