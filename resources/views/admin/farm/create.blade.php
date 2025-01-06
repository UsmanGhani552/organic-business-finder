@extends('admin.layout.master')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <a type="button" href="{{ route('admin.user.index') }}" class="btn btn-primary waves-effect waves-light text-white"><i class="fas fa-plus-square mr-2"></i>Back</a>
            </div>
            <h4 class="page-title">Create User</h4>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <strong>{{ $message }}</strong>
            </div>
            @endif
            <div class="card-body">
                <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">Name</label>
                                <input class="form-control" type="text" name="name" id="example-text-input">
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">Email</label>
                                <input class="form-control" type="text" name="email" id="example-text-input">
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">Password</label>
                                <input class="form-control" type="password" name="password" id="example-text-input">
                                @error('password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">Confirm Password</label>
                                <input class="form-control" type="password" name="password_confirmation" id="example-text-input">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">Type</label>
                                {{-- <input class="form-control" type="text" name="name" id="example-text-input"> --}}
                                <select class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;" name="type">
                                    <option class="disabled" disabled selected>Select</option>
                                    <option value="visitor">Visitor</option>
                                    <option value="farmer">Farmer</option>
                                </select>
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                        </div>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="example-text-input" class="col-form-label">Image</label>
                                <input type="file" id="input-file-now" name="image" class="dropify" />
                                @error('image')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="form-group mb-0 float-right">
                        <div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                            <button type="reset" class="btn btn-secondary waves-effect m-l-5">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection
