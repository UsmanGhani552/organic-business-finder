@extends('admin.layout.master')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group float-right">
                    <a type="button" href="{{ route('admin.membership.index') }}"
                        class="btn btn-primary waves-effect waves-light text-white"><i
                            class="fas fa-plus-square mr-2"></i>Back</a>
                </div>
                <h4 class="page-title">Edit membership</h4>
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
                            <form action="{{ route('admin.membership.update', $membership->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Product ID</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="product_id" id="example-text-input"
                                            value="{{ $membership->product_id }}">
                                        @error('product_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                                   
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="name" id="example-text-input"
                                            value="{{ $membership->name }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Price</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="price" id="example-text-input"
                                            value="{{ $membership->price }}">
                                        @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Description</label>
                                    <div class="add-description col-sm-8">
                                        @foreach ($membership->description as $key => $desc)
                                            <div class="d-flex gap-2 {{ $key != 0 ? 'mt-2' : '' }}">
                                                <input type="text" class="form-control" name="description[]"
                                                    placeholder="Enter point" value="{{ $desc }}">
                                                @if ($key != 0)
                                                    <button type="button" class="btn btn-danger btn-sm ml-3"
                                                        onclick="this.closest('div').remove()">❌</button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-sm-2 text-right">
                                        <button type="button" class="btn btn-success btn-sm" onclick="addDescription()">+
                                            Add</button>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <div>
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light">Submit</button>
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
@push('scripts')
    <script>
        function addDescription(value = '') {
            const $container = $('.add-description'); // jQuery selector
            const $div = $(`
                        <div class="d-flex gap-2 mt-2">
                            <input type="text" class="form-control" name="description[]" value="${value}" placeholder="Enter point">
                            <button type="button" class="btn btn-danger btn-sm ml-3">❌</button>
                        </div>
                    `);

            // Attach delete function
            $div.find('button').on('click', function () {
                $(this).closest('div').remove();
            });

            $container.append($div);
        }


    </script>

@endpush