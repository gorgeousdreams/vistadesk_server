@extends('layouts.default')
@section('content')
<div class="page-title">
</div>
<!-- BEGIN DASHBOARD TILES -->
<?  if (\Auth::check() && \Auth::user()->hasRole('Admin')) { ?>
        @include('partials.dashboard.revenue')
        <? } ?> 
<!-- END DASHBOARD TILES -->
@endsection