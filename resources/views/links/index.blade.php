@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')

    <div class="container">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>{{ session()->get('message') }}</strong>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <h4 style="float:left;"><strong>Links</strong></h4>
                <a style="float:right;" href="{{ route('links.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Create</a>
                @if ($links->count() > 0)
                    <table class="table table-responsive table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>URL</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($links as $link)
                            <tr>
                                <td>{{ $link->id }}</td>
                                <td>{{ $link->description }}</td>
                                <td><a href="{{ $link->url }}" target="_blank">{{ $link->url }}</a></td>
                                <td><a class="btn btn-primary btn-sm"
                                       href="{{ route('links.edit', ['id' => $link->id]) }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
                                <td><button type="button" class="btn btn-danger btn-sm delete-resource" data-url="{{ route('links.destroy', ['id' => $link->id]) }}"><i class="fa fa-times" aria-hidden="true"></i> Delete</button></td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                @else
                    <h1 style="text-align:center;">You have no links!</h1>
                @endif
            </div>
        </div>

        {{ $links->links() }}

    </div>

@stop
