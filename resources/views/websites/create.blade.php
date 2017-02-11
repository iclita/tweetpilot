@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
    <div class="card-title">
        <div class="container text-center">
            <h3>Create Website</h3>
        </div>
    </div>
    <div class="container">
        <div class="card-panel">
            <form method="POST" action="{{ route('websites.store') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="website-name">Name</label>
                    <input type="text" name="name" class="form-control" id="website-name" placeholder="Ex.: My Website" required />
                </div>
                <div class="form-group">
                    <label for="website-description">Description</label>
                    <input type="text" name="description" class="form-control" id="website-description" placeholder="Ex.: My Website is awesome!" required />
                </div>
                <div class="form-group">
                    <label for="website-name">URL</label>
                    <input type="text" name="url" class="form-control" id="website-name" placeholder="Ex.: mywebsite.com" required />
                </div>
                <div class="form-group">
                    <label for="website-app-key">App Key</label>
                    <input type="text" name="app_key" class="form-control" id="website-app-key" placeholder="Website app key" required />
                </div>
                <div class="form-group">
                    <label for="website-app-secret">App Secret</label>
                    <input type="text" name="app_secret" class="form-control" id="website-app-secret" placeholder="Website app secret" required />
                </div>
                <button style="float:right;" type="submit" class="btn btn-info"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:left;" href="{{ route('websites.index') }}" class="btn btn-warning"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>
@stop
