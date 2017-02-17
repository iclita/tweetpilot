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
        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>{{ session()->get('warning') }}</strong>
            </div>
        @endif
        @unless ($hasVideos)
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>You have no videos. Campaigns will not work properly!</strong>
            </div>
        @endunless
        @unless ($hasLinks)
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>You have no links. Campaigns will not work properly!</strong>
            </div>
        @endunless
        <div class="row">
            <div class="col-md-12">
                <h4 style="float:left;"><strong>Campaigns</strong></h4>
                <a style="float:right;" href="{{ route('campaigns.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Create</a>
                @if ($campaigns->count() > 0)
                    <table class="table table-responsive table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>URL</th>
                            <th>Type</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Workers</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>Active</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($campaigns as $campaign)
                            <tr>
                                <td>{{ $campaign->id }}</td>
                                <td><a href="{{ $campaign->website->getFullUrl() }}" target="_blank">{{ $campaign->website->url }}</a></td>
                                <td>{!! $campaign->displayType() !!}</td>
                                <td>{!! $campaign->displayAction() !!}</td>
                                <td>{!! $campaign->displayStatus() !!}</td>
                                <td><a class="btn btn-info btn-sm" target="_blank" href="{{ route('workers.index', ['id'=>$campaign->id]) }}"><i class="fa fa-eye" aria-hidden="true"></i> Show</a></td>
                                <td><a class="btn btn-primary btn-sm"
                                       href="{{ route('campaigns.edit', ['id'=>$campaign->id]) }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
                                <td><button type="button" class="btn btn-danger btn-sm delete-resource" data-url="{{ route('campaigns.destroy', ['id'=>$campaign->id]) }}"><i class="fa fa-times" aria-hidden="true"></i> Delete</button></td>
                                <td><a href="{{ route('campaigns.toggle.active', ['id'=>$campaign->id]) }}">{!! $campaign->displayActive() !!}</a></td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                @else
                    <h1 style="text-align:center;">You have no campaigns!</h1>
                @endif
            </div>
        </div>
    </div>
@stop