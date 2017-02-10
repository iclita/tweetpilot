@extends('layouts.app')

@section('title')
    <title>{{ $website->name }}</title>
@stop

@section('content')
    <div class="container">
        @if (session()->has('message'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>{{ session()->get('message') }}</strong>
            </div>
        @endif
        <div class="row">
            @foreach ($videos as $video)
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">   
                <div class="thumbnail">
                    <a href="{{ route('video.show', $video->id) }}">
                        <img src="{{ $video->getImagePreview() }}" alt="{{ route('video.show', $video->id) }}" />
                        <div class="caption">
                            <img class="play" src="/img/play.png" />
                            <p><strong>{{ short($video->title, 60) }}</strong></p>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        {{ $videos->links() }}
    </div>
@stop
