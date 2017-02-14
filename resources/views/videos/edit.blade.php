@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
    <div class="card-title ">
        <div class="container text-center">
            <h3>Edit Video</h3>
        </div>
    </div>
    <div class="container">
        @if (session()->has('danger'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>{{ session()->get('danger') }}</strong>
            </div>
        @endif
        <div class="card-panel">
            {!! Form::model($video, ['route' => ['videos.update', $video->id]]) !!}
                {{ method_field('PATCH') }}
                <div class="form-group">
                    <label for="video-title">Title (Description)</label>
                    {{ Form::text('title', null, ['class'=>'form-control', 'id'=>'video-title', 'placeholder'=>'Ex.: Check out this new awesome video...', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="video-slug">Slug (Video ID)</label>
                    {{ Form::text('slug', null, ['class'=>'form-control', 'id'=>'video-slug', 'placeholder'=>'https://www.youtube.com/watch?v=(qROhsr7Opqk)', 'required']) }}
                </div>
                <button style="float:right;" type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:left;" href="{{ route('videos.index') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>
@stop