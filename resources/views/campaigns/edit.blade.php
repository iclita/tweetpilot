@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
    <div class="card-title ">
        <div class="container text-center">
            <h3>Edit Campaign</h3>
        </div>
    </div>
    <div class="container">
        @if ($errors->count() > 0)
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <p>Website: {{ $campaign->website->url }}</p>
        <div class="card-panel">
            {!! Form::model($campaign, ['route' => ['campaigns.update', $campaign->id]]) !!}
                {{ method_field('PATCH') }}
                <div class="form-group">
                    <label for="campaign-type">Type</label>
                    {{ Form::select('type', ['post'=>'Post', 'like'=>'Like', 'retweet'=>'Retweet'], null, ['id'=>'campaign-type', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="campaign-custom-message">Custom Message</label>
                    {{ Form::text('custom_message', null, ['class'=>'form-control', 'id'=>'campaign-custom-message', 'placeholder'=>'Ex.: Check out this new product!']) }}
                </div>
                <div class="form-group">
                    <label for="campaign-custom-link">Custom Link</label>
                    {{ Form::text('custom_link', null, ['class'=>'form-control', 'id'=>'campaign-custom-link', 'placeholder'=>'Ex.: www.super-product.com?id=15']) }}
                </div>
                <div class="form-group">
                    <label for="campaign-post-id">Post ID</label>
                    {{ Form::text('post_id', null, ['class'=>'form-control', 'id'=>'campaign-post-id', 'placeholder'=>'829423543985307667']) }}
                </div>
                <button style="float:right;" type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:left;" href="{{ route('campaigns.index') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>
@stop