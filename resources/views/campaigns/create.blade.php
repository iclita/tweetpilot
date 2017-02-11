@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
    <div class="card-title">
        <div class="container text-center">
            <h3>Create Campaign</h3>
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
        <div class="card-panel">
            <form method="POST" action="{{ route('campaigns.store') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="campaign-website">Website</label>
                    {{ Form::select('website', $websites, ['id'=>'campaign-website', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="campaign-type">Type</label>
                    {{ Form::select('type', ['post'=>'Post', 'like'=>'Like', 'retweet'=>'Retweet'], 'post', ['id'=>'campaign-type', 'required']) }}
                </div>
                <div class="form-group">
                    <label for="campaign-custom-message">Custom Message</label>
                    <input type="text" name="custom_message" value="{{ old('custom_message') }}" class="form-control" id="campaign-custom-message" placeholder="Ex.: Check out this new product!" />
                </div>
                <div class="form-group">
                    <label for="campaign-custom-link">Custom Link</label>
                    <input type="text" name="custom_link" value="{{ old('custom_link') }}" class="form-control" id="campaign-custom-link" placeholder="Ex.: www.super-product.com?id=15" />
                </div>
                <div class="form-group">
                    <label for="campaign-post-id">Post ID</label>
                    <input type="text" name="post_id" value="{{ old('post_id') }}" class="form-control" id="campaign-post-id" placeholder="Ex.: 829423543985307667" />
                </div>
                <button style="float:right;" type="submit" class="btn btn-info"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:left;" href="{{ route('campaigns.index') }}" class="btn btn-warning"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>
@stop
