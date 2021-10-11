@extends('layouts.main')


@section('content')

<div class="content-wrapper">
    <div class="px-4 py-3">
        <h2 class="m-0">Positions</h2>

        <div class="col-md-8 p-0 mt-3">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title m-0">Add position</h3>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('position.store') }}" method="post">
                        @csrf
                        @method('POST')
                        <label for="position_name">Name</label>
                        <input class="form-control @error('title')
                        alert-danger
                        @enderror" id="position_name" name="title" pattern=".{2,256}" maxlength="256" value="@error('title'){{ old('title') }}@enderror">
                        @error('title')
                        <div class="text-danger p-2">
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="mb-3 mt-2 text-right">
                            <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                            <button class="btn btn-sm btn-secondary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->

            <!-- /.card -->
          </div>

    </div>

</div>
@endsection


@section('script')

$(function () {

    $('.form-control').maxlength({
        alwaysShow: true,
        validate: false,
        allowOverMax: true,
        customMaxAttribute: "90"
    });

});
@endsection
