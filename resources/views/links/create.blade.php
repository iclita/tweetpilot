@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }}</title>
@stop

@section('content')
    <div class="card-title">
        <div class="container text-center">
            <h3>Create Link</h3>
        </div>
    </div>
    <div class="container">
        <div class="card-panel">
            <form method="POST" action="{{ route('links.store') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="link-description">Description</label>
                    <input type="text" name="description" class="form-control" id="link-description" placeholder="Ex.: Buy now 50% off..." required />
                </div>
                <div class="form-group">
                    <label for="link-url">URL</label>
                    <input type="url" name="url" class="form-control" id="link-url" placeholder="https://www.mystore.com?product_id=15" required />
                </div>
                <button style="float:right;" type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                <a style="float:left;" href="{{ route('links.index') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            </form>
        </div>
    </div>
@stop
