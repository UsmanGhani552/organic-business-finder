@extends('admin.layout.master')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <a type="button" href="{{ route('admin.category.index') }}" class="btn btn-primary waves-effect waves-light text-white"><i class="fas fa-plus-square mr-2"></i>Back</a>
            </div>
            <h4 class="page-title">Create Category</h4>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12">
                        <form action="{{ route('admin.category.update',$category->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="name" value="{{ $category->name }}" id="example-text-input">
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Image</label>
                                <div class="col-sm-10">
                                    <input type="file" id="input-file-now" name="icon" class="dropify" data-default-file="{{ asset('images/farm/category/' . $category->icon) }}" />
                                    {{-- <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01" name="image">
                                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                      </div> --}}
                                      @error('icon')
                                      <span class="text-danger">{{ $message }}</span>
                                      @enderror
                                </div>
                            </div>


                            <div class="form-group mb-0">
                                <div>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                    <button type="reset" class="btn btn-secondary waves-effect m-l-5">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection
