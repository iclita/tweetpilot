@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
    <div class="card-title ">
        <div class="container text-center">
            <h3>Edit Website</h3>
        </div>
    </div>
    <div class="container">
        <div class="card-panel">
            {!! Form::model($website, ['route' => ['websites.update', $website->id]]) !!}
                {{ method_field('PATCH') }}
                <div class="form-group">
                    <label for="website-name">Name</label>
                    {{ Form::text('name', null, ['class'=>'form-control', 'id'=>'website-name', 'placeholder'=>'Ex.: My Website', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="website-description">Description</label>
                    {{ Form::text('description', null, ['class'=>'form-control', 'id'=>'website-description', 'placeholder'=>'Ex.: My Website is awesome!', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="website-url">URL</label>
                    {{ Form::text('url', null, ['class'=>'form-control', 'id'=>'website-url', 'placeholder'=>'Ex.: mywebsite.com', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="website-app-key">App Key</label>
                    {{ Form::text('app_key', null, ['class'=>'form-control', 'id'=>'website-app-key', 'placeholder'=>'Website app key', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="website-app-secret">App Secret</label>
                    {{ Form::text('app_secret', null, ['class'=>'form-control', 'id'=>'website-app-secret', 'placeholder'=>'Website app secret', 'required']) }}
                </div>
                <button style="float:right;" type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:left;" href="{{ route('websites.index') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>
@stop