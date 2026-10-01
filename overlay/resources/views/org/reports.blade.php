@extends('layouts.app')
@section('title','Reports')
@section('content')
<h2 class="mb-3">Reports to your programs</h2>
<div class="panel">@include('partials.report-table',['reports'=>$reports,'showResearcher'=>true])</div>
@endsection
