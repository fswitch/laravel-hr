@extends('layouts.main')


@section('content')

<div class="content-wrapper">
    <div class="px-4 py-3">
        <h2 class="m-0">Positions</h2>

        <div class="col-md-8 p-0 mt-3">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title m-0">Move employees</h3>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('position.moveemployees',$position->id) }}" method="post">
                        @csrf
                        @method('POST')

                        @if ($message)<div class="alert alert-info alert-dismissable">
                            {{ $message }}
                        </div>
                        @endif
                        <div class="form-group">

                            <p>
                                Move all employees with position "{{ $position->title }}" to
                            </p>

                            <label for="select-position">Position</label><br>
                            <select id="position_select" name="position_id" class="selectpicker" data-live-search="true">

                            </select>
                            @error('position')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3 mt-2 text-right">
                            <a href="{{ route('positions.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
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

    $('#position_select').selectpicker({liverSearch: true})
    .ajaxSelectPicker({
        ajax: {
            url: '{{route('position.find')}}',
            type:'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                find: '@{{{q}}}'
            }
        }
    });

});
@endsection
