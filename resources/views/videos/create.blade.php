@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')

    <div class="card-title">
        <div class="container text-center">
            <h3>Create Video</h3>
        </div>
    </div>
    <div class="container">
        <div class="card-panel">
            <form method="POST" action="{{ route('videos.store') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="video-title">Title</label>
                    <input type="text" name="title" class="form-control" id="video-title" placeholder="Ex.: Check out this new awesome video..." required />
                </div>

                <div class="form-group">
                    <label for="video-slug">Slug (Video ID)</label>
                    <input type="text" name="slug" class="form-control" id="video-slug" placeholder="https://www.youtube.com/watch?v=(qROhsr7Opqk)" required />
                </div>

                <button type="submit" class="btn btn-info"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:right;" href="{{ route('videos.index') }}" class="btn btn-warning"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>

@stop
