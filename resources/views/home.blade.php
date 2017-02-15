@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
<div class="container">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>{{ session()->get('success') }}</strong>
        </div>
    @endif
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-database" aria-hidden="true"></i> Tokens</strong></div>
                <div class="panel-body">
                    <p>Total: {{ $total_tokens }}</p>
                    <p>Today: {{ $today_tokens }}</p>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-cog" aria-hidden="true"></i> Settings</strong></div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('change.settings') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                {{ Form::checkbox('is_auto', '1', settings('is_auto')) }} Check if you want auto
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="settings-publish-interval">Campaigns/day</label>
                            {{ Form::select('publish_interval', $publish_intervals, settings('publish_interval'), ['id'=>'settings-publish-interval', 'required']) }}
                        </div>
                        <div class="form-group">
                            <label for="settings-growth-percentage">Growth percentage</label>
                             {{ Form::select('growth_percentage', $growth_percentages, settings('growth_percentage'), ['id'=>'settings-growth-percentage', 'required']) }}
                        </div>
                        <div class="form-group">
                            <label for="num-workers">Number of workers</label>
                            <input type="number" name="num_workers" value="{{ settings('num_workers') }}" style="max-width:100px;" class="form-control" id="num-workers" required />
                        </div>
                        <button type="submit" style="float:right;margin-top:-51px;" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Change</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-wrench" aria-hidden="true"></i> Debug</strong></div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('debug.post') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="post-id">Post ID</label>
                            <input type="number" name="post_id" class="form-control" id="post-id" placeholder="Insert post ID here to check if still available" required />
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane" aria-hidden="true"></i> Check</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
