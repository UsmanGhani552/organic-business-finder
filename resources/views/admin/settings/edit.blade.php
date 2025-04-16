@extends('admin.layout.master')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Update Settings</h4>
        </div>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @foreach($settings as $setting)
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label text-capitalize">{{ str_replace('_', ' ', $setting->key) }}</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary">Update Settings</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
@endsection
