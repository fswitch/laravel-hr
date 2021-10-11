@extends('layouts.main')


@section('content')
<div class="modal fade" id="EmployeeModal" tabindex="-1" role="dialog" aria-labelledby="EmployeeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="EmployeeModalTitle">Employee</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn1" data-dismiss="modal"></button>
            <button type="button" class="btn btn-primary btn2"></button>
            </div>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <div class="px-4 py-3">
        @can( 'create', [ App\Models\Employee::class ] )<a href="{{ route('employee.create') }}" class="btn btn-sm btn-secondary float-right">Add employee</a>@endcan
        @if ( $parent_id > 0 && $parent->subs < 1 )
            @can( 'delete', [ App\Models\Employee::class, $parent ] )
                <a href="#" data-employee="{{ $parent->id }}" class="deleteEmployee btn btn-sm btn-secondary float-right mr-3"><i class="delete far fa-trash-alt" id="deleteEmployeeFA-1"></i></a>
            @endcan
        @endif
        <h2 class="m-0">
            Employees
            @if ( $parent_id > 0 )
             of {{ $parent->full_name }}
            @endif
        </h2>
@if ( $parent_id > 0 && $parent->subs > 0 )
<form method="post" action="{{ route('employees.move', $parent['id']) }}">
    @csrf
    @method('post')
    Move employees to another manager:
    <div class="row no-gutters">
        <div class="col-md-4">
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
        <div class="col-md-6">
            <button class="btn btn-secondary" type="submit">Move</button>
        </div>
    </div>
</form>
@endif
        <div class="mt-3">
            <table class="table table-bordered yajra-datatable">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Date of employment</th>
                        <th>Phone number</th>
                        <th>Email</th>
                        <th>Salary</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection

@section('script')

$(function () {

        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [],
            aaSorting: [],
            ajax: "{{ route('employees.index', ['parent_id'=>$parent_id]) }}",
            columns: [
                {
                    data: 'filename_thumb_uri',
                    name: 'filename_thumb_uri',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta){
                        if (typeof data !== 'undefined' && data.length > 0){
                            return '<img src="'+data+'" width="100">';
                        } else {
                            return '';
                        }
                    }
                },
                {data: 'full_name', name: 'full_name', orderable: true, searchable: true},
                {data: 'position.title', name: 'position.title', orderable: true, searchable: false},
                {
                    /*data: 'timestamp_start', name: 'timestamp_start', orderable: false, searchable: false,
                    render: function (data, type, row, meta) {
                        return moment.unix(data).format('DD.MM.YYYY');
                    }*/
                    data: 'date_start_show', name: 'timestamp_start', orderable: true, searchable: false
                },
                {data: 'phone', name: 'phone', orderable: true, searchable: true},
                {data: 'email', name: 'email', orderable: true, searchable: true},
                {data: 'salary', name: 'salary', orderable: true, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            fnDrawCallback: function(){
                $.each($('.deleteEmployee'),function(i,data){
                    $(data).on('click',function(){
                        EmployeeDelete($(this).data('employee'));
                    });
                });
            }
        });

        function EmployeeDelete(id){
            var url = '{{ route('employee.delete', ':id') }}';
            $('#deleteEmployeeFA-'+id)
                .removeClass('far fa-trash-alt')
                .addClass('fas fa-spinner fa-spin');
            axios.get(url.replace(':id', id), {})
                .then(function (response) {
                    $('#EmployeeModal .modal-body').html(response.data['message']);
                    if (typeof response.data['btn1'] !== 'undefined' && response.data['btn1']>0){
                        $('#EmployeeModal .btn1').show();
                    } else {
                        $('#EmployeeModal .btn1').hide();
                    }
                    if (typeof response.data['btn2'] !== 'undefined' && response.data['btn2']>0){
                        $('#EmployeeModal .btn2').show();
                    } else {
                        $('#EmployeeModal .btn2').hide();
                    }

                    $('#EmployeeModal .btn1').html(response.data['btn1_title']);
                    $('#EmployeeModal .btn2').html(response.data['btn2_title']);
                    if (typeof response.data['success'] !== 'undefined' && response.data['success'] > 0){
                        $('#EmployeeModal').modal({});
                        $('#EmployeeModal .btn2').on('click',function(){
                            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
                            var url = '{{ route('employee.destroy', ':id') }}';
                            axios.get(url.replace(':id', id), {
                                    'headers': {
                                        'Accept':'application/json'
                                    }
                                })
                                .then(function (response1) {
                                    if (typeof response1.data['success'] !== 'undefined' && response1.data['success'] > 0){
                                        location.href = '{{route('employees.index')}}';
                                    } else {
                                        alert('Error (no success). Check console.');
                                        $('#EmployeeModal .btn2').html(response.data['btn2_title']);
                                        console.log(response1);
                                    }
                                })
                                .catch(function (error) {
                                    if ( typeof error.response['message'] !== 'undefined' ){
                                        $('#EmployeeModal .btn1').show();
                                        $('#EmployeeModal .btn2').hide();
                                        $('#EmployeeModal .modal-body').html(error.response['message']);
                                        $('#EmployeeModal .btn2').html(response.data['btn2_title']);
                                        $('#EmployeeModal').modal({});
                                    } else {
                                        $('#EmployeeModal .btn2').html(response.data['btn2_title']);
                                        alert('Error (catch). Check console.');
                                        console.log(error.response);
                                    }
                                });
                        });
                    } else {
                        if ( typeof response.data['message'] !== 'undefined' ){
                            $('#EmployeeModal .btn1').show();
                            $('#EmployeeModal .btn2').hide();
                            $('#EmployeeModal .modal-body').html(response.data['message']);
                            $('#EmployeeModal .btn2').html(response.data['btn2_title']);
                            $('#EmployeeModal').modal({});
                        } else {
                            alert('Error. Check console.');
                            console.log(response);
                        }
                    }
                    $('#EmployeeModal').on('hidden.bs.modal', function () {
                        $('#deleteEmployeeFA-'+id)
                            .removeClass('fas fa-spinner fa-spin')
                            .addClass('far fa-trash-alt');
                    })
                })
                .catch(function (error) {
                    alert('Error. Check console.');
                    console.log(error);
                });

        }

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

});
@endsection
