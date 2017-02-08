@extends('layouts.app')

@section('title')
    <title>{{ $website->name }}</title>
@stop

@section('content')

    <div class="container">

        <div class="row">
            @foreach ($videos as $video)
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">   
                <div class="thumbnail">
                <a href="{{ $video->getUrl() }}">
                    <img src="{{ $video->getImagePreview() }}" alt="{{ $video->getUrl() }}" />
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
