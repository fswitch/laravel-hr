@extends('layouts.main')


@section('content')

<div class="content-wrapper">
    <div class="px-4 py-3">
        <h2 class="m-0">Positions</h2>

        <div class="col-md-8 p-0 mt-3">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title m-0">Position edit</h3>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('position.update', $position->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <label for="position_name">Name</label>
                        <input class="form-control @error('title')
                        alert-danger
                        @enderror" id="position_name" name="title" pattern=".{2,256}" maxlength="256" value="@error('title'){{ old('title') }}@else{{ $position->title }}@enderror">
                        @error('title')
                        <div class="text-danger p-2">
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="content my-3">
                            <div class="row">
                                <div class="col-sm-6">Created at: {{ $position->created_at }}</div>
                                <div class="col-sm-6">Admin created ID: {{ $position->admin_created_id }}</div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">Updated at: {{ $position->updated_at }}</div>
                                <div class="col-sm-6">Admin updated ID: {{ $position->admin_updated_id }}</div>
                            </div>
                        </div>

                        <div class="mb-3 text-right">
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
