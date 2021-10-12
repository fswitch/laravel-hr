@extends('layouts.main')


@section('content')
@if (isset($employee->id) && $employee->id>0)
    @php $edit=1; @endphp
@else
    @php $edit=0; @endphp
@endif
<div class="content-wrapper">
    <div class="px-4 py-3">
        <h2 class="m-0">Employees</h2>

        <div class="col-md-8 p-0 mt-3">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title m-0">Add employee</h3>
                    </div>
                </div>

                <div class="card-body">
                    <form action="@if ($edit>0){{ route('employee.update', $employee->id) }}@else{{ route('employee.store') }}@endif" method="post" enctype="multipart/form-data">
                        @csrf
                        @if ($edit>0)
                        @method('PUT')
                        <input type="text" hidden name="id" value="{{ $employee->id }}">
                        @else
                        @method('POST')
                        @endif

                        <div class="form-group">
                            @if ($edit>0 && $employee->has_photo>0)<img src="{{ $employee->filename_thumb_uri }}" width="100px"><br>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" value="1" id="photo_delete" name="photo_delete">
                                <label class="form-check-label" for="photo_delete">Delete photo</label>
                            </div>
                            @endif
                            <label class="btn @error('photo')
                            btn-outline-danger
                            @else
                            btn-outline-secondary
                            @enderror">
                                Photo <input type="file" hidden name="photo" id="photo">
                            </label>
                            <span id="photo_filename">{!! htmlentities(Session::get('photo_orig'),ENT_QUOTES,'UTF-8'); !!}</span>
                            <p class="text-muted small form-text">
                                File format jpg, png up to 5MB, the minimum size of 300x300px
                            </p>
                            @error('photo')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="full_name">Name</label>
                            <input class="form-control @error('full_name')
                            is-invalid
                            @enderror" id="full_name" name="full_name" pattern=".{2,256}" maxlength="256" value="{{old('full_name',$employee->full_name) }}">
                            @error('full_name')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="phone">Phone</label>
                            <input class="form-control @error('phone')
                            is-invalid
                            @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                            <p class="text-muted small form-text text-right">
                                Required format +380 (xx) XXX XX XX
                            </p>
                            @error('phone')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="email">Email</label>
                            <input class="form-control @error('email')
                            is-invalid
                            @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}">
                            @error('email')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="select-position">Position</label><br>
                            <select id="position_select" name="position_id" class="selectpicker" data-live-search="true">

                            </select>
                            @error('position_id')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="salary">Salary</label>
                            <input type="number" min="0.0" max="500.0" step="0.001" name="salary" id="salary" class="form-control @error('salary')
                            is-invalid
                            @enderror" value="{{ old('salary', $employee->salary) }}">
                            @error('salary')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="parent">Head</label>
                            <input type="hidden" name="parent_id" id="parent_id" value="{{ old('parent_id') }}">
                            <input type="text" name="parent" id="parent" class="form-control complex @error('parent_id')
                            is-invalid
                            @enderror" autocomplete="off">
                            @error('parent_id')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="date_start">Date of employment</label>
                            <input type="text" id="date_start" name="date_start" value="{{ old('date_start', $employee->date_start_show) }}" class="form-control datepicker @error('date_start')
                            is-invalid
                            @enderror">
                            @error('date_start')
                            <div class="text-danger p-2">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3 mt-2 text-right">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('employees.index') }}">Cancel</a>
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

    $('#photo').on('change', function () {
        let filename = this.value;
        filename = filename.split(/[\\/]/).pop();
        $('#photo_filename').html(filename);
    });

    $('#full_name').maxlength({
        alwaysShow: true,
        validate: false,
        allowOverMax: true,
        customMaxAttribute: "90"
    });

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

    @if (old('position_id')>0 || $edit>0)
    $.ajax({
        url: '{{route('position.find')}}',
        type:'POST',
        dataType: 'json',
        data: {
            "_token": "{{ csrf_token() }}",
            find: '',
            id: '{{ old('position_id',$employee->position_id) }}'
         }
    })
    .done(function(res){
        if ( typeof res[0] !== 'undefined' ){
            $('#position_select').html('<option class="bs-title-option" value="'+res[0]['value']+'">'+res[0]['text']+'</option><optgroup label="Currently Selected"><option value="'+res[0]['value']+'" title="'+res[0]['text']+'" selected="selected">'+res[0]['text']+'</option></optgroup>');
            $('.filter-option-inner-inner').html(res[0]['text']);
        }
    });
    @endif

    $('.form-control').on('input',function(){
        $(this).removeClass('is-invalid').next('div.text-danger').remove();
    });

    $('#parent').autoComplete({
        resolver: 'custom',
        preventEnter: true,
        events: {
            search: function(find, callback){
                $('#parent_id').attr('value', 0);
                $.ajax(
                    '{{route('employee.find')}}',
                    {
                        type: 'POST',
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'find': find
                        }
                    }
                )
                .done(function(res){
                    callback(res.results);
                });
            }
        }
    });
    $('#parent').on('autocomplete.select', function(evt, item) {
        $('#parent').attr('value',item.id);
        $('#parent_id').attr('value',item.id);
    });

    @if (old('parent_id')>0 || $employee->parent_id)
    $.ajax(
        '{{route('employee.find')}}',
        {
            type: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': {{ old('parent_id',$employee->parent_id) }}
            }
        }
    ).done(function(res){
        if ( typeof res.results[0] !== 'undefined' ){
            $('#parent').autoComplete('set', { value: res.results[0]['id'], text: res.results[0]['text'] });
            $('#parent_id').attr('value',res.results[0]['id']);
        }
    });
    @endif

    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true
    });
});
@endsection
