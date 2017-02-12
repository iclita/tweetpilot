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
            <div class="col-md-12">
                <h4 style="float:left;"><strong>Workers for {{ $campaign->website->url }}</strong></h4>
                <a style="float:right;" href="{{ route('workers.add', ['id'=>$campaign->id]) }}" class="btn btn-success btn-sm btn-round"><i class="fa fa-plus" aria-hidden="true"></i></a>
                @if ($workers->count() > 0)
                    <table class="table table-responsive table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Queue</th>
                            <th>Finished</th>
                            <th>Synced</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($workers as $worker)
                            <tr>
                                <td>{{ $worker->id }}</td>
                                <td>{{ $worker->getQueue() }}</td>
                                <td>{!! $worker->displayFinished() !!}</td>
                                <td><a href="{{ route('workers.toggle.synced', ['id'=>$worker->id]) }}">{!! $worker->displaySynced() !!}</a></td>
                                <td><button type="button" class="btn btn-danger btn-sm delete-resource btn-round" data-url="{{ route('workers.delete', ['id'=>$worker->id]) }}"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                @else
                    <h1 style="text-align:center;">You have no workers!</h1>
                @endif
            </div>
        </div>
    </div>
@stop